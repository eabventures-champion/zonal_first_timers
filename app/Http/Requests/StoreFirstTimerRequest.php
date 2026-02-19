<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFirstTimerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('manage-first-timers');
    }

    public function rules(): array
    {
        return [
            'church_id' => 'required|exists:churches,id',
            'full_name' => 'required|string|max:255',
            'primary_contact' => 'required|string|max:20|unique:first_timers,primary_contact',
            'alternate_contact' => 'nullable|string|max:20|unique:first_timers,alternate_contact',
            'gender' => 'required|in:Male,Female',
            'date_of_birth' => 'nullable|date|before:today',
            'age' => 'nullable|integer|min:0|max:150',
            'residential_address' => 'required|string|max:1000',
            'occupation' => 'nullable|string|max:255',
            'marital_status' => 'required|in:Single,Married,Divorced,Widowed',
            'email' => 'required|email|unique:first_timers,email',
            'bringer_name' => 'nullable|string|max:255',
            'bringer_contact' => 'nullable|string|max:20',
            'bringer_fellowship' => 'nullable|string|max:255',
            'born_again' => 'required|boolean',
            'water_baptism' => 'required|boolean',
            'prayer_requests' => 'nullable|string|max:2000',
            'date_of_visit' => 'required|date',
            'church_event' => 'nullable|string|max:255',
            'retaining_officer_id' => 'nullable|exists:users,id',
        ];
    }

    public function messages(): array
    {
        return [
            'primary_contact.unique' => 'This phone number is already registered.',
            'email.unique' => 'This email address is already registered.',
        ];
    }
}
