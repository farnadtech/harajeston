<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadStoreBannerRequest extends FormRequest
{
    public function authorize(): bool
    {
        $store = $this->route('store');
        return $this->user()->id === $store->user_id;
    }

    public function rules(): array
    {
        return [
            'banner' => [
                'required',
                'image',
                'mimes:jpeg,png,jpg,webp',
                'max:2048', // 2MB
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'banner.required' => 'تصویر بنر الزامی است.',
            'banner.image' => 'فایل باید تصویر باشد.',
            'banner.mimes' => 'فرمت تصویر باید JPG، PNG یا WebP باشد.',
            'banner.max' => 'حجم تصویر نباید بیشتر از 2 مگابایت باشد.',
        ];
    }
}
