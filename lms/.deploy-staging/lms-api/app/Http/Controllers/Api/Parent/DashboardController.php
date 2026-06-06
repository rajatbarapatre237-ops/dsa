<?php

namespace App\Http\Controllers\Api\Parent;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $studentId = (string) $request->attributes->get('api_user')['parent_id'];
        $today = now()->toDateString();

        $child = DB::table('stud_details')->where('id', $studentId)->first();
        $attendanceToday = DB::table('attendance')
            ->where('sid', $studentId)
            ->where('date', $today)
            ->first();

        $batch = $child->batch ?? '';
        $recentAssignments = DB::table('assignement')
            ->where('batch_name', $batch)
            ->where('status', 1)
            ->orderByDesc('id')
            ->limit(20)
            ->get();

        return response()->json([
            'status' => 'success',
            'dashboard' => [
                'child' => $child,
                'today_attendance' => [
                    'date' => $today,
                    'entry_time' => $attendanceToday->entry_time ?? 'Not Available',
                    'exit_time' => $attendanceToday->exit_time ?? 'Not Available',
                    'status' => $attendanceToday->status ?? '',
                ],
                'recent_assignments' => $recentAssignments,
                'menu' => [
                    ['key' => 'attendance', 'title' => 'Monthly Attendance'],
                    ['key' => 'assignments', 'title' => 'Assignments'],
                    ['key' => 'test_marks', 'title' => 'Test Marks'],
                    ['key' => 'class_tests', 'title' => 'Class Test Results'],
                ],
            ],
        ]);
    }

    public function attendance(Request $request): JsonResponse
    {
        $studentId = (string) $request->attributes->get('api_user')['parent_id'];
        $month = $request->query('month', now()->format('Y-m'));

        $records = DB::table('attendance')
            ->where('sid', $studentId)
            ->where('date', 'like', $month.'%')
            ->orderBy('date')
            ->get();

        return response()->json(['status' => 'success', 'month' => $month, 'records' => $records]);
    }
}
