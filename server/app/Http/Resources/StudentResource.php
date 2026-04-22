<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $user = $this->user;
        $isSingle = $request->route('id') !== null;

        $base = [
            'id' => $this->id,
            'studentId' => $this->studentId,
            'userId' => $this->userId,
            'firstName' => $user?->firstName,
            'lastName' => $user?->lastName,
            'middleName' => $user?->middleName,
            'email' => $user?->email,
            'mobileNumber' => $user?->mobileNumber,
            'age' => $user?->age,
            'birthDate' => $user?->birthDate,
            'birthProvince' => $user?->birthProvince,
            'city' => $user?->city,
            'province' => $user?->province,
            'programId' => $this->programId,
            'programName' => $this->program?->name,
            'yearLevel' => $this->yearLevel,
            'unitsTaken' => $this->unitsTaken,
            'unitsLeft' => $this->unitsLeft,
            'dateEnrolled' => $this->dateEnrolled,
            'dateGraduated' => $this->dateGraduated,
            'dateDropped' => $this->dateDropped,
            'gpa' => $this->gpa,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];

        if ($isSingle) {
            $base['awards'] = $user?->awards ?? [];
            $base['extraCurriculars'] = $this->extraCurricular ?? [];
            $base['skills'] = $this->skills ?? [];
            $base['grades'] = $this->grades()->with('course')->get();
            $base['educationalBackground'] = $user?->eduBackground ?? [];
        }

        return $base;
    }
}