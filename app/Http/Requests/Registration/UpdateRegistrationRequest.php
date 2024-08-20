<?php

namespace App\Http\Requests\Registration;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRegistrationRequest extends FormRequest
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
            'pname' => ['nullable', 'string'],
            'tdate' => ['nullable', 'date_format:d-m-Y'],
            'mobile_number' => ['nullable', 'string'],
            'email' => ['nullable', 'email'],
            'echannel' => ['nullable', 'string'],
            'trace_id' => ['nullable', 'string'],
            'txn_type' => ['nullable', 'string'],
            'mno' => ['nullable', 'string'],
            'country_code' => ['nullable', 'string']
        ];
    }
}
