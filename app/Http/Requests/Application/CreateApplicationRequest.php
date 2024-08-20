<?php

namespace App\Http\Requests\Application;

use Illuminate\Foundation\Http\FormRequest;

class CreateApplicationRequest extends FormRequest
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
            'acct_no' => ['required', 'string', 'unique:tBillApplication,acct_no', 'max:255'],
            'login_id' => ['required', 'string', 'max:255'],
            'tdate' => ['required', 'date_format:d-m-Y'],
            'sec_code' => ['required', 'string', 'max:255'],
            'amt' => ['required', 'numeric'],
            'next_of_kin' => ['required', 'string', 'max:255'],
            'next_of_kin_contact' => ['required', 'string', 'max:255'],
            'trace_id' => ['required', 'string', 'unique:tBillApplication,trace_id', 'max:255'],
            'bank_code' => ['required', 'string', 'max:255'],
            'txn_type' => ['required', 'string', 'max:255'],
            'country_code' => ['required', 'string', 'max:30'],
            'echannel' => ['required', 'string', 'max:255'],
            'fsource' => ['required', 'string', 'max:255'],
            'app_module' => ['required', 'string', 'max:255'],
            'mno' => ['nullable', 'string', 'max:255'],
            'cost' => ['required', 'numeric'],
            'face_value' => ['required', 'numeric'],
            'int_rate' => ['required', 'numeric'],
            'disc_rate' => ['nullable', 'numeric'],
            'value_date' => ['required', 'date_format:d-m-Y'],
            'mat_date' => ['required', 'date_format:d-m-Y'],
            'inv_amt_type' => ['required', 'string', 'max:255']
        ];
    }
}
