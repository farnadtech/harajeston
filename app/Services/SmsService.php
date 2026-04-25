<?php

namespace App\Services;

use App\Models\MelipayamakSetting;
use Illuminate\Support\Facades\Log;

class SmsService
{
    protected ?MelipayamakSetting $settings = null;
    protected bool $loaded = false;

    const OTP_REST_URL  = 'https://rest.payamak-panel.com/api/SendSMS/SendOtp';
    const SEND_REST_URL = 'https://rest.payamak-panel.com/api/SendSMS/SendSMS';
    const SOAP_WSDL     = 'http://api.payamak-panel.com/post/Send.asmx?wsdl';

    public function __construct() {}

    protected function settings(): ?MelipayamakSetting
    {
        if (!$this->loaded) {
            try {
                $this->settings = MelipayamakSetting::get();
            } catch (\Throwable $e) {
                $this->settings = null;
            }
            $this->loaded = true;
        }
        return $this->settings;
    }

    public function isConfigured(): bool
    {
        return $this->settings()?->isConfigured() ?? false;
    }

    public function sendOtp(string $phone, string $code): bool
    {
        $result = $this->sendOtpWithLog($phone, $code);
        return $result['success'];
    }

    public function sendOtpWithLog(string $phone, string $code): array
    {
        if (!$this->isConfigured()) {
            return ['success' => false, 'message' => 'SMS not configured.', 'response' => ''];
        }
        if (!empty($this->settings()->body_id)) {
            return $this->sendByPattern($phone, $code);
        }
        return $this->sendViaSoapOtp2($phone, $code);
    }

    protected function sendByPattern(string $phone, string $code): array
    {
        try {
            ini_set('soap.wsdl_cache_enabled', '0');
            $client = new \SoapClient(self::SOAP_WSDL, ['exceptions' => true]);
            $result = $client->SendByBaseNumber2([
                'username' => $this->settings()->username,
                'password' => $this->settings()->getAuthPassword(),
                'text'     => $code,
                'to'       => $phone,
                'bodyId'   => (int)$this->settings()->body_id,
            ]);
            $recId   = (string)($result->SendByBaseNumber2Result ?? '0');
            $success = strlen($recId) > 5 && (int)$recId > 0;
            Log::info('SmsService::sendByPattern', ['phone' => $phone, 'success' => $success, 'recId' => $recId]);
            return ['success' => $success, 'message' => $success ? "OTP sent (RecId: {$recId})" : "Error: {$recId}", 'response' => "SendByBaseNumber2Result: {$recId}"];
        } catch (\Throwable $e) {
            Log::warning('SmsService: Pattern failed, fallback to SendOtp2', ['error' => $e->getMessage()]);
            return $this->sendViaSoapOtp2($phone, $code);
        }
    }

    protected function sendViaSoapOtp2(string $phone, string $code): array
    {
        try {
            ini_set('soap.wsdl_cache_enabled', '0');
            $client = new \SoapClient(self::SOAP_WSDL, ['exceptions' => true]);
            $result = $client->SendOtp2([
                'username' => $this->settings()->username,
                'password' => $this->settings()->getAuthPassword(),
                'to'       => $phone,
                'from'     => $this->settings()->from_number ?: '',
                'code'     => $code,
            ]);
            $recId   = (string)($result->SendOtp2Result ?? '0');
            $success = strlen($recId) > 10;
            Log::info('SmsService::sendOtp2', ['phone' => $phone, 'success' => $success, 'recId' => $recId]);
            return ['success' => $success, 'message' => $success ? "OTP sent (RecId: {$recId})" : "Error: {$recId}", 'response' => "SendOtp2Result: {$recId}"];
        } catch (\Throwable $e) {
            Log::warning('SmsService: SOAP failed, fallback to REST', ['error' => $e->getMessage()]);
            return $this->sendOtpViaRest($phone, $code);
        }
    }

