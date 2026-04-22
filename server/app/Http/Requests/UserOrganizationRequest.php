<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UserOrganizationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isUpdate = $this->isMethod('PUT') || $this->isMethod('PATCH');

        return [
            'userId'         => [$isUpdate ? 'sometimes' : 'required', 'integer', 'exists:user,id'],
            'organizationId' => [$isUpdate ? 'sometimes' : 'required', 'integer', 'exists:organization,id'],
            'role'           => ['nullable', 'string', 'max:100'],
            'dateJoined'     => ['nullable', 'date'],
            'dateLeft'       => ['nullable', 'date'],
        ];
    }
}