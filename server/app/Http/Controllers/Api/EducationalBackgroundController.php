<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\EducationalBackgroundRequest;
use App\Repositories\EducationalBackgroundRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EducationalBackgroundController extends Controller
{
    public function __construct(private EducationalBackgroundRepository $repo) {}

    public function index(Request $request): JsonResponse
    {
        $data = $request->has('studentId')
            ? $this->repo->getByStudent((int) $request->studentId)
            : $this->repo->getAll();

        return response()->json(['data' => $data, 'message' => 'success']);
    }

    public function store(EducationalBackgroundRequest $request): JsonResponse
    {
        $item = $this->repo->create($request->validated());
        return response()->json(['data' => $item, 'message' => 'Record added'], 201);
    }

    public function update(EducationalBackgroundRequest $request, int $id): JsonResponse
    {
        $item = $this->repo->update($id, $request->validated());
        if (!$item) return response()->json(['message' => 'Not found'], 404);
        return response()->json(['data' => $item, 'message' => 'Record updated']);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->repo->delete($id);
        return response()->json(['message' => 'Record deleted']);
    }
}
