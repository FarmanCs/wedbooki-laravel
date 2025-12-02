<?php

namespace App\Http\Requests\Vendor;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class SignupRequest extends FormRequest
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
            'full_name' => 'required|string|max:255',
            'company_name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'sub_category_id' => 'nullable|exists:sub_categories,id',
            'country' => 'required|string',
            'city' => 'required|string',
            'email' => 'required|email|unique:vendors,email|unique:hosts,email',
            'phone_no' => 'required|numeric|unique:vendors,phone_no|unique:hosts,phone_no',
            'country_code' => 'required|string',
            'password' => [
                'required',
                'string',
                'min:8',
                'regex:/[A-Z]/', // must contain uppercase
            ],
            'business_registration' => 'nullable|string',
            'business_license_number' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.exists' => 'The selected category does not exist.',
            'sub_category_id.exists' => 'The selected sub category does not exist.',
            'email.unique' => 'This email is already registered.',
            'phone_no.unique' => 'This phone number is already registered.',
            'password.regex' => 'Password must contain at least one uppercase letter.',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'status' => 'failure',
                'message' => $validator->errors()->all(),
            ], 403)
        );
    }

}
