<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Exception;

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
        try {
            $internship = Internship::with(
                'student.user',
                'teacher.user',
                'industry',
                'industry.business_field',
            )->get();

            return response()->json(
                [
                    'success' => true,
                    'message' => 'success',
                    'all_data' => InternshipResource::collection($internship),
                ],
                200,
            );
        } catch (Exception $e) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Failed to retrieve internships',
                    'error' => $e->getMessage(),
                ],
                500,
            );
        }
    }

    public function myInternship()
    {
        try {
            $user = auth()->user();

            if (!$user) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'Unauthenticated',
                    ],
                    401,
                );
            }

            $user->load('userable');

            if (!$user->userable || !($user->userable instanceof Student)) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'Student profile not found',
                    ],
                    404,
                );
            }

            $student = $user->userable;

            $internship = Internship::with(
                'student.user',
                'teacher.user',
                'industry',
                'industry.business_field',
            )
                ->where('student_id', $student->id)
                ->first();

            if (!$internship) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'No internship found for this student',
                    ],
                    404,
                );
            }

            return response()->json(
                [
                    'success' => true,
                    'message' => 'Internship data retrieved successfully',
                    'data' => new InternshipResource($internship),
                ],
                200,
            );
        } catch (Exception $e) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Failed to retrieve internship data',
                    'error' => $e->getMessage(),
                ],
                500,
            );
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Enhanced validation
        $validator = Validator::make($request->all(), [
            'student_id' => 'required|integer|exists:students,id',
            'teacher_id' => 'nullable|integer|exists:teachers,id',
            'industry_id' => 'required|integer|exists:industries,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'file' => 'required|file|mimes:pdf|max:2048', // Max 2MB
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ],
                422,
            );
        }

        // Check if internship already exists for this student
        $existing = Internship::where(
            'student_id',
            $request->student_id,
        )->first();
        if ($existing) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Internship already exists for this student',
                ],
                422,
            );
        }

        DB::beginTransaction();

        try {
            $filePath = null;

            // Handle file upload
            if ($request->hasFile('file')) {
                $file = $request->file('file');

                // Validate file is uploaded properly
                if (!$file->isValid()) {
                    throw new Exception('File upload failed - invalid file');
                }

                // Generate unique filename
                $fileName =
                    time() .
                    '_' .
                    uniqid() .
                    '.' .
                    $file->getClientOriginalExtension();

                // Store file correctly
                $filePath = $file->storeAs(
                    'internship-files',
                    $fileName,
                    'public',
                );

                // Verify file was stored
                if (!Storage::disk('public')->exists($filePath)) {
                    throw new Exception('File storage failed');
                }
            }

            // Create internship record
            $internship = Internship::create([
                'student_id' => $request->student_id,
                'teacher_id' => $request->teacher_id,
                'industry_id' => $request->industry_id,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'file' => $filePath,
            ]);

            // Load relationships for response
            $internship->load('student.user', 'teacher.user', 'industry');

            DB::commit();

            return response()->json(
                [
                    'success' => true,
                    'message' => 'Internship created successfully',
                    'data' => new InternshipResource($internship),
                ],
                201,
            );
        } catch (Exception $e) {
            DB::rollBack();

            // Clean up uploaded file if it exists
            if ($filePath && Storage::disk('public')->exists($filePath)) {
                Storage::disk('public')->delete($filePath);
            }

            return response()->json(
                [
                    'success' => false,
                    'message' => 'Failed to create internship',
                    'error' => $e->getMessage(),
                ],
                500,
            );
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $internship = Internship::with(
                'student.user',
                'teacher.user',
                'industry',
            )->findOrFail($id);

            return response()->json(
                [
                    'success' => true,
                    'message' => 'Internship found successfully',
                    'data' => new InternshipResource($internship),
                ],
                200,
            );
        } catch (Exception $e) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Internship not found',
                    'error' => $e->getMessage(),
                ],
                404,
            );
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Implementation for update method
        try {
            $internship = Internship::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'teacher_id' => 'nullable|integer|exists:teachers,id',
                'industry_id' =>
                    'sometimes|required|integer|exists:industries,id',
                'start_date' => 'sometimes|required|date',
                'end_date' => 'sometimes|required|date|after:start_date',
                'file' => 'nullable|file|mimes:pdf|max:2048',
            ]);

            if ($validator->fails()) {
                return response()->json(
                    [
                        'success' => false,
                        'message' => 'Validation failed',
                        'errors' => $validator->errors(),
                    ],
                    422,
                );
            }

            DB::beginTransaction();

            $updateData = $request->only([
                'teacher_id',
                'industry_id',
                'start_date',
                'end_date',
            ]);

            // Handle file update if provided
            if ($request->hasFile('file')) {
                $file = $request->file('file');

                if ($file->isValid()) {
                    // Delete old file
                    if (
                        $internship->file &&
                        Storage::disk('public')->exists($internship->file)
                    ) {
                        Storage::disk('public')->delete($internship->file);
                    }

                    // Store new file
                    $fileName =
                        time() .
                        '_' .
                        uniqid() .
                        '.' .
                        $file->getClientOriginalExtension();
                    $updateData['file'] = $file->storeAs(
                        'internship',
                        $fileName,
                        'public',
                    );
                }
            }

            $internship->update($updateData);
            $internship->load('student.user', 'teacher.user', 'industry');

            DB::commit();

            return response()->json(
                [
                    'success' => true,
                    'message' => 'Internship updated successfully',
                    'data' => new InternshipResource($internship),
                ],
                200,
            );
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Failed to update internship',
                    'error' => $e->getMessage(),
                ],
                500,
            );
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $internship = Internship::findOrFail($id);

            DB::beginTransaction();

            // Delete associated file
            if (
                $internship->file &&
                Storage::disk('public')->exists($internship->file)
            ) {
                Storage::disk('public')->delete($internship->file);
            }

            $internship->delete();

            DB::commit();

            return response()->json(
                [
                    'success' => true,
                    'message' => 'Internship deleted successfully',
                ],
                200,
            );
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Failed to delete internship',
                    'error' => $e->getMessage(),
                ],
                500,
            );
        }
    }
}
