<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrganizationRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $isUpdate = $this->isMethod('PUT') || $this->isMethod('PATCH');

        return [
            'collegeId'               => ['nullable', 'integer', 'exists:college,id'],
            'organizationName'        => [$isUpdate ? 'sometimes' : 'required', 'string', 'max:150'],
            'organizationDescription' => ['nullable', 'string'],
            'dateCreated'             => ['nullable', 'date'],
            'isActive'                => ['nullable', 'boolean'],
        ];
    }
}
