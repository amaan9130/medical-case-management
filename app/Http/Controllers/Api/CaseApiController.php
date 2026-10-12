<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCaseRequest;
use App\Http\Requests\UpdateCaseRequest;
use App\Models\MedicalCase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CaseApiController extends Controller
{
    /**
     * List medical cases with pagination and optional filters.
     * Accessible by: Admin, Viewer, Engineer.
     */
    public function index(Request $request): JsonResponse
    {
        $search = $request->query('search');
        $status = $request->query('status');
        $priority = $request->query('priority');
        $perPage = (int) $request->query('per_page', 10);

        $cases = MedicalCase::with('creator:id,name,email,role')
            ->search($search)
            ->filterStatus($status)
            ->filterPriority($priority)
            ->latest()
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Cases retrieved successfully.',
            'data' => $cases,
        ], 200);
    }

    /**
     * Store a newly created case.
     * Accessible by: Admin only.
     */
    public function store(StoreCaseRequest $request): JsonResponse
    {
        $user = $request->user();

        if (!$user->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Only administrators may create cases via the API.',
                'data' => null,
            ], 403);
        }

        $validated = $request->validated();
        $validated['created_by'] = $user->id;

        $case = MedicalCase::create($validated);
        $case->load('creator:id,name,email,role');

        return response()->json([
            'success' => true,
            'message' => 'Medical case created successfully.',
            'data' => $case,
        ], 201);
    }

    /**
     * Display a specific case.
     * Accessible by: Admin, Viewer, Engineer.
     */
    public function show(string|int $id): JsonResponse
    {
        $case = MedicalCase::with('creator:id,name,email,role')->find($id);

        if (!$case) {
            return response()->json([
                'success' => false,
                'message' => 'Medical case not found.',
                'data' => null,
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Medical case retrieved successfully.',
            'data' => $case,
        ], 200);
    }

    /**
     * Update an existing case.
     * Accessible by: Admin only.
     */
    public function update(UpdateCaseRequest $request, string|int $id): JsonResponse
    {
        $user = $request->user();

        if (!$user->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Only administrators may modify cases.',
                'data' => null,
            ], 403);
        }

        $case = MedicalCase::find($id);

        if (!$case) {
            return response()->json([
                'success' => false,
                'message' => 'Medical case not found.',
                'data' => null,
            ], 404);
        }

        $case->update($request->validated());
        $case->load('creator:id,name,email,role');

        return response()->json([
            'success' => true,
            'message' => 'Medical case updated successfully.',
            'data' => $case,
        ], 200);
    }

    /**
     * Delete an existing case.
     * Accessible by: Admin only.
     */
    public function destroy(Request $request, string|int $id): JsonResponse
    {
        $user = $request->user();

        if (!$user->isAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Only administrators may delete cases.',
                'data' => null,
            ], 403);
        }

        $case = MedicalCase::find($id);

        if (!$case) {
            return response()->json([
                'success' => false,
                'message' => 'Medical case not found.',
                'data' => null,
            ], 404);
        }

        $case->delete();

        return response()->json([
            'success' => true,
            'message' => "Medical case #{$case->case_number} deleted successfully.",
            'data' => null,
        ], 200);
    }
}
