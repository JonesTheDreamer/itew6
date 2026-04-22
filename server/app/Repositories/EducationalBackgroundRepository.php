<?php

namespace App\Repositories;

use App\Models\EducationalBackground;
use App\Models\Student;
use Illuminate\Database\Eloquent\Collection;

class EducationalBackgroundRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(new EducationalBackground());
    }

    public function getByUser(int $userId): Collection
    {
        return EducationalBackground::where('userId', $userId)->get();
    }

    public function getByStudent(int $studentId): Collection
    {
        $student = Student::find($studentId);
        if (!$student)
            return collect();
        return $this->getByUser($student->userId);
    }
}