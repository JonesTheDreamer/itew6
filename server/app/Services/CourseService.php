<?php

namespace App\Services;

use App\Models\Course;
use App\Repositories\CourseRepository;

class CourseService
{
    public function __construct(private CourseRepository $repo)
    {
    }

    public function getAll(array $filters = []): mixed
    {
        if (!empty($filters['curriculumId'])) {
            return $this->repo->getByCurriculum((int) $filters['curriculumId']);
        }
        return $this->repo->getAll();
    }

    public function getById(int $id): ?Course
    {
        return $this->repo->getById($id);
    }

    public function create(array $data): Course
    {
        return $this->repo->create([
            'curriculumId' => $data['curriculumId'],
            'courseCode' => $data['courseCode'],
            'courseName' => $data['courseName'],
            'units' => $data['units'],
            'labUnits' => $data['labUnits'] ?? null,
            'yearLevel' => $data['yearLevel'],
            'semester' => $data['semester'],
            'courseType' => $data['courseType'],
            'isRequired' => $data['isRequired'] ?? false,
        ]);
    }

    public function update(int $id, array $data): ?Course
    {
        // PUT replacement — all fields are always present after validation.
        $course = $this->repo->getById($id);
        if (!$course)
            return null;

        $this->repo->update($id, [
            'curriculumId' => $data['curriculumId'] ?? $course->curriculumId,
            'courseCode' => $data['courseCode'] ?? $course->courseCode,
            'courseName' => $data['courseName'] ?? $course->courseName,
            'units' => $data['units'] ?? $course->units,
            'labUnits' => $data['labUnits'] ?? $course->labUnits,
            'yearLevel' => $data['yearLevel'] ?? $course->yearLevel,
            'semester' => $data['semester'] ?? $course->semester,
            'courseType' => $data['courseType'] ?? $course->courseType,
            'isRequired' => $data['isRequired'] ?? $course->isRequired,
        ]);

        return $this->repo->getById($id);
    }

    public function delete(int $id): bool
    {
        return $this->repo->delete($id);
    }
}