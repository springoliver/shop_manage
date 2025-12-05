<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRequest extends FormRequest
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
        $storeid = $this->route('store') ? $this->route('store')->storeid : null;
        $isUpdate = $this->isMethod('PUT') || $this->isMethod('PATCH');

        return [
            // Basic store information
            'storeownerid' => ['required', 'exists:stoma_storeowner,ownerid'],
            'store_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('stoma_store', 'store_name')->ignore($storeid, 'storeid'),
            ],
            'typeid' => ['required', 'integer', 'exists:stoma_storetype,typeid'],
            'logofile' => ['nullable', 'image', 'mimes:jpeg,jpg,png,gif', 'max:2048'],
            'remove_logo' => ['nullable', 'in:yes,no'],

            // Contact information
            'store_email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('stoma_store', 'store_email')->ignore($storeid, 'storeid'),
            ],
            'store_email_pass' => ['nullable', 'string', 'max:255'],
            'manager_email' => ['nullable', 'email', 'max:255'],
            'website_url' => ['nullable', 'url', 'max:255'],

            // Address information
            'full_google_address' => ['nullable', 'string', 'max:255'],
            'latitude' => ['nullable', 'string', 'max:100'],
            'longitude' => ['nullable', 'string', 'max:100'],

            // Monday hours
            'monday_hour_from' => ['nullable', 'date_format:H:i', 'required_with:monday_hour_to'],
            'monday_hour_to' => ['nullable', 'date_format:H:i', 'required_with:monday_hour_from'],
            'monday_dayoff' => ['nullable', 'in:Yes,No'],

            // Tuesday hours
            'tuesday_hour_from' => ['nullable', 'date_format:H:i', 'required_with:tuesday_hour_to'],
            'tuesday_hour_to' => ['nullable', 'date_format:H:i', 'required_with:tuesday_hour_from'],
            'tuesday_dayoff' => ['nullable', 'in:Yes,No'],

            // Wednesday hours
            'wednesday_hour_from' => ['nullable', 'date_format:H:i', 'required_with:wednesday_hour_to'],
            'wednesday_hour_to' => ['nullable', 'date_format:H:i', 'required_with:wednesday_hour_from'],
            'wednesday_dayoff' => ['nullable', 'in:Yes,No'],

            // Thursday hours
            'thursday_hour_from' => ['nullable', 'date_format:H:i', 'required_with:thursday_hour_to'],
            'thursday_hour_to' => ['nullable', 'date_format:H:i', 'required_with:thursday_hour_from'],
            'thursday_dayoff' => ['nullable', 'in:Yes,No'],

            // Friday hours
            'friday_hour_from' => ['nullable', 'date_format:H:i', 'required_with:friday_hour_to'],
            'friday_hour_to' => ['nullable', 'date_format:H:i', 'required_with:friday_hour_from'],
            'friday_dayoff' => ['nullable', 'in:Yes,No'],

            // Saturday hours
            'saturday_hour_from' => ['nullable', 'date_format:H:i', 'required_with:saturday_hour_to'],
            'saturday_hour_to' => ['nullable', 'date_format:H:i', 'required_with:saturday_hour_from'],
            'saturday_dayoff' => ['nullable', 'in:Yes,No'],

            // Sunday hours
            'sunday_hour_from' => ['nullable', 'date_format:H:i', 'required_with:sunday_hour_to'],
            'sunday_hour_to' => ['nullable', 'date_format:H:i', 'required_with:sunday_hour_from'],
            'sunday_dayoff' => ['nullable', 'in:Yes,No'],

            // Status
            'status' => ['nullable', 'in:Pending Setup,Active,Suspended,Closed'],
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
            'storeownerid' => 'store owner',
            'store_name' => 'store name',
            'typeid' => 'store category',
            'logofile' => 'logo image',
            'store_email' => 'store email',
            'store_email_pass' => 'store email password',
            'manager_email' => 'manager email',
            'website_url' => 'website URL',
            'full_google_address' => 'address',
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
            'store_email.unique' => 'This email address is already registered for another store.',
            'store_name.unique' => 'This store name is already taken.',
            'storeownerid.exists' => 'The selected store owner does not exist.',
            'typeid.exists' => 'The selected store category does not exist.',
            '*.date_format' => 'The time must be in HH:MM format (e.g., 09:00).',
            '*.required_with' => 'Both opening and closing hours must be provided.',
        ];
    }
}

