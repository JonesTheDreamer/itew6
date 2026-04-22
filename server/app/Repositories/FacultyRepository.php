<?php

namespace App\Repositories;

use App\Models\Faculty;
use Illuminate\Database\Eloquent\Collection;

class FacultyRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(new Faculty());
    }

    public function getAll(): Collection
    {
        return Faculty::with('user')->get();
    }

    public function getById(int $id): ?Faculty
    {
        return Faculty::with([
            'user',
            'user.awards',
            'user.eduBackground',
            'jobHistory',
        ])->find($id);
    }

    public function getByDepartment(string $department): Collection
    {
        return Faculty::with('user')->where('department', $department)->get();
    }

    public function getStats(): array
    {
        $faculty = Faculty::all();
        $avg = $faculty->count() > 0 ? round($faculty->avg('monthlyIncome'), 2) : 0;

        return [
            'totalFaculty' => $faculty->count(),
            'avgMonthlyIncome' => $avg,
        ];
    }
}