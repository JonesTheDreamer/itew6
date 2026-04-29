<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FacultyRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'firstName'      => ['required', 'string', 'max:100'],
            'lastName'       => ['required', 'string', 'max:100'],
            'middleName'     => ['nullable', 'string', 'max:100'],
            'email'          => ['required', 'email', 'max:100'],
            'age'            => ['nullable', 'integer', 'min:18', 'max:100'],
            'birthDate'      => ['nullable', 'date'],
            'mobileNumber'   => ['nullable', 'string', 'max:20'],
            'city'           => ['nullable', 'string', 'max:100'],
            'province'       => ['nullable', 'string', 'max:100'],
            'position'       => ['nullable', 'string', 'max:100'],
            'employmentDate' => ['nullable', 'date'],
            'employmentType' => ['nullable', 'string', 'max:50'],
            'monthlyIncome'  => ['nullable', 'numeric', 'min:0'],
            'department'     => ['nullable', 'string', 'max:100'],
            'password' => ['nullable', 'string', 'min:8'],
        ];
    }
}
