<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class JobHistoryRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'facultyId'        => ['required', 'integer', 'exists:faculty,id'],
            'position'         => ['nullable', 'string', 'max:100'],
            'employmentDate'   => ['nullable', 'date'],
            'employmentEndDate'=> ['nullable', 'date'],
            'employmentType'   => ['nullable', 'string', 'max:50'],
            'company'          => ['nullable', 'string', 'max:150'],
            'workLocation'     => ['nullable', 'string', 'max:150'],
        ];
    }
}
