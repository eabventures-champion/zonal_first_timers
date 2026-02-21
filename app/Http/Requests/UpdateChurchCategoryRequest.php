<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateChurchCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('manage-church-categories');
    }

    public function rules(): array
    {
        $category = $this->route('church_category');
        $id = is_object($category) ? $category->id : $category;

        return [
            'name' => 'required|string|max:255|unique:church_categories,name,' . $id,
            'zonal_pastor_name' => 'nullable|string|max:255',
            'zonal_pastor_contact' => 'nullable|string|max:255',
        ];
    }
}
