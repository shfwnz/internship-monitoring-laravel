<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

// Resources
use App\Models\User;
use App\Models\Student;

class AuthController extends Controller
{
    public function __construct(User $user)
    {
        // model as dependency injection
        $this->user = $user;
    }

    public function login (Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Login failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $credentials = $request->only('email', 'password');

        if(!$token = auth()->guard('api')->attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message' => 'Login failed',
            ], 401);
        }

        return $this->respondWithToken($token);
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->guard('api')->factory()->getTTL() * 60,
            'user' => auth()->guard('api')->user()
        ]);
    }

    public function register (Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nis' => 'required|string|unique:students,nis',

            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
        ], [
            'nis.unique' => 'NIS has already been registered',
            'email.unique' => 'Email has already been registered',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Registration failed',
                'errors' => $validator->errors()
            ], 422);
        }

        DB::beginTransaction();

        try {
            $student = Student::create([
                'nis' => $request->nis,
                'status' => false
            ]);

            $user = $student->user()->create([
                // 'id' => $student->id,
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'gender' => null,
                'phone' => null,
                'address' => null
            ]);

            $user->assignRole('student');

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Registered successfully',
                'register_data' => $user
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Registration failed',
                'errors' => $e->getMessage()
            ], 422);
        }
    }

    public function logout(Request $request)
    {
        auth()->guard('api')->logout();
        
        return response()->json([
            'success' => true,
            'message' => 'Successfully logged out'
        ]);
    }
}
