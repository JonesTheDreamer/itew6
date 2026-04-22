<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ExtraCurricularRequest;
use App\Repositories\ExtraCurricularRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExtraCurricularController extends Controller
{
    public function __construct(private ExtraCurricularRepository $repo) {}

    public function index(Request $request): JsonResponse
    {
        $data = $request->has('studentId')
            ? $this->repo->getByStudent((int) $request->studentId)
            : $this->repo->getAll();

        return response()->json(['data' => $data, 'message' => 'success']);
    }

    public function store(ExtraCurricularRequest $request): JsonResponse
    {
        $item = $this->repo->create($request->validated());
        return response()->json(['data' => $item, 'message' => 'Activity added'], 201);
    }

    public function update(ExtraCurricularRequest $request, int $id): JsonResponse
    {
        $item = $this->repo->update($id, $request->validated());
        if (!$item) return response()->json(['message' => 'Not found'], 404);
        return response()->json(['data' => $item, 'message' => 'Activity updated']);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->repo->delete($id);
        return response()->json(['message' => 'Activity deleted']);
    }
}
