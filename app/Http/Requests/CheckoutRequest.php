<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'shipping_address' => ['required', 'string', 'max:500'],
            'shipping_method_id' => ['nullable', 'exists:shipping_methods,id'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'shipping_address.required' => 'آدرس ارسال الزامی است.',
            'shipping_address.max' => 'آدرس ارسال نباید بیشتر از 500 کاراکتر باشد.',
            'shipping_method_id.exists' => 'روش ارسال انتخاب شده نامعتبر است.',
            'notes.max' => 'یادداشت نباید بیشتر از 1000 کاراکتر باشد.',
        ];
    }
}
