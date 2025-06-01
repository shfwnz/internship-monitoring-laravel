<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

// Resources
use App\Models\User;
use App\Models\Student;
use App\Models\Teacher;

class AuthController extends Controller
{
    protected $user;

    public function __construct(User $user)
    {
        // model as dependency injection
        $this->user = $user;
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Login failed',
                    'errors' => $validator->errors(),
                ],
                422,
            );
        }

        $credentials = $request->only('email', 'password');

        if (!($token = auth()->guard('api')->attempt($credentials))) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Login failed',
                ],
                401,
            );
        }

        return $this->respondWithToken($token);
    }

    protected function respondWithToken($token)
    {
        $user = auth()->guard('api')->user();
        
        // Load the profile data based on role
        $profileData = null;
        if ($user->hasRole('student')) {
            $profileData = $user->profile; 
        } elseif ($user->hasRole('teacher')) {
            $profileData = $user->profile; 
        }

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->guard('api')->factory()->getTTL() * 60,
            'user' => $user,
            'profile' => $profileData,
            'roles' => $user->getRoleNames(),
        ]);
    }

    public function register(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'role' => 'required|in:student,teacher',
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:8',
                // Student
                'nis' =>
                    'required_if:role,student|string|unique:students,nis|nullable',
                // Teacher
                'nip' =>
                    'required_if:role,teacher|string|unique:teachers,nip|nullable',
            ],
            [
                'nis.required_if' => 'NIS required if role is student',
                'nip.required_if' => 'NIP required if role is teacher',
                'role.in' => 'Role must be student or teacher',
            ],
        );

        if ($validator->fails()) {
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Registration failed',
                    'errors' => $validator->errors(),
                ],
                422,
            );
        }

        DB::beginTransaction();

        try {
            $userData = [
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'gender' => null,
                'phone' => null,
                'address' => null,
            ];

            if ($request->role === 'student') {
                $student = Student::create([
                    'nis' => $request->nis,
                    'status' => false,
                ]);

                $user = $student->user()->create($userData);
                $role = Role::findByName('student', 'api');
                $user->assignRole($role);

                $profileId = $student->id;
            } else {
                $teacher = Teacher::create([
                    'nip' => $request->nip,
                ]);

                $user = $teacher->user()->create($userData);
                $role = Role::findByName('teacher', 'api');
                $user->assignRole($role);

                $profileId = $teacher->id;
            }

            DB::commit();

            return response()->json(
                [
                    'success' => true,
                    'message' => 'Registered successfully',
                    'register_data' => [
                        'profile_id' => $profileId,
                        'user_id' => $user->id,
                        'role' => $request->role,
                    ],
                ],
                201,
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(
                [
                    'success' => false,
                    'message' => 'Registration failed',
                    'errors' => $e->getMessage(),
                ],
                500,
            );
        }
    }

    public function logout(Request $request)
    {
        auth()->guard('api')->logout();

        return response()->json([
            'success' => true,
            'message' => 'Successfully logged out',
        ]);
    }
}
