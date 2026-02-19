<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ImportFirstTimersRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('manage-first-timers');
    }

    public function rules(): array
    {
        return [
            'church_id' => 'required|exists:churches,id',
            'csv_file' => 'required|file|mimes:csv,txt|max:5120',
        ];
    }

    public function messages(): array
    {
        return [
            'csv_file.max' => 'The CSV file must not be larger than 5MB.',
            'csv_file.mimes' => 'Please upload a valid CSV file.',
        ];
    }
}
