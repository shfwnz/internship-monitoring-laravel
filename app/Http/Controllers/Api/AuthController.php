<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

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
            $profileData = $user->userable;
        } elseif ($user->hasRole('teacher')) {
            $profileData = $user->userable;
        }

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => JWTAuth::factory()->getTTL() * 60,
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
                'email' => 'required|email',
                'password' => 'required|string|min:8',
                // Student
                'nis' => 'required_if:role,student|string|nullable',
                // Teacher
                'nip' => 'required_if:role,teacher|string|nullable',
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
                'password' => Hash::make($request->password),
            ];

            // Find existing user
            $user = User::where('email', $request->email)->first();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User with this email not found',
                ], 404);
            }

            if ($request->role === 'student') {
                // Try to find existing student with this NIS
                $student = Student::where('nis', $request->nis)->first();

                if ($student) {
                    // Update existing student
                    $student->update([
                        'status' => false,
                    ]);
                } else {
                    // Create new student if none exists
                    return response()->json([
                        'success' => false,
                        'message' => 'Student with this NIS not found',
                    ], 404);
                }

                // Update existing user
                $user->update([
                    'name' => $request->name,
                    'password' => Hash::make($request->password),
                    'userable_id' => $student->id,
                    'userable_type' => Student::class,
                ]);
                $user->assignRoleWithGuard('student');
                $profileId = $student->id;
            } else {
                // Try to find existing teacher with this NIP
                $teacher = Teacher::where('nip', $request->nip)->first();

                if ($teacher) {
                    // Update existing teacher if needed
                    // Add any fields that need to be updated
                } else {
                    // Create new teacher if none exists
                    return response()->json([
                        'success' => false,
                        'message' => 'Teacher with this NIP not found',
                    ], 404);
                }

                // Update existing user
                $user->update([
                    'name' => $request->name,
                    'password' => Hash::make($request->password),
                    'userable_id' => $teacher->id,
                    'userable_type' => Teacher::class,
                ]);
                $user->assignRoleWithGuard('teacher');
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

    public function logout()
    {
        auth()->guard('api')->logout();

        return response()->json([
            'success' => true,
            'message' => 'Successfully logged out',
        ]);
    }
}
