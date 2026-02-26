<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'pastor_contact' => [
                'required_unless:pastor_name,Highly Esteemed Pastor Lisa Ma',
                'nullable',
                'string',
                'max:255',
                Rule::unique('church_groups', 'pastor_contact')->ignore($this->route('church_group')),
            ],
        ];
    }
}
