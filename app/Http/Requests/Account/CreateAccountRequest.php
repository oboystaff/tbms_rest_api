<?php

namespace App\Http\Requests\Account;

use Illuminate\Foundation\Http\FormRequest;

class CreateAccountRequest extends FormRequest
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
            'tdate' => ['required', 'date_format:d-m-Y'],
            'login_id' => ['required', 'string'],
            'bank_code' => ['required', 'string'],
            'account_number' => ['required', 'string', 'unique:tBillAccount,account_number'],
            'action' => ['required', 'string'],
            'echannel' => ['required', 'string'],
            'trace_id' => ['required', 'string', 'unique:tBillAccount,trace_id'],
            'txn_type' => ['required', 'string']
        ];
    }
}
