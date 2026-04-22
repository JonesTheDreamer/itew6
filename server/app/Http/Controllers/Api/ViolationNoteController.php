<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Violation;
use App\Models\ViolationNote;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ViolationNoteController extends Controller
{
    public function store(Request $request, int $violationId): JsonResponse
    {
        $violation = Violation::findOrFail($violationId);

        $validated = $request->validate([
            'note' => ['required', 'string'],
        ]);

        $note = ViolationNote::create([
            'violationId' => $violation->id,
            'note'        => $validated['note'],
            'addedBy'     => auth()->id(),
        ]);

        $note->load('author');

        return response()->json([
            'data' => [
                'id'          => $note->id,
                'violationId' => $note->violationId,
                'note'        => $note->note,
                'addedBy'     => $note->addedBy,
                'addedByName' => $note->author
                    ? trim($note->author->firstName . ' ' . $note->author->lastName)
                    : 'Unknown',
                'created_at'  => $note->created_at,
            ],
            'message' => 'Note added',
        ], 201);
    }
}
