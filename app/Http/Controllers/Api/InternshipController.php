<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

// Resources
use App\Http\Resources\InternshipResource;
use App\Models\Internship;
use App\Models\Student;

class InternshipController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $internship = Internship::with('student.user', 'teacher.user', 'industry')->get();

        return response()->json([
            'success' => true,
            'message' => 'success',
            'all_data' => InternshipResource::collection($internship),
        ], 200);
    }

    public function myInternship()
    {
        $user = auth()->user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated',
            ], 401);
        }

        $user->load('userable');
        
        if (!$user->userable || !($user->userable instanceof Student)) {
            return response()->json([
                'success' => false,
                'message' => 'Student profile not found',
            ], 404);
        }

        $student = $user->userable;
        
        $internship = Internship::with('student.user', 'teacher.user', 'industry')
            ->where('student_id', $student->id)
            ->first();

        if (!$internship) {
            return response()->json([
                'success' => false,
                'message' => 'No internship found for this student',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Internship data retrieved successfully',
            'data' => new InternshipResource($internship),
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'student_id' => 'required|exists:students,id',
            'teacher_id' => 'required|exists:teachers,id',
            'industry_id' => 'required|exists:industries,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        ]);

        $existing = Internship::where('student_id', $request->student_id);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Data has not been created',
                'errors' => $validator->errors()
            ], 422);
        }

        if ($existing->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Internship already exists for this student',
            ], 422);
        }

        DB::beginTransaction();

        try {
            Internship::create($request->all());
            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Data has been created',
                'created_data' => new InternshipResource(Internship::with('student.user', 'teacher.user', 'industry')->latest()->first())
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Data has not been created',
                'errors' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $internship = Internship::with('student.user', 'teacher.user', 'industry')->findOrFail($id);

        return response()->json([
            'success' => true,
            'message' => 'Data has been found',
            'find_data' => new InternshipResource($internship),
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
