<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateListingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->role === 'seller' || $this->user()->role === 'admin';
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Convert Persian dates to Gregorian
        if ($this->has('starts_at')) {
            $this->merge([
                'starts_at' => $this->convertPersianToGregorian($this->starts_at),
            ]);
        }

        if ($this->has('ends_at')) {
            $this->merge([
                'ends_at' => $this->convertPersianToGregorian($this->ends_at),
            ]);
        }
    }

    /**
     * Convert Persian date to Gregorian format
     */
    private function convertPersianToGregorian(?string $persianDate): ?string
    {
        if (!$persianDate) {
            return null;
        }

        try {
            // Check if already in Gregorian format (YYYY-MM-DD)
            if (preg_match('/^\d{4}-\d{2}-\d{2}/', $persianDate)) {
                return $persianDate;
            }

            // Parse Persian date (format: 1403/12/04 14:30)
            $parts = explode(' ', $persianDate);
            $dateParts = explode('/', $parts[0]);
            $timeParts = isset($parts[1]) ? explode(':', $parts[1]) : ['00', '00'];

            if (count($dateParts) !== 3) {
                return null;
            }

            $jYear = (int) $dateParts[0];
            $jMonth = (int) $dateParts[1];
            $jDay = (int) $dateParts[2];
            $hour = (int) $timeParts[0];
            $minute = (int) $timeParts[1];

            // Convert to Gregorian using Jalalian package
            $gregorian = \Morilog\Jalali\Jalalian::fromFormat('Y/m/d H:i', $persianDate)
                ->toCarbon()
                ->format('Y-m-d H:i:s');

            return $gregorian;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to convert Persian date: ' . $e->getMessage(), [
                'date' => $persianDate
            ]);
            return null;
        }
    }

    public function rules(): array
    {
        $rules = [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:5000'],
            'category_id' => [
                'required',
                'exists:categories,id',
                function ($attribute, $value, $fail) {
                    $category = \App\Models\Category::find($value);
                    if ($category && $category->hasChildren()) {
                        $fail('فقط دسته‌های نهایی (بدون زیردسته) قابل انتخاب هستند.');
                    }
                },
            ],
            'condition' => ['required', Rule::in(['new', 'like_new', 'used'])],
            'starting_price' => ['required', 'numeric', 'min:1000'],
            'buy_now_price' => ['nullable', 'numeric', 'min:1000', 'gt:starting_price'],
            'bid_increment' => ['nullable', 'numeric', 'min:1000'],
            'starts_at' => ['required', 'date', 'after:now'],
            'ends_at' => ['required', 'date', 'after:starts_at'],
            'auto_extend' => ['nullable', 'boolean'],
            'attributes' => ['nullable', 'array'],
            'attributes.*' => ['nullable', 'string', 'max:255'],
            'shipping_methods' => ['required', 'array', 'min:1'],
            'shipping_methods.*' => ['exists:shipping_methods,id'],
            'shipping_costs' => ['nullable', 'array'],
            'shipping_costs.*' => ['nullable', 'numeric', 'min:0'],
            'tags' => ['nullable', 'string', 'max:500'],
            'images' => ['nullable', 'array', 'max:8'],
            'images.*' => ['image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ];

        return $rules;
    }

    public function messages(): array
    {
        return [
            'title.required' => 'عنوان حراجی الزامی است.',
            'title.max' => 'عنوان حراجی نباید بیشتر از 255 کاراکتر باشد.',
            'description.required' => 'توضیحات حراجی الزامی است.',
            'description.max' => 'توضیحات نباید بیشتر از 5000 کاراکتر باشد.',
            'category_id.required' => 'انتخاب دسته‌بندی الزامی است.',
            'category_id.exists' => 'دسته‌بندی انتخاب شده معتبر نیست.',
            'condition.required' => 'وضعیت کالا الزامی است.',
            'condition.in' => 'وضعیت کالا نامعتبر است.',
            'starting_price.required' => 'قیمت شروع الزامی است.',
            'starting_price.numeric' => 'قیمت شروع باید عدد باشد.',
            'starting_price.min' => 'قیمت شروع باید حداقل 1000 تومان باشد.',
            'buy_now_price.numeric' => 'قیمت خرید فوری باید عدد باشد.',
            'buy_now_price.min' => 'قیمت خرید فوری باید حداقل 1000 تومان باشد.',
            'buy_now_price.gt' => 'قیمت خرید فوری باید بیشتر از قیمت شروع باشد.',
            'bid_increment.numeric' => 'حداقل افزایش پیشنهاد باید عدد باشد.',
            'bid_increment.min' => 'حداقل افزایش پیشنهاد باید حداقل 1000 تومان باشد.',
            'starts_at.required' => 'زمان شروع حراجی الزامی است.',
            'starts_at.after' => 'زمان شروع باید در آینده باشد.',
            'ends_at.required' => 'زمان پایان حراجی الزامی است.',
            'ends_at.after' => 'زمان پایان باید بعد از زمان شروع باشد.',
            'shipping_methods.required' => 'انتخاب حداقل یک روش ارسال الزامی است.',
            'shipping_methods.min' => 'حداقل یک روش ارسال را انتخاب کنید.',
            'shipping_methods.*.exists' => 'روش ارسال انتخاب شده معتبر نیست.',
            'images.max' => 'حداکثر 8 تصویر می‌توانید آپلود کنید.',
            'images.*.image' => 'فایل باید تصویر باشد.',
            'images.*.mimes' => 'فرمت تصویر باید jpeg، png، jpg یا gif باشد.',
            'images.*.max' => 'حجم هر تصویر نباید بیشتر از 2MB باشد.',
        ];
    }
}
