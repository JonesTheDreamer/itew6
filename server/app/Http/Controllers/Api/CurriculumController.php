<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CurriculumRequest;
use App\Http\Resources\CurriculumResource;
use App\Services\CurriculumService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CurriculumController extends Controller
{
    public function __construct(private CurriculumService $service) {}

    public function index(Request $request): JsonResponse
    {
        $data = $this->service->getAll($request->only(['programId']));
        return response()->json(['data' => CurriculumResource::collection($data), 'message' => 'success']);
    }

    public function store(CurriculumRequest $request): JsonResponse
    {
        $curriculum = $this->service->create($request->validated());
        return response()->json(['data' => new CurriculumResource($curriculum), 'message' => 'Curriculum created'], 201);
    }

    public function show(int $id): JsonResponse
    {
        $curriculum = $this->service->getById($id);
        if (!$curriculum) return response()->json(['message' => 'Not found'], 404);
        return response()->json(['data' => new CurriculumResource($curriculum), 'message' => 'success']);
    }

    public function update(CurriculumRequest $request, int $id): JsonResponse
    {
        $curriculum = $this->service->update($id, $request->validated());
        if (!$curriculum) return response()->json(['message' => 'Not found'], 404);
        return response()->json(['data' => new CurriculumResource($curriculum), 'message' => 'Curriculum updated']);
    }

    public function destroy(int $id): JsonResponse
    {
        if (!$this->service->delete($id)) {
            return response()->json(['message' => 'Not found'], 404);
        }
        return response()->json(['message' => 'Curriculum deleted']);
    }

    public function activate(int $id): JsonResponse
    {
        $curriculum = $this->service->activate($id);
        if (!$curriculum) return response()->json(['message' => 'Not found'], 404);
        return response()->json(['data' => new CurriculumResource($curriculum), 'message' => 'Curriculum activated']);
    }
}
