<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'curriculumId' => $this->curriculumId,
            'courseCode'   => $this->courseCode,
            'courseName'   => $this->courseName,
            'units'        => $this->units,
            'labUnits'     => $this->labUnits,
            'yearLevel'    => $this->yearLevel,
            'semester'     => $this->semester,
            'courseType'   => $this->courseType,
            'isRequired'   => (bool) $this->isRequired,
            'created_at'   => $this->created_at,
            'updated_at'   => $this->updated_at,
        ];
    }
}
