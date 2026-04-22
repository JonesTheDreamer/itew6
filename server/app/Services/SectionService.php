<?php

namespace App\Services;

use App\Models\Section;
use App\Repositories\SectionRepository;

class SectionService
{
    public function __construct(private SectionRepository $repo)
    {
    }

    public function getAll(array $filters = []): mixed
    {
        if (!empty($filters['programId']) || !empty($filters['academicYear']) || !empty($filters['semester'])) {
            return $this->repo->getFiltered($filters);
        }
        return $this->repo->getAll();
    }

    public function getById(int $id): ?Section
    {
        return $this->repo->getById($id);
    }

    public function create(array $data): Section
    {
        $section = $this->repo->create([
            'programId' => $data['programId'],
            'sectionName' => $data['sectionName'],
            'academicYear' => $data['academicYear'],
            'yearLevel' => $data['yearLevel'],
            'semester' => $data['semester'],
        ]);
        return $this->repo->getById($section->id);
    }

    public function update(int $id, array $data): ?Section
    {
        $section = $this->repo->getById($id);
        if (!$section)
            return null;

        $this->repo->update($id, [
            'programId' => $data['programId'] ?? $section->programId,
            'sectionName' => $data['sectionName'] ?? $section->sectionName,
            'academicYear' => $data['academicYear'] ?? $section->academicYear,
            'yearLevel' => $data['yearLevel'] ?? $section->yearLevel,
            'semester' => $data['semester'] ?? $section->semester,
        ]);

        return $this->repo->getById($id);
    }

    public function delete(int $id): bool
    {
        return $this->repo->delete($id);
    }
}