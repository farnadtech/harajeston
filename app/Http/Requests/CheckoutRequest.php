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
            'shipping_city' => ['required', 'string', 'max:100'],
            'shipping_postal_code' => ['required', 'string', 'size:10'],
            'shipping_phone' => ['required', 'string', 'regex:/^09\d{9}$/'],
            'shipping_method_id' => ['required', 'exists:shipping_methods,id'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'shipping_address.required' => 'آدرس ارسال الزامی است.',
            'shipping_address.max' => 'آدرس ارسال نباید بیشتر از 500 کاراکتر باشد.',
            'shipping_city.required' => 'شهر الزامی است.',
            'shipping_city.max' => 'نام شهر نباید بیشتر از 100 کاراکتر باشد.',
            'shipping_postal_code.required' => 'کد پستی الزامی است.',
            'shipping_postal_code.size' => 'کد پستی باید 10 رقم باشد.',
            'shipping_phone.required' => 'شماره تماس الزامی است.',
            'shipping_phone.regex' => 'شماره تماس باید با 09 شروع شود و 11 رقم باشد.',
            'shipping_method_id.required' => 'انتخاب روش ارسال الزامی است.',
            'shipping_method_id.exists' => 'روش ارسال انتخاب شده نامعتبر است.',
            'notes.max' => 'یادداشت نباید بیشتر از 1000 کاراکتر باشد.',
        ];
    }
}
