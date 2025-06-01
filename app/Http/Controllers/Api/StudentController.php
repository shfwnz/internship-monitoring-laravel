<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

// Resources
use App\Http\Resources\StudentResource;
use App\Models\Student;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $students = Student::with('user')->get();

        return response()->json(
            [
                'success' => true,
                'message' => 'success',
                'all_data' => StudentResource::collection($students),
            ],
            200,
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make(
            request()->all(),
            [
                // Student
                'nis' => 'required|string|unique:students,nis',

                // User
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:8',
                'gender' => 'nullable|in:L,P',
                'phone' => 'nullable|string|unique:users,phone',
                'address' => 'nullable|string',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ],
            [
                'phone.unique' => 'Phone number has already been registered',
                'email.unique' => 'Email has already been registered',
                'nis.unique' => 'NIS has already been registered',
            ],
        );

        if ($validator->fails()) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Data has not been created',
                    'errors' => $validator->errors(),
                ],
                422,
            );
        }

        DB::beginTransaction();

        try {
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imagePath = $image->store('student-images', 'public');
            }

            // Create Student
            $student = Student::create([
                'nis' => $request->nis,
                'status' => false,
            ]);

            // Create User
            $user = $student
                ->user()
                ->create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'gender' => $request->gender,
                    'phone' => $request->phone,
                    'address' => $request->address,
                    'image' => $imagePath,
                ])
                ->assignRole('student');

            DB::commit();

            return response()->json(
                [
                    'success' => true,
                    'message' => 'Student created successfully',
                    'data' => new StudentResource($student->load('user')),
                ],
                201,
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Failed to create student',
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
        $student = Student::with('user')->findOrFail($id);

        return response()->json(
            [
                'success' => true,
                'message' => 'Data has been found',
                'find_data' => new StudentResource($student),
            ],
            200,
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $student = Student::with('user')->findOrFail($id);

        $validator = Validator::make(
            $request->all(),
            [
                // Student
                'nis' => 'sometimes|string|unique:students,nis,' . $student->id,

                // User
                'name' => 'sometimes|string|max:255',
                'email' =>
                    'sometimes|email|unique:users,email,' . $student->user->id,
                'password' => 'sometimes|string|min:8',
                'gender' => 'sometimes|in:L,P',
                'phone' =>
                    'sometimes|string|unique:users,phone,' . $student->user->id,
                'address' => 'sometimes|string',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ],
            [
                'phone.unique' => 'Phone number has already been registered',
                'email.unique' => 'Email has already been registered',
                'nis.unique' => 'NIS has already been registered',
            ],
        );

        if ($validator->fails()) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Data has not been updated',
                    'errors' => $validator->errors(),
                ],
                422,
            );
        }

        DB::beginTransaction();

        try {
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imagePath = $image->store('student-images', 'public');

                if ($student->user->image) {
                    Storage::delete('public/' . $student->user->image);
                }

                $student->user->update([
                    'image' => $imagePath,
                ]);
            }

            // Update Student
            $student->update($validator->validated());

            // Update User
            $student->user->update(
                $request->only([
                    'name',
                    'email',
                    'password',
                    'gender',
                    'phone',
                    'address',
                ]),
            );

            DB::commit();

            return response()->json(
                [
                    'success' => true,
                    'message' => 'Data has been updated',
                    'updated_data' => new StudentResource($student),
                ],
                200,
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Failed to update student',
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
        DB::beginTransaction();

        try {
            $student = Student::with('user')->findOrFail($id);

            // Delete User
            if ($student->user) {
                $student->user->delete();
            }

            // Then delete Student
            $student->delete();

            DB::commit();

            return response()->json(
                [
                    'success' => true,
                    'message' =>
                        'Student and associated user deleted successfully',
                    'deleted_data' => new StudentResource($student),
                ],
                200,
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Failed to delete student',
                    'error' => $e->getMessage(),
                ],
                500,
            );
        }
    }
}
