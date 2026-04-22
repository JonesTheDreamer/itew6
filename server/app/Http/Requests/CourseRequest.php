<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CourseRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $courseId = $this->route('id');

        return [
            'curriculumId' => ['required', 'integer', 'exists:curriculum,id'],
            'courseCode'   => ['required', 'string', 'max:20', 'unique:courses,courseCode,' . $courseId],
            'courseName'   => ['required', 'string', 'max:150'],
            'units'        => ['required', 'integer', 'min:0'],
            'labUnits'     => ['nullable', 'integer', 'min:0'],
            'yearLevel'    => ['required', 'integer', 'min:1', 'max:4'],
            'semester'     => ['required', 'integer', 'in:1,2'],
            'courseType'   => ['required', 'in:lecture,lecture_lab'],
            'isRequired'   => ['sometimes', 'boolean'],
        ];
    }
}
