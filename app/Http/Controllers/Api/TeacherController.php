<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

// Resources
use App\Http\Resources\TeacherResource;
use App\Models\Teacher;
use App\Models\User;

class TeacherController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $teachers = Teacher::with('user')->get();

        return response()->json([
            'success' => true,
            'message' => 'success',
            'all_data' => TeacherResource::collection($teachers),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make(request()->all(), [
            // Teacher
            'nip' => 'required|string',
            
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
                'status' => 422,
                'errors' => $validator->errors()
            ]);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'gender' => $request->gender,
            'phone' => $request->phone,
            'address' => $request->address,
            'userable_type' => Teacher::class 
        ]);

        $teacher = Teacher::create([
            'nip' => request()->nip,
        ]);

        $user->update(['userable_id' => $teacher->id]);

        return response()->json([
            'success' => true,
            'status' => 200,
            'added_data' => new TeacherResource($teacher),
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $teacher = Teacher::find($id);

        return response()->json([
            'success' => true,
            'status' => 200,
            'find_data' => new TeacherResource($teacher),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'nip' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'status' => 422,
                'errors' => $validator->errors()
            ]);
        }

        $teacher = Teacher::create([
            'nip' => $request->nip,
        ]);

        return response()->json([
            'success' => true,
            'status' => 200,
            'updated_data' => new TeacherResource($teacher),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $teacher = Teacher::find($id);
        $teacher->delete();
        
        return response()->json([
            'success' => true,
            'status' => 200,
            'deleted_data' => new TeacherResource($teacher),
        ]);
    }
}
