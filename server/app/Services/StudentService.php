<?php

namespace App\Services;

use App\Models\Student;
use App\Repositories\UserRepository;
use App\Repositories\StudentRepository;
use Illuminate\Support\Facades\DB;
use Hash;

class StudentService
{
    public function __construct(
        private StudentRepository $studentRepo,
        private UserRepository $userRepo,
    ) {
    }

    public function getAll(array $filters = []): mixed
    {
        if (!empty($filters['programId']) && !empty($filters['skillId'])) {
            return $this->studentRepo->getByProgramAndSkill(
                (int) $filters['programId'],
                (int) $filters['skillId']
            );
        }
        if (!empty($filters['skillId'])) {
            return $this->studentRepo->getBySkill((int) $filters['skillId']);
        }
        if (!empty($filters['programId'])) {
            return $this->studentRepo->getByProgram((int) $filters['programId']);
        }
        if (!empty($filters['status'])) {
            return $this->studentRepo->getByStatus($filters['status']);
        }
        return $this->studentRepo->getAll();
    }

    public function getById(int $id): ?Student
    {
        return $this->studentRepo->getById($id);
    }

    public function getStats(): array
    {
        return $this->studentRepo->getStats();
    }

    public function createWithUser(array $data): Student
    {
        return DB::transaction(function () use ($data) {
            try {
                $studentId = $data['studentId'] ?? ('CSC-' . date('Y') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT));
                $user = $this->userRepo->create([
                    'firstName' => $data['firstName'],
                    'lastName' => $data['lastName'],
                    'middleName' => $data['middleName'] ?? null,
                    'age' => $data['age'] ?? null,
                    'birthDate' => $data['birthDate'] ?? null,
                    'birthProvince' => $data['birthProvince'] ?? null,
                    'mobileNumber' => $data['mobileNumber'] ?? null,
                    'email' => $data['email'],
                    'city' => $data['city'] ?? null,
                    'province' => $data['province'] ?? null,
                    'password' => Hash::make('password - ' . $studentId),
                ]);



                $student = $this->studentRepo->create([
                    'userId' => $user->id,
                    'studentId' => $studentId,
                    'programId' => $data['programId'],
                    'yearLevel' => $data['yearLevel'] ?? 1,
                    'unitsTaken' => $data['unitsTaken'] ?? 0,
                    'unitsLeft' => $data['unitsLeft'] ?? 0,
                    'dateEnrolled' => $data['dateEnrolled'] ?? now()->toDateString(),
                    'dateGraduated' => $data['dateGraduated'] ?? null,
                    'dateDropped' => $data['dateDropped'] ?? null,
                    'gpa' => $data['gpa'] ?? null,
                    'status' => $data['status'] ?? 'Active',
                ]);

                if (!empty($data['skillIds']) && is_array($data['skillIds'])) {
                    $student->skills()->sync($data['skillIds']);
                }

                return $this->studentRepo->getById($student->id);
            } catch (\Throwable $th) {
                dd($th->getMessage());
            }

        });
    }

    public function updateWithUser(int $id, array $data): ?Student
    {
        return DB::transaction(function () use ($id, $data) {
            $student = $this->studentRepo->getById($id);
            if (!$student)
                return null;

            $this->userRepo->update($student->userId, [
                'firstName' => $data['firstName'],
                'lastName' => $data['lastName'],
                'middleName' => $data['middleName'] ?? null,
                'age' => $data['age'] ?? null,
                'birthDate' => $data['birthDate'] ?? null,
                'birthProvince' => $data['birthProvince'] ?? null,
                'mobileNumber' => $data['mobileNumber'] ?? null,
                'email' => $data['email'],
                'city' => $data['city'] ?? null,
                'province' => $data['province'] ?? null,
            ]);

            $this->studentRepo->update($id, [
                'programId' => $data['programId'],
                'yearLevel' => $data['yearLevel'] ?? $student->yearLevel,
                'unitsTaken' => $data['unitsTaken'] ?? $student->unitsTaken,
                'unitsLeft' => $data['unitsLeft'] ?? $student->unitsLeft,
                'dateEnrolled' => $data['dateEnrolled'] ?? $student->dateEnrolled,
                'dateGraduated' => $data['dateGraduated'] ?? $student->dateGraduated,
                'dateDropped' => $data['dateDropped'] ?? $student->dateDropped,
                'gpa' => $data['gpa'] ?? $student->gpa,
                'status' => $data['status'] ?? $student->status,
            ]);

            if (isset($data['skillIds']) && is_array($data['skillIds'])) {
                $student->skills()->sync($data['skillIds']);
            }

            return $this->studentRepo->getById($id);
        });
    }

    public function delete(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $student = $this->studentRepo->getById($id);
            if (!$student)
                return false;
            $userId = $student->userId;
            $this->studentRepo->delete($id);
            $this->userRepo->delete($userId);
            return true;
        });
    }

    public function updateStatus(int $id, string $status): ?Student
    {
        $student = $this->studentRepo->getById($id);
        if (!$student)
            return null;
        $this->studentRepo->update($id, ['status' => $status]);
        return $this->studentRepo->getById($id);
    }
}