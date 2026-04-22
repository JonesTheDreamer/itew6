<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExtraCurricularRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'studentId'    => ['required', 'integer', 'exists:student,id'],
            'activity'     => ['nullable', 'string', 'max:150'],
            'role'         => ['nullable', 'string', 'max:100'],
            'organization' => ['nullable', 'string', 'max:150'],
            'startDate'    => ['nullable', 'date'],
            'endDate'      => ['nullable', 'date'],
        ];
    }
}
