<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreGigRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:80',
            'description' => 'required|string',
            'gig_category_id' => 'required|exists:gig_categories,id',
            'gig_subcategory_id' => [
                'required',
                Rule::exists('gig_subcategories', 'id')
                    ->where(fn ($query) => $query->where('gig_category_id', $this->input('gig_category_id'))),
            ],
            'gig_service_type_id' => [
                'required',
                Rule::exists('gig_service_types', 'id')
                    ->where(fn ($query) => $query->where('gig_subcategory_id', $this->input('gig_subcategory_id'))),
            ],
            'search_tags' => 'nullable|array|max:5',
            'packages' => 'required|array|min:1',
            'packages.*.type' => 'required|string|max:50|distinct',
            'packages.*.name' => 'required|string',
            'packages.*.price' => 'required|numeric',
            'packages.*.revisions' => 'nullable|integer|min:0',
            'packages.*.delivery_days' => 'required|integer|min:1',
            'packages.*.description' => 'required|string',
            'thumbnail' => 'nullable|image|max:20480',
            'gallery' => 'nullable|array',
            'gallery.*' => 'nullable|image|max:20480',
            'video' => 'nullable|mimes:mp4,mov,avi|max:102400',
            'documents.*' => 'nullable|mimes:pdf,doc,docx|max:20480',
            'requirement_questions' => 'nullable|array',
            'remove_media' => 'nullable|array',
        ];
    }
}
