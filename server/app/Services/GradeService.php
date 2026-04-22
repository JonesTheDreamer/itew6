<?php

namespace App\Services;

use App\Models\Grade;
use App\Repositories\GradeRepository;
use Illuminate\Validation\ValidationException;

class GradeService
{
    public function __construct(private GradeRepository $repo)
    {
    }

    public function getAll(array $filters = []): mixed
    {
        if (!empty($filters['studentId'])) {
            return $this->repo->getByStudent((int) $filters['studentId']);
        }
        if (!empty($filters['sectionId'])) {
            return $this->repo->getBySection((int) $filters['sectionId']);
        }
        return $this->repo->getAll();
    }

    public function getById(int $id): ?Grade
    {
        return $this->repo->getById($id);
    }

    public function create(array $data): Grade
    {
        $exists = $this->repo->findDuplicate(
            $data['studentId'],
            $data['courseId'],
            $data['academicYear'],
            $data['semester'],
            $data['term']
        );

        if ($exists) {
            throw ValidationException::withMessages([
                'term' => ['A grade already exists for this student, course, and term.'],
            ]);
        }

        return $this->repo->create([
            'studentId' => $data['studentId'],
            'sectionId' => $data['sectionId'],
            'courseId' => $data['courseId'],
            'academicYear' => $data['academicYear'],
            'semester' => $data['semester'],
            'term' => $data['term'],
            'grade' => $data['grade'],
            'remarks' => $data['remarks'] ?? null,
        ]);
    }

    public function update(int $id, array $data): ?Grade
    {
        $grade = $this->repo->getById($id);
        if (!$grade)
            return null;

        $this->repo->update($id, [
            'studentId' => $data['studentId'] ?? $grade->studentId,
            'sectionId' => $data['sectionId'] ?? $grade->sectionId,
            'courseId' => $data['courseId'] ?? $grade->courseId,
            'academicYear' => $data['academicYear'] ?? $grade->academicYear,
            'semester' => $data['semester'] ?? $grade->semester,
            'term' => $data['term'] ?? $grade->term,
            'grade' => $data['grade'] ?? $grade->grade,
            'remarks' => $data['remarks'] ?? $grade->remarks,
        ]);

        return Grade::with('course', 'section')->find($id);
    }

    public function delete(int $id): bool
    {
        return $this->repo->delete($id);
    }

    /**
     * GPA = average of per-course semester grades.
     * Per-course grade = (Prelim × 0.30) + (Midterm × 0.30) + (Finals × 0.40)
     */
    public function computeSemesterGpa(int $studentId, string $academicYear, int $semester): ?float
    {
        $grades = $this->repo->getBySemester($studentId, $academicYear, $semester);
        $byCourse = $grades->groupBy('courseId');

        $courseGpas = [];
        foreach ($byCourse as $courseGrades) {
            $prelim = $courseGrades->firstWhere('term', 'preliminary')?->grade;
            $midterm = $courseGrades->firstWhere('term', 'midterm')?->grade;
            $finals = $courseGrades->firstWhere('term', 'finals')?->grade;

            if ($prelim !== null && $midterm !== null && $finals !== null) {
                $courseGpas[] = round(($prelim * 0.30) + ($midterm * 0.30) + ($finals * 0.40), 2);
            }
        }

        if (empty($courseGpas))
            return null;

        return round(array_sum($courseGpas) / count($courseGpas), 2);
    }
}