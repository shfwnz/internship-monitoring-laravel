<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;

// Resources
use App\Http\Resources\UserResource;
use App\Models\User;

class UserController extends Controller
{
    /**
     * Get authenticated user data
     */
    public function me(Request $request)
    {
        $user = $request->user()->load('roles');
        
        return response()->json([
            'success' => true,
            'data' => new UserResource($user)
        ]);
    }

    /**
     * Display a listing of admin users
     */
    public function index()
    {
        $users = User::whereDoesntHave('userable')
                   ->with('roles')
                   ->get();

        return response()->json([
            'success' => true,
            'data' => UserResource::collection($users)
        ]);
    }

    /**
     * Create new admin user
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'gender' => 'required|in:L,P',
            'phone' => 'required|string|unique:users,phone',
            'address' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'gender' => $request->gender,
                'phone' => $request->phone,
                'address' => $request->address,
                // Tidak ada userable_id/userable_type karena murni admin
            ]);

            $user->assignRole('admin');

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => new UserResource($user->load('roles'))
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create admin user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display specific admin user
     */
    public function show(string $id)
    {
        $user = User::whereDoesntHave('userable')
                  ->with('roles')
                  ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => new UserResource($user)
        ]);
    }

    /**
     * Update admin user
     */
    public function update(Request $request, string $id)
    {
        $user = User::whereDoesntHave('userable')
                  ->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,'.$user->id,
            'password' => 'sometimes|string|min:8',
            'gender' => 'sometimes|in:L,P',
            'phone' => 'sometimes|string|unique:users,phone,'.$user->id,
            'address' => 'sometimes|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $user->update($request->except('password'));

        if ($request->has('password')) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        return response()->json([
            'success' => true,
            'data' => new UserResource($user->fresh()->load('roles'))
        ]);
    }

    /**
     * Delete admin user
     */
    public function destroy(string $id)
    {
        $user = User::whereDoesntHave('userable')
                  ->findOrFail($id);

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'Admin user deleted successfully'
        ]);
    }
}