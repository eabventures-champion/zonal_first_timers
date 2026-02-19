<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFirstTimerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('manage-first-timers');
    }

    public function rules(): array
    {
        $id = $this->route('first_timer');

        return [
            'church_id' => 'required|exists:churches,id',
            'full_name' => 'required|string|max:255',
            'primary_contact' => 'required|string|max:20|unique:first_timers,primary_contact,' . $id,
            'alternate_contact' => 'nullable|string|max:20|unique:first_timers,alternate_contact,' . $id,
            'gender' => 'required|in:Male,Female',
            'date_of_birth' => 'nullable|date|before:today',
            'age' => 'nullable|integer|min:0|max:150',
            'residential_address' => 'required|string|max:1000',
            'occupation' => 'nullable|string|max:255',
            'marital_status' => 'required|in:Single,Married,Divorced,Widowed',
            'email' => 'required|email|unique:first_timers,email,' . $id,
            'bringer_name' => 'nullable|string|max:255',
            'bringer_contact' => 'nullable|string|max:20',
            'bringer_fellowship' => 'nullable|string|max:255',
            'born_again' => 'required|boolean',
            'water_baptism' => 'required|boolean',
            'prayer_requests' => 'nullable|string|max:2000',
            'date_of_visit' => 'required|date',
            'church_event' => 'nullable|string|max:255',
            'status' => 'nullable|in:New,In Progress,Member',
            'retaining_officer_id' => 'nullable|exists:users,id',
        ];
    }
}
