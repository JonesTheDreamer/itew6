<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FacultyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $user = $this->user;
        $isSingle = $request->route('id') !== null;

        $base = [
            'id' => $this->id,
            'userId' => $this->userId,
            'firstName' => $user?->firstName,
            'lastName' => $user?->lastName,
            'middleName' => $user?->middleName,
            'email' => $user?->email,
            'birthProvince' => $user?->birthProvince,  // ← add this
            'postalCode' => $user?->postalCode,        // ← add this
            'mobileNumber' => $user?->mobileNumber,
            'age' => $user?->age,
            'birthDate' => $user?->birthDate,
            'city' => $user?->city,
            'province' => $user?->province,
            'position' => $this->position,
            'employmentDate' => $this->employmentDate,
            'employmentType' => $this->employmentType,
            'monthlyIncome' => $this->monthlyIncome,
            'department' => $this->department,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];

        if ($isSingle) {
            $base['awards'] = $user?->awards ?? [];
            $base['eduBackground'] = $user?->eduBackground ?? [];
            $base['jobHistory'] = $this->jobHistory ?? [];
        }

        return $base;
    }
}