    protected function sendOtpViaRest(string $phone, string $code): array
    {
        $data = ['username' => $this->settings()->username, 'password' => $this->settings()->getAuthPassword(), 'to' => $phone, 'from' => $this->settings()->from_number ?: '', 'code' => $code];
        try {
            $result  = $this->postJson(self::OTP_REST_URL, $data);
            $value   = (string)($result['Value'] ?? '0');
            $status  = (int)($result['RetStatus'] ?? 0);
            $success = $status === 1 && strlen($value) > 10;
            return ['success' => $success, 'message' => $success ? "OTP sent via REST (RecId: {$value})" : "Error: {$value}", 'response' => json_encode($result, JSON_UNESCAPED_UNICODE)];
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => $e->getMessage(), 'response' => ''];
        }
    }

    public function send(string $phone, string $text): array
    {
        if (!$this->isConfigured()) {
            return ['success' => false, 'message' => 'SMS not configured.', 'response' => ''];
        }
        $data = ['username' => $this->settings()->username, 'password' => $this->settings()->getAuthPassword(), 'to' => $phone, 'from' => $this->settings()->from_number ?: '', 'text' => $text, 'isFlash' => false];
        try {
            $result  = $this->postJson(self::SEND_REST_URL, $data);
            $value   = (string)($result['Value'] ?? '0');
            $status  = (int)($result['RetStatus'] ?? 0);
            $success = $status === 1 && strlen($value) > 5;
            $message = $success ? "SMS sent (RecId: {$value})" : "Error: {$value}";
            Log::info('SmsService::send', ['phone' => $phone, 'success' => $success]);
            return ['success' => $success, 'message' => $message, 'response' => json_encode($result, JSON_UNESCAPED_UNICODE)];
        } catch (\Throwable $e) {
            return ['success' => false, 'message' => $e->getMessage(), 'response' => ''];
        }
    }

    protected function postJson(string $url, array $data): array
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_POST => true, CURLOPT_POSTFIELDS => json_encode($data), CURLOPT_HTTPHEADER => ['Content-Type: application/json'], CURLOPT_SSL_VERIFYPEER => false, CURLOPT_SSL_VERIFYHOST => false, CURLOPT_TIMEOUT => 15]);
        $response = curl_exec($ch);
        $error    = curl_error($ch);
        curl_close($ch);
        if ($error) { throw new \RuntimeException($error); }
        return json_decode($response, true) ?? [];
    }

    /**
     * Send SMS via a specific pattern ID (for notification events)
     */
    public function sendByPatternId(string $phone, string $patternId, array $params = []): bool
    {
        if (!$this->isConfigured()) return false;
        try {
            ini_set('soap.wsdl_cache_enabled', '0');
            $client = new \SoapClient(self::SOAP_WSDL, ['exceptions' => true]);
            // Build text from params (first value used as text)
            $text = implode(',', array_values($params));
            $result = $client->SendByBaseNumber2([
                'username' => $this->settings()->username,
                'password' => $this->settings()->getAuthPassword(),
                'text'     => $text,
                'to'       => $phone,
                'bodyId'   => (int)$patternId,
            ]);
            $recId = (string)($result->SendByBaseNumber2Result ?? '0');
            $success = strlen($recId) > 5 && (int)$recId > 0;
            Log::info('SmsService::sendByPatternId', ['phone' => $phone, 'patternId' => $patternId, 'success' => $success]);
            return $success;
        } catch (\Throwable $e) {
            Log::error('SmsService::sendByPatternId failed', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Send plain text SMS
     */
    public function sendText(string $phone, string $text): bool
    {
        if (!$this->isConfigured()) return false;
        try {
            $data = [
                'UserName' => $this->settings()->username,
                'Password' => $this->settings()->getAuthPassword(),
                'To'       => $phone,
                'From'     => $this->settings()->from ?? '',
                'Text'     => $text,
                'IsFlash'  => false,
            ];
            $response = $this->httpPost(self::SEND_REST_URL, $data);
            $result = json_decode($response, true);
            $success = isset($result['Value']) && (int)$result['Value'] > 0;
            Log::info('SmsService::sendText', ['phone' => $phone, 'success' => $success]);
            return $success;
        } catch (\Throwable $e) {
            Log::error('SmsService::sendText failed', ['error' => $e->getMessage()]);
            return false;
        }
    }
}