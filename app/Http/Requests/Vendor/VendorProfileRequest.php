<?php

namespace App\Http\Requests\Vendor;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class VendorProfileRequest extends FormRequest
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
            'profile_image'    => 'nullable|image|max:51200', // 50MB
            'cover_image'      => 'nullable|image|max:51200',
            'portfolio_images' => 'nullable|array|min:3|max:20',
            'portfolio_images.*' => 'image|max:51200',

            'videos' => 'nullable|array|max:10',
            'videos.*' => 'mimetypes:video/mp4,video/webm|max:102400', // 100MB

            'chat_document' => 'nullable|mimes:pdf,doc,docx|max:51200',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'status'  => 'failure',
                'message' => $validator->errors()->toArray()
            ], 403)
        );
    }
}
