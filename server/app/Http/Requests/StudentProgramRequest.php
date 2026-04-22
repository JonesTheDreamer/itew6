<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StudentProgramRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'studentId'    => ['required', 'integer', 'exists:student,id'],
            'programId'    => ['required', 'integer', 'exists:program,id'],
            'dateEnrolled' => ['nullable', 'date'],
            'dateLeft'     => ['nullable', 'date'],
        ];
    }
}
