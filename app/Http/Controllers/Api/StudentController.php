<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

// Resources
use App\Http\Resources\StudentResource;
use App\Models\Student;
use App\Models\User;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $students = Student::with('user')->get();

        return response()->json([
            'success' => true,
            'message' => 'success',
            'all_data' => StudentResource::collection($students),
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make(request()->all(), [
            // Student
            'nis' => 'required|string|unique:students,nis',
            
            // User
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string',
            'gender' => 'required|in:L,P',
            'address' => 'required|string',
            'phone' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Data has not been created',
                'errors' => $validator->errors()
            ], 422);
        }

        $student = Student::create([
            'nis' => request()->nis,
            'status' => false
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'gender' => $request->gender,
            'phone' => $request->phone,
            'address' => $request->address,
            'userable_id' => $student->id,
            'userable_type' => Student::class 
        ]);


        $student = Student::with('user')->find($student->id);

        return response()->json([
            'success' => true,
            'message' => 'Data has been created',
            'created_data' => new StudentResource($student->load('user')),
        ],201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $student = Student::find($id);

        return response()->json([
            'success' => true,
            'status' => 200,
            'find_data' => new StudentResource($student),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $student = Student::findOrFail($id);

        $validator = Validator::make($request->all(), [
            // Student
            'nis' => 'sometimes|string|unique:students,nis,'.$student->id,
            
            // User
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,'.$student->user->id,
            'password' => 'sometimes|string|min:8',
            'gender' => 'sometimes|in:L,P',
            'phone' => 'sometimes|string|unique:users,phone,'.$student->user->id,
            'address' => 'sometimes|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Data has not been updated',
                'errors' => $validator->errors()
            ], 422);
        }

        if ($request->has('nis')) {
            $student->update(['nis' => $request->nis]);
        }

        $student->user->update($request->only([
            'name', 'email', 'gender', 'phone', 'address'
        ]));

        if ($request->has('password')) {
            $student->user->update(['password' => Hash::make($request->password)]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Data has been updated',
            'updated_data' => new StudentResource($student),    
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $student = Student::find($id);
        $student->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data has been deleted',
            'deleted_data' => new StudentResource($student),
        ], 200);
    }
}
