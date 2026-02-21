<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateChurchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('manage-churches');
    }

    public function rules(): array
    {
        return [
            'church_group_id' => 'required|exists:church_groups,id',
            'name' => 'required|string|max:255',
            'leader_name' => 'nullable|string|max:255',
            'leader_contact' => 'nullable|string|max:255',
            'retaining_officer_id' => 'nullable|exists:users,id',
        ];
    }
}
