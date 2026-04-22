<?php

namespace App\Repositories;

use App\Models\Course;
use Illuminate\Database\Eloquent\Collection;

class CourseRepository extends BaseRepository
{
    public function __construct() { parent::__construct(new Course()); }

    public function getAll(): Collection
    {
        return Course::orderBy('yearLevel')->orderBy('semester')->get();
    }

    public function getById(int $id): ?Course
    {
        return Course::find($id);
    }

    public function getByCurriculum(int $curriculumId): Collection
    {
        return Course::where('curriculumId', $curriculumId)
            ->orderBy('yearLevel')
            ->orderBy('semester')
            ->get();
    }
}
