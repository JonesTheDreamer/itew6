<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\CollegeRepository;
use Illuminate\Http\JsonResponse;

class CollegeController extends Controller
{
    public function __construct(private CollegeRepository $repo) {}

    public function index(): JsonResponse
    {
        return response()->json(['data' => $this->repo->getAll(), 'message' => 'success']);
    }

    public function show(int $id): JsonResponse
    {
        $college = $this->repo->getById($id);
        if (!$college) return response()->json(['message' => 'Not found'], 404);
        return response()->json(['data' => $college, 'message' => 'success']);
    }
}
