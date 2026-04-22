<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LessonRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'courseId'    => ['required', 'integer', 'exists:courses,id'],
            'lessonOrder' => ['required', 'integer', 'min:1'],
            'lessonTitle' => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string'],
        ];
    }
}
