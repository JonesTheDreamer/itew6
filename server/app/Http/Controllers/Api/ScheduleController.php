<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\ScheduleRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function __construct(private ScheduleRepository $repo) {}

    public function index(Request $request): JsonResponse
{
    if ($request->has('facultyId')) {
        $data = $this->repo->getByFaculty((int) $request->facultyId);
    } elseif ($request->has('sectionId')) {
        $data = $this->repo->getBySection((int) $request->sectionId);
    } else {
        $data = []; // don't load all schedules without a filter
    }

    return response()->json(['data' => $data, 'message' => 'success']);
}

    public function byStudent(int $studentId): JsonResponse
    {
        return response()->json([
            'data'    => $this->repo->getByStudent($studentId),
            'message' => 'success',
        ]);
    }
}