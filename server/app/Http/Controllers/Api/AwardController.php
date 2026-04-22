<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\AwardRequest;
use App\Repositories\AwardRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AwardController extends Controller
{
    public function __construct(private AwardRepository $repo) {}

    public function index(Request $request): JsonResponse
    {
        $data = $request->has('studentId')
            ? $this->repo->getByStudent((int) $request->studentId)
            : $this->repo->getAll();

        return response()->json(['data' => $data, 'message' => 'success']);
    }

    public function store(AwardRequest $request): JsonResponse
    {
        $award = $this->repo->create($request->validated());
        return response()->json(['data' => $award, 'message' => 'Award added'], 201);
    }

    public function update(AwardRequest $request, int $id): JsonResponse
    {
        $award = $this->repo->update($id, $request->validated());
        if (!$award) return response()->json(['message' => 'Not found'], 404);
        return response()->json(['data' => $award, 'message' => 'Award updated']);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->repo->delete($id);
        return response()->json(['message' => 'Award deleted']);
    }
}
