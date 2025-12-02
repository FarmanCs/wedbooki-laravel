<?php

namespace App\Http\Requests\Vendor;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class VendorUpdateProfileRequest extends FormRequest
{
    // Determine if the user is authorized to make this request.
    public function authorize(): bool
    {
        return true;
    }

    // Get the validation rules that apply to the request.

    public function rules(): array
    {
        return [
            'name' => 'nullable|string|max:255',
            'years_of_experince' => 'nullable|integer',
            'team_members' => 'nullable|integer',
            'phone_no' => 'nullable|string|max:20',
            'country_code' => 'nullable|string|max:5',
            'country' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'about' => 'nullable|string',
            'languages' => 'nullable|string',
            'specialties' => 'nullable',
            'profile_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new \HttpResponseException(response()->json([
            'status' => 'failure',
            'message' => $validator->errors()->all(),
        ], 403));
    }


}
