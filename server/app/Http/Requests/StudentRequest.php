<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StudentRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'firstName'    => ['required', 'string', 'max:100'],
            'lastName'     => ['required', 'string', 'max:100'],
            'middleName'   => ['nullable', 'string', 'max:100'],
            'email'        => ['required', 'email', 'max:100'],
            'age'          => ['nullable', 'integer', 'min:15', 'max:100'],
            'birthDate'    => ['nullable', 'date'],
            'birthProvince'=> ['nullable', 'string', 'max:100'],
            'mobileNumber' => ['nullable', 'string', 'max:20'],
            'city'         => ['nullable', 'string', 'max:100'],
            'province'     => ['nullable', 'string', 'max:100'],
            'programId'    => ['nullable', 'integer', 'exists:program,id'],
            'yearLevel'    => ['nullable', 'integer', 'min:1', 'max:6'],
            'unitsTaken'   => ['nullable', 'integer', 'min:0'],
            'unitsLeft'    => ['nullable', 'integer', 'min:0'],
            'dateEnrolled' => ['nullable', 'date'],
            'dateGraduated'=> ['nullable', 'date'],
            'dateDropped'  => ['nullable', 'date'],
            'gpa'          => ['nullable', 'numeric', 'min:0', 'max:4'],
            'status'       => ['nullable', 'in:Active,Graduated,Dropped,Inactive'],
            'studentId'    => ['nullable', 'string', 'max:20'],
            'skillIds'     => ['nullable', 'array'],
            'skillIds.*'   => ['integer', 'exists:skills,id'],
        ];
    }
}
