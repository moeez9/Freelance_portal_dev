<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreJobRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'upload_logo' => 'nullable|image|max:20480',
            'upload_banner' => 'nullable|image|max:20480',
            'title' => 'required|string|max:255',
            'job_category_id' => 'nullable|exists:job_categories,id',
            'categories' => 'nullable|string|max:255',
            'other_category' => 'exclude_unless:categories,Other|required|string|max:255',
            'deadline' => 'required|date|after_or_equal:today',
            'url' => 'required|url',
            'email' => 'required|email',
            'phone_no' => 'required|string|max:30',
            'salary_type_id' => 'nullable|exists:salary_types,id',
            'salary_type' => 'required_without:salary_type_id|string|max:255',
            'min' => 'required|numeric',
            'max' => 'required|numeric|gte:min',
            'description' => 'required|string',
            'requirements' => 'required|string',
            'required_skills' => 'required|string|max:2000',
            'company_name' => 'required|string|max:255',
            'job_location' => 'required|string|max:255',
        ];
    }
}
