<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PlaceBidRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'listing_id' => 'required|exists:listings,id',
            'amount' => 'required|numeric|min:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'listing_id.required' => 'شناسه حراجی الزامی است.',
            'listing_id.exists' => 'حراجی مورد نظر یافت نشد.',
            'amount.required' => 'مبلغ پیشنهاد الزامی است.',
            'amount.numeric' => 'مبلغ پیشنهاد باید عدد باشد.',
            'amount.min' => 'مبلغ پیشنهاد باید حداقل ۱۰۰۰ تومان باشد.',
        ];
    }
    
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->listing_id) {
                $listing = \App\Models\Listing::find($this->listing_id);
                
                if ($listing) {
                    $highestBid = $listing->bids()->orderBy('amount', 'desc')->first();
                    $increment = $listing->bid_increment ?? 1000;
                    $minAmount = $highestBid ? $highestBid->amount + $increment : $listing->starting_price;
                    
                    if ($this->amount < $minAmount) {
                        $persianAmount = \App\Services\PersianNumberService::convertToPersian(number_format($minAmount));
                        $validator->errors()->add('amount', 'مبلغ پیشنهاد باید حداقل ' . $persianAmount . ' تومان باشد.');
                    }
                    
                    // Check wallet balance including deposit if needed
                    $user = auth()->user();
                    $wallet = $user->wallet;
                    $balance = $wallet ? $wallet->balance : 0;
                    
                    $requiredBalance = $this->amount;
                    
                    // Get deposit from site settings
                    $depositSetting = \App\Models\SiteSetting::where('key', 'deposit_type')->first();
                    $depositType = $depositSetting ? $depositSetting->value : 'none';
                    
                    $depositAmount = 0;
                    if ($depositType === 'fixed') {
                        $fixedSetting = \App\Models\SiteSetting::where('key', 'deposit_fixed_amount')->first();
                        $depositAmount = $fixedSetting ? (int)$fixedSetting->value : 0;
                    } elseif ($depositType === 'percentage') {
                        $percentageSetting = \App\Models\SiteSetting::where('key', 'deposit_percentage')->first();
                        $percentage = $percentageSetting ? (float)$percentageSetting->value : 0;
                        $depositAmount = (int)($listing->starting_price * ($percentage / 100));
                    }
                    
                    // Add deposit to required balance if user hasn't bid yet
                    if ($depositAmount > 0) {
                        $userHasBid = $listing->bids()->where('user_id', $user->id)->exists();
                        if (!$userHasBid) {
                            $requiredBalance += $depositAmount;
                        }
                    }
                    
                    if ($balance < $requiredBalance) {
                        $persianRequired = \App\Services\PersianNumberService::convertToPersian(number_format($requiredBalance));
                        $persianBalance = \App\Services\PersianNumberService::convertToPersian(number_format($balance));
                        
                        if ($depositAmount > 0 && !$userHasBid) {
                            $persianDeposit = \App\Services\PersianNumberService::convertToPersian(number_format($depositAmount));
                            $validator->errors()->add('amount', 'موجودی کیف پول شما (' . $persianBalance . ' تومان) کافی نیست. مبلغ مورد نیاز: ' . $persianRequired . ' تومان (شامل ' . $persianDeposit . ' تومان سپرده)');
                        } else {
                            $validator->errors()->add('amount', 'موجودی کیف پول شما (' . $persianBalance . ' تومان) کافی نیست. مبلغ مورد نیاز: ' . $persianRequired . ' تومان');
                        }
                    }
                }
            }
        });
    }
}
