<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateOrderStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        $order = $this->route('order');
        $user = $this->user();
        
        // Seller can update any status
        if ($user->id === $order->seller_id) {
            return true;
        }
        
        // Buyer can only update from 'shipped' to 'delivered'
        if ($user->id === $order->buyer_id && $order->status === 'shipped' && $this->status === 'delivered') {
            return true;
        }
        
        return false;
    }

    public function rules(): array
    {
        return [
            'status' => [
                'required',
                Rule::in(['pending', 'processing', 'shipped', 'delivered', 'cancelled']),
            ],
            'tracking_number' => ['nullable', 'string', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => 'وضعیت سفارش الزامی است.',
            'status.in' => 'وضعیت سفارش نامعتبر است.',
            'tracking_number.max' => 'کد رهگیری نباید بیشتر از 100 کاراکتر باشد.',
        ];
    }
}
