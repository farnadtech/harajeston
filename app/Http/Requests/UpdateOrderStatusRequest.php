<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateOrderStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        $order = $this->route('order');
        return $this->user()->id === $order->seller_id;
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
