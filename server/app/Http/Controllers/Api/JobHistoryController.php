<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\JobHistoryRequest;
use App\Repositories\JobHistoryRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class JobHistoryController extends Controller
{
    public function __construct(private JobHistoryRepository $repo) {}

    public function index(Request $request): JsonResponse
    {
        $data = $request->has('facultyId')
            ? $this->repo->getByFaculty((int) $request->facultyId)
            : $this->repo->getAll();

        return response()->json(['data' => $data, 'message' => 'success']);
    }

    public function store(JobHistoryRequest $request): JsonResponse
    {
        $item = $this->repo->create($request->validated());
        return response()->json(['data' => $item, 'message' => 'Job history added'], 201);
    }

    public function update(JobHistoryRequest $request, int $id): JsonResponse
    {
        $item = $this->repo->update($id, $request->validated());
        if (!$item) return response()->json(['message' => 'Not found'], 404);
        return response()->json(['data' => $item, 'message' => 'Job history updated']);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->repo->delete($id);
        return response()->json(['message' => 'Job history deleted']);
    }
}
