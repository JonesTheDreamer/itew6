<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Violation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ViolationController extends Controller
{
    public function index(int $studentId): JsonResponse
    {
        $student = Student::findOrFail($studentId);

        $violations = Violation::where('studentId', $student->userId)
            ->with(['notes' => fn($q) => $q->with('author')->orderBy('created_at', 'asc')])
            ->orderBy('violationDate', 'desc')
            ->get()
            ->map(fn($v) => [
                'id'            => $v->id,
                'studentId'     => $v->studentId,
                'title'         => $v->title,
                'violationDate' => $v->violationDate,
                'description'   => $v->description,
                'created_at'    => $v->created_at,
                'notes'         => $v->notes->map(fn($n) => [
                    'id'          => $n->id,
                    'violationId' => $n->violationId,
                    'note'        => $n->note,
                    'addedBy'     => $n->addedBy,
                    'addedByName' => $n->author
                        ? trim($n->author->firstName . ' ' . $n->author->lastName)
                        : 'Unknown',
                    'created_at'  => $n->created_at,
                ]),
            ]);

        return response()->json(['data' => $violations, 'message' => 'success']);
    }

    public function store(Request $request, int $studentId): JsonResponse
    {
        $student = Student::findOrFail($studentId);

        $validated = $request->validate([
            'title'         => ['required', 'string', 'max:200'],
            'violationDate' => ['required', 'date'],
            'description'   => ['nullable', 'string'],
        ]);

        $violation = Violation::create([
            'studentId'     => $student->userId,
            'title'         => $validated['title'],
            'violationDate' => $validated['violationDate'],
            'description'   => $validated['description'] ?? null,
        ]);

        return response()->json([
            'data' => [
                'id'            => $violation->id,
                'studentId'     => $violation->studentId,
                'title'         => $violation->title,
                'violationDate' => $violation->violationDate,
                'description'   => $violation->description,
                'created_at'    => $violation->created_at,
                'notes'         => [],
            ],
            'message' => 'Violation recorded',
        ], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $violation = Violation::findOrFail($id);

        $validated = $request->validate([
            'description' => ['nullable', 'string'],
        ]);

        if (array_key_exists('description', $validated)) {
            $violation->update(['description' => $validated['description']]);
        }

        return response()->json(['data' => $violation, 'message' => 'Violation updated']);
    }
}
