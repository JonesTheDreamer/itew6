<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CourseRequest;
use App\Http\Resources\CourseResource;
use App\Services\CourseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function __construct(private CourseService $service)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $data = $this->service->getAll($request->only(['curriculumId']));
        return response()->json(['data' => CourseResource::collection($data), 'message' => 'success']);
    }

    public function store(CourseRequest $request): JsonResponse
    {
        $course = $this->service->create($request->validated());
        return response()->json(['data' => new CourseResource($course), 'message' => 'Course created'], 201);
    }

    public function show(int $id): JsonResponse
    {
        $course = $this->service->getById($id);
        if (!$course)
            return response()->json(['message' => 'Not found'], 404);
        return response()->json(['data' => new CourseResource($course), 'message' => 'success']);
    }

    public function update(CourseRequest $request, int $id): JsonResponse
    {
        $course = $this->service->update($id, $request->validated());
        if (!$course)
            return response()->json(['message' => 'Not found'], 404);
        return response()->json(['data' => new CourseResource($course), 'message' => 'Course updated']);
    }

    public function destroy(int $id): JsonResponse
    {
        if (!$this->service->delete($id)) {
            return response()->json(['message' => 'Not found'], 404);
        }
        return response()->json(['message' => 'Course deleted']);
    }
}