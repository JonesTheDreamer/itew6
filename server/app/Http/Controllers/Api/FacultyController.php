<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\FacultyRequest;
use App\Http\Resources\FacultyResource;
use App\Services\FacultyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FacultyController extends Controller
{
    public function __construct(private FacultyService $service)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $faculty = $this->service->getAll($request->only(['department']));
        return response()->json(['data' => FacultyResource::collection($faculty), 'message' => 'success']);
    }

    public function stats(): JsonResponse
    {
        return response()->json(['data' => $this->service->getStats(), 'message' => 'success']);
    }

    public function store(FacultyRequest $request): JsonResponse
    {
        $faculty = $this->service->createWithUser($request->validated());
        return response()->json(['data' => new FacultyResource($faculty), 'message' => 'Faculty created'], 201);
    }

    public function show(int $id): JsonResponse
    {
        $faculty = $this->service->getById($id);
        if (!$faculty)
            return response()->json(['message' => 'Not found'], 404);
        return response()->json(['data' => new FacultyResource($faculty), 'message' => 'success']);
    }

    public function update(FacultyRequest $request, int $id): JsonResponse
    {
        $faculty = $this->service->updateWithUser($id, $request->validated());
        if (!$faculty)
            return response()->json(['message' => 'Not found'], 404);
        return response()->json(['data' => new FacultyResource($faculty), 'message' => 'Faculty updated']);
    }

    public function destroy(int $id): JsonResponse
    {
        $deleted = $this->service->delete($id);
        if (!$deleted)
            return response()->json(['message' => 'Not found'], 404);
        return response()->json(['message' => 'Faculty deleted']);
    }
}