<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreChurchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('manage-churches');
    }

    public function rules(): array
    {
        return [
            'church_group_id' => 'required|exists:church_groups,id',
            'retaining_officer_id' => 'nullable|exists:users,id',
            'churches' => 'required|array|min:1',
            'churches.*.name' => 'required|string|max:255',
            'churches.*.leader_name' => 'nullable|string|max:255',
            'churches.*.leader_contact' => 'nullable|string|max:255|distinct|unique:churches,leader_contact',
        ];
    }
}
