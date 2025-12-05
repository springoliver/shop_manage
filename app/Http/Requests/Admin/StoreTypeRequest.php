<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTypeRequest extends FormRequest
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
        $typeid = $this->route('store_type') ? $this->route('store_type')->typeid : null;
        $isUpdate = $this->isMethod('PUT') || $this->isMethod('PATCH');

        if ($isUpdate) {
            // Update rules
            return [
                'store_type' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('stoma_storetype', 'store_type')->ignore($typeid, 'typeid'),
                ],
            ];
        }

        // Store rules
        return [
            'store_type' => ['required', 'string', 'max:255', 'unique:stoma_storetype,store_type'],
            'status' => ['required', 'in:Enable,Disable'],
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
            'store_type' => 'store category name',
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
            'store_type.unique' => 'This store category name already exists.',
            'store_type.required' => 'The store category name is required.',
        ];
    }
}

