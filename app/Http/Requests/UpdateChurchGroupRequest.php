<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateChurchGroupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('manage-church-categories');
    }

    public function rules(): array
    {
        return [
            'church_category_id' => 'required|exists:church_categories,id',
            'name' => 'required|string|max:255',
            'pastor_name' => 'nullable|string|max:255',
            'pastor_contact' => 'nullable|string|max:255',
        ];
    }
}
