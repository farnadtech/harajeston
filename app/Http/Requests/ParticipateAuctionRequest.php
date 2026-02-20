<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ParticipateAuctionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->role === 'buyer';
    }

    public function rules(): array
    {
        return [
            // No additional fields needed - participation is automatic
        ];
    }

    public function messages(): array
    {
        return [];
    }
}
