<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\GradeRequest;
use App\Http\Resources\GradeResource;
use App\Services\GradeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GradeController extends Controller
{
    public function __construct(private GradeService $service) {}

    public function index(Request $request): JsonResponse
    {
        $data = $this->service->getAll($request->only(['studentId', 'sectionId']));

        $response = ['data' => GradeResource::collection($data), 'message' => 'success'];

        if ($request->filled('studentId') && $request->filled('academicYear') && $request->filled('semester')) {
            $response['gpa'] = $this->service->computeSemesterGpa(
                (int) $request->studentId,
                $request->academicYear,
                (int) $request->semester
            );
        }

        return response()->json($response);
    }

    public function store(GradeRequest $request): JsonResponse
    {
        $grade = $this->service->create($request->validated());
        return response()->json(['data' => new GradeResource($grade), 'message' => 'Grade recorded'], 201, [], JSON_PRESERVE_ZERO_FRACTION);
    }

    public function update(GradeRequest $request, int $id): JsonResponse
    {
        $grade = $this->service->update($id, $request->validated());
        if (!$grade) return response()->json(['message' => 'Not found'], 404);
        return response()->json(['data' => new GradeResource($grade), 'message' => 'Grade updated'], 200, [], JSON_PRESERVE_ZERO_FRACTION);
    }

    public function destroy(int $id): JsonResponse
    {
        if (!$this->service->delete($id)) {
            return response()->json(['message' => 'Not found'], 404);
        }
        return response()->json(['message' => 'Grade deleted']);
    }
}
