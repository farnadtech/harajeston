<?php

namespace App\Services;

use App\Models\NotificationSetting;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotificationDispatcher
{
    public function __construct(protected SmsService $sms) {}

    /**
     * Dispatch notification for an event to a user
     */
    public function dispatch(string $eventKey, User $user, array $data = []): void
    {
        $setting = NotificationSetting::forEvent($eventKey);
        if (!$setting) return;

        // Database notification is handled by the caller (Laravel Notification)
        // Here we handle SMS and Email

        if ($setting->via_sms && $user->phone) {
            $this->sendSms($setting, $user, $data);
        }

        if ($setting->via_email && $user->email) {
            $this->sendEmail($setting, $user, $data);
        }
    }

    protected function sendSms(NotificationSetting $setting, User $user, array $data): void
    {
        try {
            if ($setting->sms_pattern_id) {
                // Send via Melipayamak pattern
                $this->sms->sendByPatternId(
                    $user->phone,
                    $setting->sms_pattern_id,
                    $data
                );
            } elseif ($setting->sms_template) {
                // Send plain text SMS
                $text = $this->renderTemplate($setting->sms_template, $data);
                $this->sms->sendText($user->phone, $text);
            }
        } catch (\Throwable $e) {
            Log::error("NotificationDispatcher SMS failed [{$setting->event_key}]", [
                'user' => $user->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    protected function sendEmail(NotificationSetting $setting, User $user, array $data): void
    {
        try {
            // Use saved email_subject/body if set, otherwise fallback to data
            $subject = $setting->email_subject ?: ($data['title'] ?? $setting->event_label);
            $rawBody = $setting->email_body ?: ($data['message'] ?? $setting->event_label);

            // Replace variables in subject and body
            $vars = [
                '{order_number}'    => $data['order_number']    ?? '',
                '{buyer_name}'      => $data['buyer_name']      ?? '',
                '{seller_name}'     => $data['seller_name']     ?? '',
                '{amount}'          => $data['amount']          ?? '',
                '{listing_title}'   => $data['listing_title']   ?? '',
                '{tracking_number}' => $data['tracking_number'] ?? '',
            ];
            $subject = str_replace(array_keys($vars), array_values($vars), $subject);
            $body    = str_replace(array_keys($vars), array_values($vars), $rawBody);

            $siteName = config('app.name', 'سایت');
            $siteUrl  = config('app.url', '');

            Mail::send('emails.notification',
                compact('subject', 'body', 'siteName', 'siteUrl'),
                function ($m) use ($user, $subject) {
                    $m->to($user->email)->subject($subject);
                }
            );
        } catch (\Throwable $e) {
            Log::error("NotificationDispatcher Email failed [{$setting->event_key}]", [
                'user'  => $user->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    protected function renderTemplate(string $template, array $data): string
    {
        foreach ($data as $key => $value) {
            $template = str_replace('{' . $key . '}', $value, $template);
        }
        return $template;
    }
}
