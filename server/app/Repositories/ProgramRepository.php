<?php

namespace App\Repositories;

use App\Models\Program;
use Illuminate\Database\Eloquent\Collection;

class ProgramRepository extends BaseRepository
{
    public function __construct() { parent::__construct(new Program()); }

    public function getByCollege(int $collegeId): Collection
    {
        return Program::where('collegeId', $collegeId)->get();
    }
}
