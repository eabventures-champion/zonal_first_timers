<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreChurchCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('manage-church-categories');
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:church_categories,name',
            'description' => 'nullable|string|max:1000',
        ];
    }
}
