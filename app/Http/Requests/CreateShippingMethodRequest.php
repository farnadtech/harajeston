<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateShippingMethodRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'base_cost' => ['required', 'numeric', 'min:0'],
            'is_active' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'نام روش ارسال الزامی است.',
            'name.max' => 'نام روش ارسال نباید بیشتر از 255 کاراکتر باشد.',
            'description.max' => 'توضیحات نباید بیشتر از 1000 کاراکتر باشد.',
            'base_cost.required' => 'هزینه پایه الزامی است.',
            'base_cost.numeric' => 'هزینه پایه باید عدد باشد.',
            'base_cost.min' => 'هزینه پایه نمی‌تواند منفی باشد.',
        ];
    }
}
