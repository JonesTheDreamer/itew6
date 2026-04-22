<?php

namespace App\Repositories;

use App\Models\ExtraCurricular;
use Illuminate\Database\Eloquent\Collection;

class ExtraCurricularRepository extends BaseRepository
{
    public function __construct() { parent::__construct(new ExtraCurricular()); }

    public function getByStudent(int $studentId): Collection
    {
        return ExtraCurricular::where('studentId', $studentId)->get();
    }
}
