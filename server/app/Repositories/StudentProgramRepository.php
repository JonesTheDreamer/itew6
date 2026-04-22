<?php

namespace App\Repositories;

use App\Models\StudentProgram;
use Illuminate\Database\Eloquent\Collection;

class StudentProgramRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(new StudentProgram());
    }

    public function getByStudent(int $studentId): Collection
    {
        return StudentProgram::with('program')
            ->where('studentId', $studentId)
            ->orderByDesc('dateEnrolled')
            ->get();
    }

    public function getByProgram(int $programId): Collection
    {
        return StudentProgram::with('student.user')
            ->where('programId', $programId)
            ->get();
    }
}