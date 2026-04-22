<?php

namespace App\Services;

use App\Models\Faculty;
use App\Repositories\UserRepository;
use App\Repositories\FacultyRepository;
use Illuminate\Support\Facades\DB;

class FacultyService
{
    public function __construct(
        private FacultyRepository $facultyRepo,
        private UserRepository $userRepo,
    ) {
    }

    public function getAll(array $filters = []): mixed
    {
        if (!empty($filters['department'])) {
            return $this->facultyRepo->getByDepartment($filters['department']);
        }
        return $this->facultyRepo->getAll();
    }

    public function getById(int $id): ?Faculty
    {
        return $this->facultyRepo->getById($id);
    }

    public function getStats(): array
    {
        return $this->facultyRepo->getStats();
    }

    public function createWithUser(array $data): Faculty
    {
        return DB::transaction(function () use ($data) {
            $user = $this->userRepo->create([
                'firstName' => $data['firstName'],
                'lastName' => $data['lastName'],
                'middleName' => $data['middleName'] ?? null,
                'age' => $data['age'] ?? null,
                'birthDate' => $data['birthDate'] ?? null,
                'mobileNumber' => $data['mobileNumber'] ?? null,
                'email' => $data['email'],
                'city' => $data['city'] ?? null,
                'province' => $data['province'] ?? null,
            ]);

            $faculty = $this->facultyRepo->create([
                'userId' => $user->id,
                'position' => $data['position'] ?? null,
                'employmentDate' => $data['employmentDate'] ?? null,
                'employmentType' => $data['employmentType'] ?? null,
                'monthlyIncome' => $data['monthlyIncome'] ?? null,
                'department' => $data['department'] ?? null,
            ]);

            return $this->facultyRepo->getById($faculty->id);
        });
    }

    public function updateWithUser(int $id, array $data): ?Faculty
    {
        return DB::transaction(function () use ($id, $data) {
            $faculty = $this->facultyRepo->getById($id);
            if (!$faculty)
                return null;

            $this->userRepo->update($faculty->userId, [
                'firstName' => $data['firstName'],
                'lastName' => $data['lastName'],
                'middleName' => $data['middleName'] ?? null,
                'age' => $data['age'] ?? null,
                'birthDate' => $data['birthDate'] ?? null,
                'mobileNumber' => $data['mobileNumber'] ?? null,
                'email' => $data['email'],
                'city' => $data['city'] ?? null,
                'province' => $data['province'] ?? null,
            ]);

            $this->facultyRepo->update($id, [
                'position' => $data['position'] ?? $faculty->position,
                'employmentDate' => $data['employmentDate'] ?? $faculty->employmentDate,
                'employmentType' => $data['employmentType'] ?? $faculty->employmentType,
                'monthlyIncome' => $data['monthlyIncome'] ?? $faculty->monthlyIncome,
                'department' => $data['department'] ?? $faculty->department,
            ]);

            return $this->facultyRepo->getById($id);
        });
    }

    public function delete(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $faculty = $this->facultyRepo->getById($id);
            if (!$faculty)
                return false;
            $userId = $faculty->userId;
            $this->facultyRepo->delete($id);
            $this->userRepo->delete($userId);
            return true;
        });
    }
}