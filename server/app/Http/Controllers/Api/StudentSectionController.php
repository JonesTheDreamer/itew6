<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StudentSectionRequest;
use App\Repositories\StudentSectionRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class StudentSectionController extends Controller
{
    public function __construct(private StudentSectionRepository $repo) {}

    public function index(Request $request): JsonResponse
    {
        if ($request->filled('studentId')) {
            $data = $this->repo->getByStudent((int) $request->studentId);
        } elseif ($request->filled('sectionId')) {
            $data = $this->repo->getBySection((int) $request->sectionId);
        } else {
            $data = $this->repo->getAll();
        }

        return response()->json(['data' => $data, 'message' => 'success']);
    }

    public function store(StudentSectionRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $exists = $this->repo->findDuplicate(
            $validated['studentId'],
            $validated['sectionId'],
            $validated['academicYear'],
            $validated['semester']
        );

        if ($exists) {
            throw ValidationException::withMessages([
                'sectionId' => ['Student is already enrolled in this section for the given academic year and semester.'],
            ]);
        }

        $item = $this->repo->create($validated);
        return response()->json(['data' => $item, 'message' => 'Enrolled in section'], 201);
    }

    public function destroy(int $id): JsonResponse
    {
        if (!$this->repo->delete($id)) {
            return response()->json(['message' => 'Not found'], 404);
        }
        return response()->json(['message' => 'Removed from section']);
    }
}
