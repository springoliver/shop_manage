<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ModuleRequest extends FormRequest
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
        $moduleid = $this->route('module') ? $this->route('module')->moduleid : null;
        $isUpdate = $this->isMethod('PUT') || $this->isMethod('PATCH');

        if ($isUpdate) {
            // Update rules
            return [
                'module' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('stoma_module', 'module')->ignore($moduleid, 'moduleid'),
                ],
                'module_description' => ['required', 'string'],
                'module_detailed_info' => ['nullable', 'string'],
                'price_1months' => ['required', 'numeric', 'min:0'],
                'price_3months' => ['required', 'numeric', 'min:0'],
                'price_6months' => ['required', 'numeric', 'min:0'],
                'price_12months' => ['required', 'numeric', 'min:0'],
                'free_days' => ['required', 'integer', 'min:0'],
            ];
        }

        // Store rules
        return [
            'module' => ['required', 'string', 'max:255', 'unique:stoma_module,module'],
            'module_description' => ['required', 'string'],
            'module_detailed_info' => ['nullable', 'string'],
            'price_1months' => ['required', 'numeric', 'min:0'],
            'price_3months' => ['required', 'numeric', 'min:0'],
            'price_6months' => ['required', 'numeric', 'min:0'],
            'price_12months' => ['required', 'numeric', 'min:0'],
            'free_days' => ['required', 'integer', 'min:0'],
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
            'module' => 'module name',
            'module_description' => 'module description',
            'module_detailed_info' => 'module detailed information',
            'price_1months' => '1 month price',
            'price_3months' => '3 months price',
            'price_6months' => '6 months price',
            'price_12months' => '12 months price',
            'free_days' => 'free days',
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
            'module.unique' => 'This module name already exists.',
            'module.required' => 'The module name is required.',
            'module_description.required' => 'The module description is required.',
            'price_1months.required' => 'The 1 month price is required.',
            'price_3months.required' => 'The 3 months price is required.',
            'price_6months.required' => 'The 6 months price is required.',
            'price_12months.required' => 'The 12 months price is required.',
            'free_days.required' => 'The free days field is required.',
        ];
    }
}

