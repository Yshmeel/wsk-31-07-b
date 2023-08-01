<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EditEventRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required',
            'slug' => 'required|regex:([a-z0-9-]+)',
            'date' => 'required'
        ];
    }

    public function messages()
    {
        return [
            'name' => 'Name is required.',
            'slug' => 'Slug must not be empty and only contain a-z, 0-9 and \'-\'',
            'date' => 'Date is required.',
        ];
    }
}
