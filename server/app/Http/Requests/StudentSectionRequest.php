<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StudentSectionRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'studentId'    => ['required', 'integer', 'exists:student,id'],
            'sectionId'    => ['required', 'integer', 'exists:section,id'],
            'academicYear' => ['required', 'string', 'max:20'],
            'semester'     => ['required', 'integer', 'in:1,2'],
        ];
    }
}
