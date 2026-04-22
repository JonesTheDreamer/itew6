<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SectionRequest;
use App\Http\Resources\SectionResource;
use App\Services\SectionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SectionController extends Controller
{
    public function __construct(private SectionService $service) {}

    public function index(Request $request): JsonResponse
    {
        $data = $this->service->getAll($request->only(['programId', 'academicYear', 'semester']));
        return response()->json(['data' => SectionResource::collection($data), 'message' => 'success']);
    }

    public function store(SectionRequest $request): JsonResponse
    {
        $section = $this->service->create($request->validated());
        return response()->json(['data' => new SectionResource($section), 'message' => 'Section created'], 201);
    }

    public function show(int $id): JsonResponse
    {
        $section = $this->service->getById($id);
        if (!$section) return response()->json(['message' => 'Not found'], 404);
        return response()->json(['data' => new SectionResource($section), 'message' => 'success']);
    }

    public function update(SectionRequest $request, int $id): JsonResponse
    {
        $section = $this->service->update($id, $request->validated());
        if (!$section) return response()->json(['message' => 'Not found'], 404);
        return response()->json(['data' => new SectionResource($section), 'message' => 'Section updated']);
    }

    public function destroy(int $id): JsonResponse
    {
        if (!$this->service->delete($id)) {
            return response()->json(['message' => 'Not found'], 404);
        }
        return response()->json(['message' => 'Section deleted']);
    }
}
