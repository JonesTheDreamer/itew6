<?php

namespace App\Repositories;

use App\Models\Student;
use Illuminate\Database\Eloquent\Collection;

class StudentRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(new Student());
    }

    public function getAll(): Collection
    {
        return Student::with(['user', 'program'])->get();
    }

    public function getById(int $id): ?Student
    {
        return Student::with([
            'user',
            'user.awards',
            'program',
            'grades',
            'extraCurricular',
            'skills',
            'user.eduBackground'
        ])->find($id);
    }

    public function getByProgram(int $programId): Collection
    {
        return Student::with(['user', 'program'])->where('programId', $programId)->get();
    }

    public function getByStatus(string $status): Collection
    {
        return Student::with(['user', 'program'])->where('status', $status)->get();
    }

    public function getBySkill(int $skillId): Collection
    {
        return Student::with(['user', 'program'])
            ->whereHas('skills', fn($q) => $q->where('skills.id', $skillId))
            ->get();
    }

    public function getByProgramAndSkill(int $programId, int $skillId): Collection
    {
        return Student::with(['user', 'program'])
            ->where('programId', $programId)
            ->whereHas('skills', fn($q) => $q->where('skills.id', $skillId))
            ->get();
    }

    public function getStats(): array
    {
        $students = Student::all();
        $active = $students->where('status', 'Active')->count();
        $avgGPA = $students->count() > 0 ? round($students->avg('gpa'), 2) : 0;

        return [
            'totalStudents' => $students->count(),
            'activeStudents' => $active,
            'avgGPA' => $avgGPA,
        ];
    }
}