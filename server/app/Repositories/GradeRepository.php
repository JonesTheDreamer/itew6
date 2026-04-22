<?php

namespace App\Repositories;

use App\Models\Grade;
use Illuminate\Database\Eloquent\Collection;

class GradeRepository extends BaseRepository
{
    public function __construct() { parent::__construct(new Grade()); }

    public function getByStudent(int $studentId): Collection
    {
        return Grade::with('course', 'section')
            ->where('studentId', $studentId)
            ->orderBy('academicYear')
            ->orderBy('semester')
            ->orderByRaw("FIELD(term, 'preliminary', 'midterm', 'finals')")
            ->get();
    }

    public function getBySection(int $sectionId): Collection
    {
        return Grade::with('course')
            ->where('sectionId', $sectionId)
            ->get();
    }

    public function findDuplicate(int $studentId, int $courseId, string $academicYear, int $semester, string $term): ?Grade
    {
        return Grade::where('studentId', $studentId)
            ->where('courseId', $courseId)
            ->where('academicYear', $academicYear)
            ->where('semester', $semester)
            ->where('term', $term)
            ->first();
    }

    public function getBySemester(int $studentId, string $academicYear, int $semester): Collection
    {
        return Grade::where('studentId', $studentId)
            ->where('academicYear', $academicYear)
            ->where('semester', $semester)
            ->get();
    }
}
