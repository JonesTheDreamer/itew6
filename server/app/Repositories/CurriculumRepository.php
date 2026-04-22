<?php

namespace App\Repositories;

use App\Models\Curriculum;
use Illuminate\Database\Eloquent\Collection;

class CurriculumRepository extends BaseRepository
{
    public function __construct() { parent::__construct(new Curriculum()); }

    public function getAll(): Collection
    {
        return Curriculum::with('program')->get();
    }

    public function getById(int $id): ?Curriculum
    {
        return Curriculum::with('program')->find($id);
    }

    public function getByProgram(int $programId): Collection
    {
        return Curriculum::with('program')->where('programId', $programId)->get();
    }

    public function deactivateAllForProgram(int $programId): void
    {
        Curriculum::where('programId', $programId)->update(['isActive' => false]);
    }
}
