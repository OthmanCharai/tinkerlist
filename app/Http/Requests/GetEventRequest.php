<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class GetEventRequest extends FormRequest
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
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            // validate date time "2023-05-20 10:00:00"
            'start_date' => ['required', 'date', 'date_format:Y-m-d H:i:s'],
            'end_date' => ['required', 'date', 'date_format:Y-m-d H:i:s', 'after:start_date'],

            // validate per page attribute
            'per_page' => ['nullable', 'integer', 'min:1', 'max:10'],
        ];
    }
}
