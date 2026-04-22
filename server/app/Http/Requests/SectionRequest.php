<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SectionRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $sectionId = $this->route('id');

        return [
            'programId'    => ['required', 'integer', 'exists:program,id'],
            'sectionName'  => [
                'required', 'string', 'max:50',
                Rule::unique('section')->where(function ($query) {
                    return $query
                        ->where('programId',    $this->programId)
                        ->where('academicYear', $this->academicYear)
                        ->where('semester',     $this->semester);
                })->ignore($sectionId),
            ],
            'academicYear' => ['required', 'string', 'max:20'],
            'yearLevel'    => ['required', 'integer', 'min:1', 'max:4'],
            'semester'     => ['required', 'integer', 'in:1,2'],
        ];
    }
}
