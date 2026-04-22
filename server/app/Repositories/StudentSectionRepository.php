<?php

namespace App\Repositories;

use App\Models\StudentSection;
use Illuminate\Database\Eloquent\Collection;

class StudentSectionRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(new StudentSection());
    }

    public function getByStudent(int $studentId): Collection
    {
        return StudentSection::with('section.program')
            ->where('studentId', $studentId)
            ->orderBy('academicYear')
            ->orderBy('semester')
            ->get();
    }

    public function getBySection(int $sectionId): Collection
    {
        return StudentSection::with('student.user')
            ->where('sectionId', $sectionId)
            ->get();
    }

    public function findDuplicate(int $studentId, int $sectionId, string $academicYear, int $semester): ?StudentSection
    {
        return StudentSection::where('studentId', $studentId)
            ->where('sectionId', $sectionId)
            ->where('academicYear', $academicYear)
            ->where('semester', $semester)
            ->first();
    }
}