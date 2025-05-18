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
        try {
            $user = auth()->user();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated',
                ], 401);
            }

            \Log::info('Authenticated user:', ['user' => $user]);

            $user->load('userable');
            \Log::info('User after loading userable:', ['user' => $user]);

            if (!$user->userable) {
                \Log::error('Userable not found for user:', ['user_id' => $user->id]);
                return response()->json([
                    'success' => false,
                    'message' => 'Student profile not found',
                ], 404);
            }

            if (!($user->userable instanceof Student)) {
                \Log::error('Userable is not a Student:', [
                    'user_id' => $user->id,
                    'userable_type' => get_class($user->userable)
                ]);
                return response()->json([
                    'success' => false,
                    'message' => 'You are not a student',
                ], 403);
            }

            $student = $user->userable;
            \Log::info('Student found:', ['student' => $student]);
            
            $internship = Internship::with('student.user', 'teacher.user', 'industry')
                ->where('student_id', $student->id)
                ->first(); 

            \Log::info('Internship query executed');

            if (!$internship) {
                \Log::warning('No internship found for student:', ['student_id' => $student->id]);
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

        } catch (\Exception $e) {
            \Log::error('Error in myInternship:', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Internal server error',
                'error' => env('APP_DEBUG') ? $e->getMessage() : 'Please contact administrator'
            ], 500);
        }
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
        
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Data has not been created',
                'errors' => $validator->errors()
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
