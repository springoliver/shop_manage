<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SettingRequest extends FormRequest
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
        $settingid = $this->route('setting') ? $this->route('setting')->settingid : null;
        $isUpdate = $this->isMethod('PUT') || $this->isMethod('PATCH');

        if ($isUpdate) {
            // Update rules
            return [
                'title' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('stoma_setting', 'title')->ignore($settingid, 'settingid'),
                ],
                'value' => ['required', 'string'],
            ];
        }

        // Store rules
        return [
            'title' => ['required', 'string', 'max:255', 'unique:stoma_setting,title'],
            'value' => ['required', 'string'],
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
            'title' => 'field name',
            'value' => 'field value',
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
            'title.unique' => 'This setting field already exists.',
            'title.required' => 'The field name is required.',
            'value.required' => 'The field value is required.',
        ];
    }
}

