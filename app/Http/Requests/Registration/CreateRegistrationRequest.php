<?php

namespace App\Http\Requests\Registration;

use Illuminate\Foundation\Http\FormRequest;

class CreateRegistrationRequest extends FormRequest
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
        return [
            'pname' => ['required', 'string'],
            'tdate' => ['required', 'date_format:d-m-Y'],
            'mobile_number' => ['required', 'string'],
            'email' => ['required', 'email', 'unique:tBillRegistration,email'],
            'echannel' => ['required', 'string'],
            'trace_id' => ['required', 'string', 'unique:tBillRegistration,trace_id'],
            'txn_type' => ['required', 'string'],
            'mno' => ['nullable', 'string'],
            'country_code' => ['required', 'string']
        ];
    }
}
