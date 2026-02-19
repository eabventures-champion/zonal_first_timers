<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreChurchGroupRequest extends FormRequest
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
            'description' => 'nullable|string|max:1000',
        ];
    }
}
