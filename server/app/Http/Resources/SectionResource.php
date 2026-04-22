<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SectionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'programId'    => $this->programId,
            'programName'  => $this->program?->name,
            'sectionName'  => $this->sectionName,
            'academicYear' => $this->academicYear,
            'yearLevel'    => $this->yearLevel,
            'semester'     => $this->semester,
            'created_at'   => $this->created_at,
            'updated_at'   => $this->updated_at,
        ];
    }
}
