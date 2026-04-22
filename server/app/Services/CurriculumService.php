<?php

namespace App\Services;

use App\Models\Curriculum;
use App\Repositories\CurriculumRepository;
use Illuminate\Support\Facades\DB;

class CurriculumService
{
    public function __construct(private CurriculumRepository $repo)
    {
    }

    public function getAll(array $filters = []): mixed
    {
        if (!empty($filters['programId'])) {
            return $this->repo->getByProgram((int) $filters['programId']);
        }
        return $this->repo->getAll();
    }

    public function getById(int $id): ?Curriculum
    {
        return $this->repo->getById($id);
    }

    public function create(array $data): Curriculum
    {
        $curriculum = $this->repo->create([
            'programId' => $data['programId'],
            'name' => $data['name'],
            'effectiveYear' => $data['effectiveYear'],
            'isActive' => $data['isActive'] ?? false,
            'description' => $data['description'] ?? null,
        ]);

        return $this->repo->getById($curriculum->id);
    }

    public function update(int $id, array $data): ?Curriculum
    {
        $curriculum = $this->repo->getById($id);
        if (!$curriculum)
            return null;

        $this->repo->update($id, [
            'programId' => $data['programId'] ?? $curriculum->programId,
            'name' => $data['name'] ?? $curriculum->name,
            'effectiveYear' => $data['effectiveYear'] ?? $curriculum->effectiveYear,
            'isActive' => $data['isActive'] ?? $curriculum->isActive,
            'description' => $data['description'] ?? $curriculum->description,
        ]);

        return $this->repo->getById($id);
    }

    public function delete(int $id): bool
    {
        return $this->repo->delete($id);
    }

    public function activate(int $id): ?Curriculum
    {
        $curriculum = $this->repo->getById($id);
        if (!$curriculum)
            return null;

        DB::transaction(function () use ($id, $curriculum) {
            $this->repo->deactivateAllForProgram($curriculum->programId);
            $this->repo->update($id, ['isActive' => true]);
        });

        return $this->repo->getById($id);
    }
}