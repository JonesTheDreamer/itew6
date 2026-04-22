<?php

namespace App\Repositories;

use App\Models\Schedule;
use App\Models\StudentSection;
use Illuminate\Database\Eloquent\Collection;

class ScheduleRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(new Schedule());
    }

    public function getBySection(int $sectionId): Collection
    {
        return Schedule::where('sectionId', $sectionId)->get();
    }

    public function getByStudent(int $studentId): Collection
    {
        $sectionIds = StudentSection::where('studentId', $studentId)->pluck('sectionId');
        return Schedule::whereIn('sectionId', $sectionIds)->get();
    }
}