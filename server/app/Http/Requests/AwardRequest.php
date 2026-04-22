<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AwardRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'userId' => ['required', 'integer', 'exists:user,id'],
            'title' => ['required', 'string', 'max:200'],
            'awardingDate' => ['nullable', 'date'],
            'awardingOrganization' => ['nullable', 'string', 'max:150'],
            'awardingLocation' => ['nullable', 'string', 'max:150'],
        ];
    }
}