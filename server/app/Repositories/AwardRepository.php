<?php

namespace App\Repositories;

use App\Models\Award;
use App\Models\Student;
use Illuminate\Database\Eloquent\Collection;

class AwardRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(new Award());
    }

    public function getByUser(int $userId): Collection
    {
        return Award::where('userId', $userId)->get();
    }

    public function getByStudent(int $studentId): Collection
    {
        $student = Student::find($studentId);
        if (!$student)
            return collect();
        return $this->getByUser($student->userId);
    }
}