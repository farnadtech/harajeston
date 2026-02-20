<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PlaceBidRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->role === 'buyer';
    }

    public function rules(): array
    {
        $listing = $this->route('listing');
        
        return [
            'amount' => [
                'required',
                'numeric',
                'min:' . ($listing->current_highest_bid ?? $listing->base_price),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'amount.required' => 'مبلغ پیشنهاد الزامی است.',
            'amount.numeric' => 'مبلغ پیشنهاد باید عدد باشد.',
            'amount.min' => 'مبلغ پیشنهاد باید بیشتر از بالاترین پیشنهاد فعلی باشد.',
        ];
    }
}
