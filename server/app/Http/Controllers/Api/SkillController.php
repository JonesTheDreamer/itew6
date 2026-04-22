<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Skill;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SkillController extends Controller
{
    public function index(): JsonResponse
    {
        $skills = Skill::withCount('students')
            ->orderBy('isAcademic', 'desc')
            ->orderBy('name')
            ->get();
        return response()->json(['data' => $skills, 'message' => 'success']);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'       => ['required', 'string', 'max:100', 'unique:skills,name'],
            'isAcademic' => ['boolean'],
        ]);

        $skill = Skill::create([
            'name'       => $validated['name'],
            'isAcademic' => $validated['isAcademic'] ?? false,
        ]);

        $skill->loadCount('students');
        return response()->json(['data' => $skill, 'message' => 'success'], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $skill = Skill::findOrFail($id);

        $validated = $request->validate([
            'name'       => ['required', 'string', 'max:100', Rule::unique('skills', 'name')->ignore($id)],
            'isAcademic' => ['boolean'],
        ]);

        $skill->update($validated);
        $skill->loadCount('students');
        return response()->json(['data' => $skill, 'message' => 'success']);
    }

    public function destroy(int $id): JsonResponse
    {
        Skill::findOrFail($id)->delete();
        return response()->json(null, 204);
    }
}
