<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ApiTokenService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function __construct(private ApiTokenService $tokens) {}

    public function teacherLogin(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $teacher = DB::table('teacher')
            ->where('email', $data['email'])
            ->first();

        if (! $teacher || $teacher->password !== $data['password']) {
            return response()->json(['status' => 'error', 'message' => 'Invalid email or password'], 401);
        }

        $token = $this->tokens->issue([
            'role' => 'teacher',
            'email' => $teacher->email,
            'id' => $teacher->tid ?? $teacher->email,
        ]);

        return response()->json([
            'status' => 'success',
            'token' => $token,
            'user' => [
                'role' => 'teacher',
                'email' => $teacher->email,
                'name' => $teacher->name ?? $teacher->email,
            ],
        ]);
    }

    public function studentLogin(Request $request): JsonResponse
    {
        $data = $request->validate([
            'student_id' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $raw = strtoupper(trim($data['student_id']));
        $numericId = str_starts_with($raw, 'ACE') ? substr($raw, 3) : $raw;

        $user = DB::table('users')
            ->where('sid', $numericId)
            ->where('pass', $data['password'])
            ->first();

        if (! $user) {
            return response()->json(['status' => 'error', 'message' => 'Invalid student ID or password'], 401);
        }

        $sid = 'ACE'.$numericId;
        $token = $this->tokens->issue([
            'role' => 'student',
            'sid' => $sid,
            'id' => $numericId,
        ]);

        $profile = DB::table('stud_details')->where('id', $numericId)->first();

        return response()->json([
            'status' => 'success',
            'token' => $token,
            'user' => [
                'role' => 'student',
                'sid' => $sid,
                'id' => (int) $numericId,
                'name' => $profile->name ?? 'Student',
            ],
        ]);
    }

    public function parentLogin(Request $request): JsonResponse
    {
        $data = $request->validate([
            'student_id' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $studentId = trim($data['student_id']);

        $parent = DB::table('parent')
            ->where('id', $studentId)
            ->where('pass', $data['password'])
            ->first();

        if (! $parent) {
            return response()->json(['status' => 'error', 'message' => 'Invalid student ID or password'], 401);
        }

        $token = $this->tokens->issue([
            'role' => 'parent',
            'parent_id' => $studentId,
            'id' => $studentId,
        ]);

        $profile = DB::table('stud_details')->where('id', $studentId)->first();

        return response()->json([
            'status' => 'success',
            'token' => $token,
            'user' => [
                'role' => 'parent',
                'student_id' => $studentId,
                'child_name' => $profile->name ?? 'Student',
            ],
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $this->tokens->revoke($request->bearerToken());

        return response()->json(['status' => 'success', 'message' => 'Logged out']);
    }
}
