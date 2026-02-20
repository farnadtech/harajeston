<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateListingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->role === 'seller';
    }

    public function rules(): array
    {
        $rules = [
            'type' => ['required', Rule::in(['auction', 'direct_sale', 'hybrid'])],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:5000'],
            'category_id' => [
                'required',
                'exists:categories,id',
                function ($attribute, $value, $fail) {
                    $category = \App\Models\Category::find($value);
                    if ($category && $category->isParent()) {
                        $fail('فقط زیردسته‌ها قابل انتخاب هستند. لطفاً یک زیردسته انتخاب کنید.');
                    }
                },
            ],
            'attributes' => ['nullable', 'array'],
            'attributes.*' => ['nullable', 'string', 'max:255'],
        ];

        // Type-specific validation
        $type = $this->input('type');

        if ($type === 'auction' || $type === 'hybrid') {
            $rules['base_price'] = ['required', 'numeric', 'min:1000'];
            $rules['start_time'] = ['required', 'date', 'after:now'];
            $rules['end_time'] = ['required', 'date', 'after:start_time'];
        }

        if ($type === 'direct_sale' || $type === 'hybrid') {
            $rules['price'] = [
                'required',
                'numeric',
                'min:1000',
            ];
            
            // For hybrid, price must be greater than base_price
            if ($type === 'hybrid') {
                $rules['price'][] = function ($attribute, $value, $fail) {
                    $basePrice = $this->input('base_price');
                    if ($basePrice && $value <= $basePrice) {
                        $fail('قیمت فروش مستقیم باید بیشتر از قیمت پایه مزایده باشد.');
                    }
                };
            }
            
            $rules['stock'] = ['required', 'integer', 'min:0'];
            $rules['low_stock_threshold'] = ['nullable', 'integer', 'min:0'];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'type.required' => 'نوع آگهی الزامی است.',
            'type.in' => 'نوع آگهی نامعتبر است.',
            'title.required' => 'عنوان آگهی الزامی است.',
            'title.max' => 'عنوان آگهی نباید بیشتر از 255 کاراکتر باشد.',
            'description.required' => 'توضیحات آگهی الزامی است.',
            'description.max' => 'توضیحات نباید بیشتر از 5000 کاراکتر باشد.',
            'base_price.required' => 'قیمت پایه مزایده الزامی است.',
            'base_price.numeric' => 'قیمت پایه باید عدد باشد.',
            'base_price.min' => 'قیمت پایه باید حداقل 1000 ریال باشد.',
            'start_time.required' => 'زمان شروع مزایده الزامی است.',
            'start_time.after' => 'زمان شروع باید در آینده باشد.',
            'end_time.required' => 'زمان پایان مزایده الزامی است.',
            'end_time.after' => 'زمان پایان باید بعد از زمان شروع باشد.',
            'price.required' => 'قیمت محصول الزامی است.',
            'price.numeric' => 'قیمت باید عدد باشد.',
            'price.min' => 'قیمت باید حداقل 1000 ریال باشد.',
            'price.gt' => 'قیمت فروش مستقیم باید بیشتر از قیمت پایه مزایده باشد.',
            'stock.required' => 'موجودی محصول الزامی است.',
            'stock.integer' => 'موجودی باید عدد صحیح باشد.',
            'stock.min' => 'موجودی نمی‌تواند منفی باشد.',
        ];
    }
}
