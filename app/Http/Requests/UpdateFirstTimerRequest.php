<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateFirstTimerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('manage-first-timers');
    }

    public function rules(): array
    {
        $firstTimer = $this->route('first_timer');
        $id = is_object($firstTimer) ? $firstTimer->id : $firstTimer;

        return [
            'church_id' => ['required', 'exists:churches,id'],
            'bringer_id' => ['nullable', 'exists:bringers,id'],
            'full_name' => ['required', 'string', 'max:255'],
            'primary_contact' => [
                'required',
                'string',
                'min:10',
                'max:20',
                Rule::unique('first_timers', 'primary_contact')->ignore($id)
            ],
            'alternate_contact' => [
                'nullable',
                'string',
                'min:10',
                'max:20',
                Rule::unique('first_timers', 'alternate_contact')->ignore($id)
            ],
            'gender' => ['required', 'in:Male,Female'],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'age' => ['nullable', 'integer', 'min:0', 'max:150'],
            'residential_address' => ['required', 'string', 'max:1000'],
            'occupation' => ['nullable', 'string', 'max:255'],
            'marital_status' => ['required', 'in:Single,Married,Divorced,Widowed'],
            'email' => [
                'nullable',
                'email',
                Rule::unique('first_timers', 'email')->ignore($id)
            ],
            'bringer_name' => ['nullable', 'string', 'max:255'],
            'bringer_contact' => ['nullable', 'string', 'min:10', 'max:20'],
            'born_again' => ['required', 'boolean'],
            'water_baptism' => ['required', 'boolean'],
            'prayer_requests' => ['nullable', 'string', 'max:2000'],
            'date_of_visit' => ['required', 'date'],
            'church_event' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'in:New,Developing,Retained'],
            'retaining_officer_id' => ['nullable', 'exists:users,id'],
        ];
    }

    protected function prepareForValidation(): void
    {
        // Ensure checkboxes are present as booleans
        $this->merge([
            'born_again' => $this->boolean('born_again'),
            'water_baptism' => $this->boolean('water_baptism'),
        ]);

        if ($this->has(['dob_day', 'dob_month']) && $this->dob_day && $this->dob_month) {
            // Use leap year 2000 to allow Feb 29
            $this->merge([
                'date_of_birth' => "2000-{$this->dob_month}-{$this->dob_day}",
            ]);
        }
    }
}
