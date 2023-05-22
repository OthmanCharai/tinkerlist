<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EventStoreRequest extends FormRequest
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
     */
    public function rules(): array
    {
        //can use EventStoreRequest but for the scalability
        return [
            'date' => ['required', 'date', 'after:now'], // validate that date should be in feature
            'time' => ['required', 'date_format:H:i:s'], // validate time format
            'title' => ['required', 'string'],
            'description' => ['required', 'string'],
            'location' => ['required', 'string'],
            'invitees' => ['required', 'array'], // validate invitees need to be an array
            'invitees.*' => 'exists:users,id',   // check if the invitees exists in our db,
            // I chose these way based on requirement,
            // and also the email need to be verified, to make sure invitation emails send
        ];
    }
}
