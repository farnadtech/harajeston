<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        $store = $this->route('store');
        return $this->user()->id === $store->user_id;
    }

    public function rules(): array
    {
        return [
            'store_name' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'store_name.max' => 'نام فروشگاه نباید بیشتر از 255 کاراکتر باشد.',
            'description.max' => 'توضیحات نباید بیشتر از 2000 کاراکتر باشد.',
        ];
    }
}
