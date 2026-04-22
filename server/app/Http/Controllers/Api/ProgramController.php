<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\ProgramRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProgramController extends Controller
{
    public function __construct(private ProgramRepository $repo) {}

    public function index(Request $request): JsonResponse
    {
        $programs = $request->has('collegeId')
            ? $this->repo->getByCollege((int) $request->collegeId)
            : $this->repo->getAll();

        return response()->json(['data' => $programs, 'message' => 'success']);
    }

    public function show(int $id): JsonResponse
    {
        $program = $this->repo->getById($id);
        if (!$program) return response()->json(['message' => 'Not found'], 404);
        return response()->json(['data' => $program, 'message' => 'success']);
    }
}
