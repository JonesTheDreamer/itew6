<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GradeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'studentId'    => $this->studentId,
            'sectionId'    => $this->sectionId,
            'courseId'     => $this->courseId,
            'courseCode'   => $this->course?->courseCode,
            'courseName'   => $this->course?->courseName,
            'academicYear' => $this->academicYear,
            'semester'     => $this->semester,
            'term'         => $this->term,
            'grade'        => $this->grade !== null ? round((float) $this->grade, 2) : null,
            'remarks'      => $this->remarks,
            'created_at'   => $this->created_at,
            'updated_at'   => $this->updated_at,
        ];
    }
}
