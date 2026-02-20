<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadImageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'images' => ['required', 'array', 'max:5'],
            'images.*' => [
                'required',
                'image',
                'mimes:jpeg,png,jpg,webp',
                'max:5120', // 5MB
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'images.required' => 'حداقل یک تصویر الزامی است.',
            'images.array' => 'تصاویر باید به صورت آرایه ارسال شوند.',
            'images.max' => 'حداکثر 5 تصویر می‌توانید آپلود کنید.',
            'images.*.required' => 'تصویر الزامی است.',
            'images.*.image' => 'فایل باید تصویر باشد.',
            'images.*.mimes' => 'فرمت تصویر باید JPG، PNG یا WebP باشد.',
            'images.*.max' => 'حجم هر تصویر نباید بیشتر از 5 مگابایت باشد.',
        ];
    }
}
