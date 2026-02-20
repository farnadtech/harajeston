<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateListingRequest extends FormRequest
{
    public function authorize(): bool
    {
        $listing = $this->route('listing');
        return $this->user()->id === $listing->seller_id;
    }

    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'string', 'max:5000'],
            'category' => ['nullable', 'string', 'max:100'],
            'price' => ['sometimes', 'numeric', 'min:1000'],
            'stock' => ['sometimes', 'integer', 'min:0'],
            'low_stock_threshold' => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.max' => 'عنوان آگهی نباید بیشتر از 255 کاراکتر باشد.',
            'description.max' => 'توضیحات نباید بیشتر از 5000 کاراکتر باشد.',
            'price.numeric' => 'قیمت باید عدد باشد.',
            'price.min' => 'قیمت باید حداقل 1000 ریال باشد.',
            'stock.integer' => 'موجودی باید عدد صحیح باشد.',
            'stock.min' => 'موجودی نمی‌تواند منفی باشد.',
        ];
    }
}
