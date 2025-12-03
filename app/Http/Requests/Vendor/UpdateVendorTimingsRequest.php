<?php

namespace App\Http\Requests\Vendor;

use Illuminate\Foundation\Http\FormRequest;

class UpdateVendorTimingsRequest extends FormRequest
{
    //Determine if the user is authorized to make this request.
    public function authorize(): bool
    {
        // Allow all authenticated users (or implement your own auth logic)
        return true;
    }

    // Get the validation rules that apply to the request.

    public function rules(): array
    {
        return [
            // For venue category
            'timings_venue' => 'sometimes|array',

            // For service category
            'working_hours' => 'sometimes|array',
            'slot_duration' => 'sometimes|integer|min:1',

            // Unavailable dates
            'unavailable_dates' => 'sometimes|array',
        ];
    }

    //Custom messages for validation errors (optional)
    public function messages(): array
    {
        return [
            'timings_venue.array' => 'The timings for venue must be an array.',
            'working_hours.array' => 'The working hours must be an array.',
            'slot_duration.integer' => 'The slot duration must be an integer.',
            'slot_duration.min' => 'The slot duration must be at least 1 minute.',
            'unavailable_dates.array' => 'The unavailable dates must be an array.',
        ];
    }
}
