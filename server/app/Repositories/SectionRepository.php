<?php

namespace App\Repositories;

use App\Models\Section;
use Illuminate\Database\Eloquent\Collection;

class SectionRepository extends BaseRepository
{
    public function __construct() { parent::__construct(new Section()); }

    public function getAll(): Collection
    {
        return Section::with('program')->get();
    }

    public function getById(int $id): ?Section
    {
        return Section::with('program')->find($id);
    }

    public function getFiltered(array $filters): Collection
    {
        $query = Section::with('program');

        if (!empty($filters['programId'])) {
            $query->where('programId', $filters['programId']);
        }
        if (!empty($filters['academicYear'])) {
            $query->where('academicYear', $filters['academicYear']);
        }
        if (!empty($filters['semester'])) {
            $query->where('semester', $filters['semester']);
        }

        return $query->orderBy('yearLevel')->orderBy('sectionName')->get();
    }
}
