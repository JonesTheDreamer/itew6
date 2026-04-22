<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StudentRequest;
use App\Http\Requests\StudentStatusRequest;
use App\Http\Resources\StudentResource;
use App\Services\StudentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function __construct(private StudentService $service) {}

    public function index(Request $request): JsonResponse
    {
        $students = $this->service->getAll($request->only(['programId', 'status', 'skillId']));
        return response()->json(['data' => StudentResource::collection($students), 'message' => 'success']);
    }

    public function stats(): JsonResponse
    {
        return response()->json(['data' => $this->service->getStats(), 'message' => 'success']);
    }

    public function store(StudentRequest $request): JsonResponse
    {
        $student = $this->service->createWithUser($request->validated());
        return response()->json(['data' => new StudentResource($student), 'message' => 'Student created'], 201);
    }

    public function show(int $id): JsonResponse
    {
        $student = $this->service->getById($id);
        if (!$student) return response()->json(['message' => 'Not found'], 404);
        return response()->json(['data' => new StudentResource($student), 'message' => 'success']);
    }

    public function update(StudentRequest $request, int $id): JsonResponse
    {
        $student = $this->service->updateWithUser($id, $request->validated());
        if (!$student) return response()->json(['message' => 'Not found'], 404);
        return response()->json(['data' => new StudentResource($student), 'message' => 'Student updated']);
    }

    public function destroy(int $id): JsonResponse
    {
        $deleted = $this->service->delete($id);
        if (!$deleted) return response()->json(['message' => 'Not found'], 404);
        return response()->json(['message' => 'Student deleted']);
    }

    public function updateStatus(StudentStatusRequest $request, int $id): JsonResponse
    {
        $student = $this->service->updateStatus($id, $request->status);
        if (!$student) return response()->json(['message' => 'Not found'], 404);
        return response()->json(['data' => new StudentResource($student), 'message' => 'Status updated']);
    }
}
