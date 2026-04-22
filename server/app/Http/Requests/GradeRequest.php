<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GradeRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'studentId'    => ['required', 'integer', 'exists:student,id'],
            'sectionId'    => ['required', 'integer', 'exists:section,id'],
            'courseId'     => ['required', 'integer', 'exists:courses,id'],
            'academicYear' => ['required', 'string', 'max:20'],
            'semester'     => ['required', 'integer', 'in:1,2'],
            'term'         => ['required', 'in:preliminary,midterm,finals'],
            'grade'        => ['required', 'numeric', 'min:1.0', 'max:5.0'],
            'remarks'      => ['nullable', 'in:passed,failed,dropped,incomplete'],
        ];
    }
}
