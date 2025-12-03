<?php

namespace App\Http\Requests\Vendor;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateBusinessProfileRequest extends FormRequest
{
    // Determine if the user is authorized to make this request.

    public function authorize(): bool
    {
        return true;
    }

    //Get the validation rules that apply to the request.
    public function rules(): array
    {
        return [
            'company_name'             => 'nullable|string|max:255',
            'business_desc'            => 'nullable|string',
            'category_id'              => 'nullable|exists:categories,id',
            'sub_category_id'           => 'nullable|exists:sub_categories,id',
            'venue_type'               => 'nullable|string|max:255',
            'member_type'              => 'nullable|string|max:255',
            'business_registration'    => 'nullable|string|max:255',
            'business_license_number'  => 'nullable|string|max:255',
            'rating'                   => 'nullable|numeric|min:0|max:5',
            'is_featured'              => 'nullable|boolean',
            'business_type'            => 'nullable|in:partnership,llc,corporation',
            'website'                  => 'nullable|string|max:255',
            'social_links'             => 'nullable|array',
            'postal_code'              => 'nullable|string|max:20',
            'business_email'            => 'nullable|email',
            'business_phone'            => 'nullable|string|max:20',
            'features'                 => 'nullable|array',
            'profile_verification'     => 'nullable|in:under_review,rejected,verified',
            'services'                 => 'nullable|array',
            'faqs'                     => 'nullable|array',
            'street_address'           => 'nullable|string',
            'capacity'                 => 'nullable|integer',
            'payment_days_advance'     => 'nullable|integer|min:0',
            'payment_days_final'       => 'nullable|integer|min:0',
            'services_radius'          => 'nullable|integer|min:0',
            'advance_percentage'       => 'nullable|numeric|min:0|max:100',

            'profile_image'            => 'nullable|image|max:51200',
            'cover_image'              => 'nullable|image|max:51200',
            'portfolio_images'         => 'nullable|array',
            'portfolio_images.*'       => 'image|max:51200',
            'videos'                   => 'nullable|array',
            'videos.*'                 => 'mimetypes:video/mp4,video/mkv,video/webm,video/quicktime|max:51200',

            'chat_image'               => 'nullable|image|max:51200',
            'chat_video'               => 'nullable|mimetypes:video/mp4,video/mkv,video/webm,video/quicktime|max:51200',
            'chat_document'            => 'nullable|mimes:pdf,doc,docx|max:51200'
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
