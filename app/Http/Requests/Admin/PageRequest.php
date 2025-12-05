<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $pageid = $this->route('page') ? $this->route('page')->pageid : null;
        $isUpdate = $this->isMethod('PUT') || $this->isMethod('PATCH');

        if ($isUpdate) {
            // Update rules
            return [
                'page_title' => [
                    'required',
                    'string',
                    Rule::unique('stoma_pages', 'page_title')->ignore($pageid, 'pageid'),
                ],
                'description' => ['required', 'string'],
                'meta_title' => ['nullable', 'string'],
                'meta_keyword' => ['nullable', 'string'],
                'meta_description' => ['nullable', 'string'],
                'short_description' => ['nullable', 'string'],
            ];
        }

        // Store rules
        return [
            'page_title' => ['required', 'string', 'unique:stoma_pages,page_title'],
            'description' => ['required', 'string'],
            'status' => ['required', 'in:Enable,Disable'],
            'meta_title' => ['nullable', 'string'],
            'meta_keyword' => ['nullable', 'string'],
            'meta_description' => ['nullable', 'string'],
            'short_description' => ['nullable', 'string'],
        ];
    }

    /**
     * Get custom attribute names for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'page_title' => 'page title',
            'description' => 'content',
            'meta_title' => 'meta title',
            'meta_keyword' => 'meta keyword',
            'meta_description' => 'meta description',
            'short_description' => 'short description',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'page_title.required' => 'The page title is required.',
            'page_title.unique' => 'This page title already exists.',
            'description.required' => 'The content is required.',
        ];
    }
}

