<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CurriculumRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'programId'     => ['required', 'integer', 'exists:program,id'],
            'name'          => ['required', 'string', 'max:150'],
            'effectiveYear' => ['required', 'integer', 'min:2000', 'max:2100'],
            'isActive'      => ['sometimes', 'boolean'],
            'description'   => ['nullable', 'string'],
        ];
    }
}
