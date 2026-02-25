<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateListingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Authorization handled in controller
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'condition' => 'required|in:new,like_new,used',
            'starting_price' => 'required|numeric|min:0',
            'buy_now_price' => 'nullable|numeric|min:0',
            'bid_increment' => 'nullable|numeric|min:0',
            'starts_at' => 'required|date',
            'ends_at' => 'required|date|after:starts_at',
            'auto_extend' => 'nullable|boolean',
            'shipping_methods' => 'required|array|min:1',
            'shipping_methods.*' => 'exists:shipping_methods,id',
            'shipping_costs' => 'nullable|array',
            'shipping_costs.*' => 'nullable|numeric|min:0',
            'images' => 'nullable|array|max:8',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'deleted_images' => 'nullable|string',
            'main_image_id' => 'nullable|integer',
            'tags' => 'nullable',
            'attributes' => 'nullable|array',
            'attributes.*' => 'nullable|string',
        ];
    }

    protected function prepareForValidation()
    {
        // Convert auto_extend checkbox to boolean
        if ($this->has('auto_extend')) {
            $this->merge([
                'auto_extend' => $this->auto_extend ? true : false,
            ]);
        } else {
            $this->merge(['auto_extend' => false]);
        }

        // Convert Jalali dates to Gregorian
        if ($this->has('starts_at') && !empty($this->starts_at)) {
            $this->merge([
                'starts_at' => \App\Services\JalaliDateService::toGregorian($this->starts_at)
            ]);
        }
        
        if ($this->has('ends_at') && !empty($this->ends_at)) {
            $this->merge([
                'ends_at' => \App\Services\JalaliDateService::toGregorian($this->ends_at)
            ]);
        }

        // Convert tags string to array (only if not empty)
        if ($this->has('tags') && !empty($this->tags) && is_string($this->tags)) {
            $tags = array_map('trim', explode(',', $this->tags));
            $tags = array_filter($tags); // Remove empty values
            $tags = array_slice($tags, 0, 5); // Max 5 tags
            $this->merge(['tags' => $tags]);
        } elseif (!$this->has('tags') || empty($this->tags)) {
            // If tags is empty, set to empty array
            $this->merge(['tags' => []]);
        }
    }
}
