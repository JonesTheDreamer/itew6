<?php

namespace App\Repositories;

use App\Models\JobHistory;
use Illuminate\Database\Eloquent\Collection;

class JobHistoryRepository extends BaseRepository
{
    public function __construct() { parent::__construct(new JobHistory()); }

    public function getByFaculty(int $facultyId): Collection
    {
        return JobHistory::where('facultyId', $facultyId)->get();
    }
}
