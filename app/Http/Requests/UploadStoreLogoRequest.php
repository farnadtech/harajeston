<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadStoreLogoRequest extends FormRequest
{
    public function authorize(): bool
    {
        $store = $this->route('store');
        return $this->user()->id === $store->user_id;
    }

    public function rules(): array
    {
        return [
            'logo' => [
                'required',
                'image',
                'mimes:jpeg,png,jpg,webp',
                'max:1024', // 1MB
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'logo.required' => 'تصویر لوگو الزامی است.',
            'logo.image' => 'فایل باید تصویر باشد.',
            'logo.mimes' => 'فرمت تصویر باید JPG، PNG یا WebP باشد.',
            'logo.max' => 'حجم تصویر نباید بیشتر از 1 مگابایت باشد.',
        ];
    }
}
