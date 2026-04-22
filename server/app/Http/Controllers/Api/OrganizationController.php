<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrganizationRequest;
use App\Repositories\OrganizationRepository;
use Illuminate\Http\JsonResponse;

class OrganizationController extends Controller
{
    public function __construct(private OrganizationRepository $repo) {}

    public function index(): JsonResponse
    {
        return response()->json(['data' => $this->repo->getAll(), 'message' => 'success']);
    }

    public function stats(): JsonResponse
    {
        return response()->json(['data' => $this->repo->getStats(), 'message' => 'success']);
    }

    public function show(int $id): JsonResponse
    {
        $org = $this->repo->getById($id);
        if (!$org) return response()->json(['message' => 'Not found'], 404);
        return response()->json(['data' => $org, 'message' => 'success']);
    }

    public function store(OrganizationRequest $request): JsonResponse
    {
        $org = $this->repo->create($request->validated());
        return response()->json(['data' => $org, 'message' => 'Organization created'], 201);
    }

    public function update(OrganizationRequest $request, int $id): JsonResponse
    {
        $org = $this->repo->update($id, $request->validated());
        if (!$org) return response()->json(['message' => 'Not found'], 404);
        return response()->json(['data' => $org, 'message' => 'Organization updated']);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->repo->delete($id);
        return response()->json(['message' => 'Organization deleted']);
    }
}
