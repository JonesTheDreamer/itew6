<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StudentProgramRequest;
use App\Repositories\StudentProgramRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StudentProgramController extends Controller
{
    public function __construct(private StudentProgramRepository $repo) {}

    public function index(Request $request): JsonResponse
    {
        if ($request->filled('studentId')) {
            $data = $this->repo->getByStudent((int) $request->studentId);
        } elseif ($request->filled('programId')) {
            $data = $this->repo->getByProgram((int) $request->programId);
        } else {
            $data = $this->repo->getAll();
        }

        return response()->json(['data' => $data, 'message' => 'success']);
    }

    public function store(StudentProgramRequest $request): JsonResponse
    {
        $item = $this->repo->create($request->validated());
        return response()->json(['data' => $item, 'message' => 'Enrolled in program'], 201);
    }

    public function destroy(int $id): JsonResponse
    {
        if (!$this->repo->delete($id)) {
            return response()->json(['message' => 'Not found'], 404);
        }
        return response()->json(['message' => 'Removed from program']);
    }
}
