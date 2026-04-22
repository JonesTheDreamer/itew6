<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LessonRequest;
use App\Services\LessonService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LessonController extends Controller
{
    public function __construct(private LessonService $service) {}

    public function index(Request $request): JsonResponse
    {
        $data = $this->service->getAll($request->only(['courseId']));
        return response()->json(['data' => $data, 'message' => 'success']);
    }

    public function store(LessonRequest $request): JsonResponse
    {
        $lesson = $this->service->create($request->validated());
        return response()->json(['data' => $lesson, 'message' => 'Lesson created'], 201);
    }

    public function show(int $id): JsonResponse
    {
        $lesson = $this->service->getById($id);
        if (!$lesson) return response()->json(['message' => 'Not found'], 404);
        return response()->json(['data' => $lesson, 'message' => 'success']);
    }

    public function update(LessonRequest $request, int $id): JsonResponse
    {
        $lesson = $this->service->update($id, $request->validated());
        if (!$lesson) return response()->json(['message' => 'Not found'], 404);
        return response()->json(['data' => $lesson, 'message' => 'Lesson updated']);
    }

    public function destroy(int $id): JsonResponse
    {
        if (!$this->service->delete($id)) {
            return response()->json(['message' => 'Not found'], 404);
        }
        return response()->json(['message' => 'Lesson deleted']);
    }
}
