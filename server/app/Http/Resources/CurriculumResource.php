<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CurriculumResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'programId'     => $this->programId,
            'programName'   => $this->program?->programName,
            'programCode'   => $this->program?->programCode,
            'name'          => $this->name,
            'effectiveYear' => $this->effectiveYear,
            'isActive'      => (bool) $this->isActive,
            'description'   => $this->description,
            'created_at'    => $this->created_at,
            'updated_at'    => $this->updated_at,
        ];
    }
}
