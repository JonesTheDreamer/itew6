<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EducationalBackgroundRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user' => ['required', 'integer', 'exists:user,id'],
            'schoolUniversity' => ['nullable', 'string', 'max:150'],
            'startYear' => ['nullable', 'integer'],
            'graduateYear' => ['nullable', 'integer'],
            'type' => ['nullable', 'string', 'max:50'],
            'award' => ['nullable', 'string', 'max:100'],
        ];
    }
}