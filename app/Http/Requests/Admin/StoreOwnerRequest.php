<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreOwnerRequest extends FormRequest
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
        $ownerid = $this->route('storeOwner') ? $this->route('storeOwner')->ownerid : null;
        $isUpdate = $this->isMethod('PUT') || $this->isMethod('PATCH');

        return [
            'firstname' => ['required', 'string', 'max:255'],
            'lastname' => ['required', 'string', 'max:255'],
            'username' => [
                'required',
                'string',
                'max:255',
                Rule::unique('stoma_storeowner', 'username')->ignore($ownerid, 'ownerid'),
            ],
            'emailid' => [
                'required',
                'email',
                'max:255',
                Rule::unique('stoma_storeowner', 'emailid')->ignore($ownerid, 'ownerid'),
            ],
            'password' => $isUpdate ? ['nullable', 'string', 'min:8', 'confirmed'] : ['required', 'string', 'min:8', 'confirmed'],
            'profile_photo' => ['nullable', 'image', 'mimes:jpeg,jpg,png,gif', 'max:2048'],
            'phone' => ['required', 'string', 'max:51'],
            'country' => ['required', 'string', 'max:55'],
            'address1' => ['required', 'string', 'max:255'],
            'address2' => ['nullable', 'string', 'max:255'],
            'state' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'zipcode' => ['required', 'string', 'max:21'],
            'dateofbirth' => ['required', 'date', 'before:today'],
            'accept_terms' => ['nullable', 'in:Yes,No'],
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
            'firstname' => 'first name',
            'lastname' => 'last name',
            'emailid' => 'email address',
            'dateofbirth' => 'date of birth',
            'profile_photo' => 'profile image',
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
            'emailid.unique' => 'This email address is already registered.',
            'username.unique' => 'This username is already taken.',
            'dateofbirth.before' => 'The date of birth must be a date before today.',
        ];
    }
}

