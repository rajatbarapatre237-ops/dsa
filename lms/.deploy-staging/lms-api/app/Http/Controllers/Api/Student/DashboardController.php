<?php

namespace App\Http\Controllers\Api\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $id = (string) $request->attributes->get('api_user')['id'];

        $profile = DB::table('stud_details')->where('id', $id)->first();
        $attendance = DB::table('attendance')
            ->selectRaw("sid, course, batch, COUNT(DISTINCT date) AS total_days,
                SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) AS present_days,
                SUM(CASE WHEN status = 'absent' THEN 1 ELSE 0 END) AS absent_days")
            ->where('sid', $id)
            ->groupBy('sid', 'course', 'batch')
            ->get();

        $batch = $profile->batch ?? '';
        $assignments = DB::table('assignement')
            ->where('batch_name', $batch)
            ->where('status', 1)
            ->orderByDesc('id')
            ->limit(20)
            ->get();

        return response()->json([
            'status' => 'success',
            'dashboard' => [
                'profile' => $profile,
                'attendance_summary' => $attendance,
                'recent_assignments' => $assignments,
                'menu' => [
                    ['key' => 'profile', 'title' => 'My Profile'],
                    ['key' => 'attendance', 'title' => 'Attendance'],
                    ['key' => 'assignments', 'title' => 'Assignments'],
                    ['key' => 'fees', 'title' => 'Fees & Transactions'],
                    ['key' => 'class_tests', 'title' => 'Class Test Results'],
                ],
            ],
        ]);
    }

    public function profile(Request $request): JsonResponse
    {
        $id = (string) $request->attributes->get('api_user')['id'];
        $profile = DB::table('stud_details')->where('id', $id)->first();

        return response()->json(['status' => 'success', 'profile' => $profile]);
    }
}
