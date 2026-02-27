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
                    
                    // Check wallet balance only for first bid (deposit requirement)
                    $user = auth()->user();
                    $wallet = $user->wallet;
                    $balance = $wallet ? $wallet->balance : 0;
                    
                    // Check if user has already bid in this auction
                    $userHasBid = $listing->bids()->where('user_id', $user->id)->exists();
                    
                    // Only check balance for first bid (when deposit needs to be blocked)
                    if (!$userHasBid) {
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
                        
                        // For first bid, only check if user has enough for deposit
                        if ($depositAmount > 0 && $balance < $depositAmount) {
                            $persianDeposit = \App\Services\PersianNumberService::convertToPersian(number_format($depositAmount));
                            $persianBalance = \App\Services\PersianNumberService::convertToPersian(number_format($balance));
                            $validator->errors()->add('amount', 'موجودی کیف پول شما (' . $persianBalance . ' تومان) برای پرداخت سپرده (' . $persianDeposit . ' تومان) کافی نیست.');
                        }
                    }
                    // For subsequent bids, no balance check needed (deposit already blocked)
                }
            }
        });
    }
}
