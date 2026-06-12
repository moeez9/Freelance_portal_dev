<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'phone_no' => [
                'required',
                'string',
                'max:30',
                Rule::unique(User::class, 'phone_no')->ignore($this->user()->id),
            ],
            'profile_pic' => ['nullable', 'image', 'max:2048'],
            'candidate_payment_method' => ['nullable', 'in:bank_transfer,jazzcash,easypaisa'],
            'candidate_payment_details' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
