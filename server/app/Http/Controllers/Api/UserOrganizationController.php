<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserOrganizationRequest;
use App\Repositories\UserOrganizationRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserOrganizationController extends Controller
{
    public function __construct(private UserOrganizationRepository $repo)
    {
    }

    public function index(Request $request): JsonResponse
    {
        if ($request->has('userId')) {
            $data = $this->repo->getByUser((int) $request->userId);
        } elseif ($request->has('organizationId')) {
            $data = $this->repo->getByOrganization((int) $request->organizationId);
        } else {
            $data = $this->repo->getAll();
        }

        return response()->json(['data' => $data, 'message' => 'success']);
    }

    public function store(UserOrganizationRequest $request): JsonResponse
    {
        $item = $this->repo->create($request->validated());
        return response()->json(['data' => $item, 'message' => 'Joined organization'], 201);
    }

    public function update(UserOrganizationRequest $request, int $id): JsonResponse
    {
        $item = $this->repo->update($id, $request->validated());
        if (!$item)
            return response()->json(['message' => 'Not found'], 404);
        return response()->json(['data' => $item, 'message' => 'Membership updated']);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->repo->delete($id);
        return response()->json(['message' => 'Left organization']);
    }
}