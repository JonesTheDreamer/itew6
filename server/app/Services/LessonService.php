<?php

namespace App\Services;

use App\Models\Lesson;
use App\Repositories\LessonRepository;

class LessonService
{
    public function __construct(private LessonRepository $repo)
    {
    }

    public function getAll(array $filters = []): mixed
    {
        if (!empty($filters['courseId'])) {
            return $this->repo->getByCourse((int) $filters['courseId']);
        }
        return $this->repo->getAll();
    }

    public function getById(int $id): ?Lesson
    {
        return $this->repo->getById($id);
    }

    public function create(array $data): Lesson
    {
        return $this->repo->create([
            'courseId' => $data['courseId'],
            'lessonOrder' => $data['lessonOrder'],
            'lessonTitle' => $data['lessonTitle'],
            'description' => $data['description'] ?? null,
        ]);
    }

    public function update(int $id, array $data): ?Lesson
    {
        $lesson = $this->repo->getById($id);
        if (!$lesson)
            return null;

        $this->repo->update($id, [
            'courseId' => $data['courseId'] ?? $lesson->courseId,
            'lessonOrder' => $data['lessonOrder'] ?? $lesson->lessonOrder,
            'lessonTitle' => $data['lessonTitle'] ?? $lesson->lessonTitle,
            'description' => $data['description'] ?? $lesson->description,
        ]);

        return $this->repo->getById($id);
    }

    public function delete(int $id): bool
    {
        return $this->repo->delete($id);
    }
}