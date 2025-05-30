<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
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
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make(request()->all(), [
            // Teacher
            'nip' => 'required|string|unique:teachers,nip',
            
            // User
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'gender' => 'required|in:L,P',
            'phone' => 'required|string|unique:users,phone', 
            'address' => 'required|string',    
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ], [
            'phone.unique' => 'Phone number has already been registered',
            'email.unique' => 'Email has already been registered',
            'nip.unique' => 'NIP has already been registered',
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
            $image = $request->file('image');
            $image->storeAs('user-images', $image->hashName());

            // Create Teacher
            $teacher = Teacher::create([
                'nip' => $request->nip,
            ]);

            // Create User
            $user = $teacher->user()->create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'gender' => $request->gender,
                'phone' => $request->phone,
                'address' => $request->address,
                'image' => $image->hashName(),
            ])->assignRole('teacher');


            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data has been created',
                'created_data' => new TeacherResource($teacher),
            ], 200);
        } catch (\Exception $th) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Data has not been created',
                'errors' => $th->getMessage()
            ], 422);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $teacher = $teacher->with('user')->findOrFail($id);

        return response()->json([
            'success' => true,
            'message' => 'Data has been found',
            'find_data' => new TeacherResource($teacher),
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $teacher = Teacher::with('user')->findOrFail($id);

        $validator = Validator::make($request->all(), [
            // Teacher
            'nip' => 'required|string|unique:teachers,nip',
            
            // User
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'gender' => 'required|in:L,P',
            'phone' => 'required|string|unique:users,phone', 
            'address' => 'required|string',   
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', 
            ], [
            'phone.unique' => 'Phone number has already been registered',
            'email.unique' => 'Email has already been registered',
            'nip.unique' => 'NIP has already been registered',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Data has not been updated',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();

        try {
            $image = $request->file('image');
            if ($image) {
                $image->storeAs('user-images', $image->hashName());
                $teacher->user->update([
                    'image' => $image->hashName(),
                ]);
            }

            // Update Teacher
            $teacher->update($validator->validated());

            // Update User
            $teacher->user->update($request->only([
                'name', 'email', 'password', 'gender', 'phone', 'address', 'image'
            ]));

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Data has been updated',
                'updated_data' => new TeacherResource($teacher),
            ], 200);

        } catch (\Exception $th) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Data has not been updated',
                'errors' => $th->getMessage()
            ], 422);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        DB::beginTransaction();

        try {
            $teacher = Teacher::with('user')->findOrFail($id);
            
            // Delete User
            if ($teacher->user) {
                $teacher->user->delete();
            }
            
            // Then delete 
            $teacher->delete();
    
            DB::commit();
    
            return response()->json([
                'success' => true,
                'message' => 'Teacher and associated user deleted successfully',
                'deleted_data' => new TeacherResource($teacher)
            ], 200);
    
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete student',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
