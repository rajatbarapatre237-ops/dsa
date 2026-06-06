<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Parent\DashboardController as ParentDashboardController;
use App\Http\Controllers\Api\Parent\ParentController;
use App\Http\Controllers\Api\Student\DashboardController as StudentDashboardController;
use App\Http\Controllers\Api\Student\StudentController;
use App\Http\Controllers\Api\Teacher\DashboardController as TeacherDashboardController;
use App\Http\Controllers\Api\Teacher\AssignmentController;
use App\Http\Controllers\Api\Teacher\TeacherController;
use App\Http\Controllers\Api\Teacher\TeacherFormController;
use App\Http\Middleware\AuthenticateApiToken;
use App\Http\Middleware\EnsureApiRole;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('/auth/teacher/login', [AuthController::class, 'teacherLogin']);
    Route::post('/auth/student/login', [AuthController::class, 'studentLogin']);
    Route::post('/auth/parent/login', [AuthController::class, 'parentLogin']);

    Route::middleware(AuthenticateApiToken::class)->group(function () {
        Route::post('/auth/logout', [AuthController::class, 'logout']);

        Route::middleware(EnsureApiRole::class.':teacher')->prefix('teacher')->group(function () {
            Route::get('/dashboard', [TeacherDashboardController::class, 'index']);
            Route::get('/students', [TeacherDashboardController::class, 'students']);
            Route::get('/students/{id}', [TeacherDashboardController::class, 'showStudent']);
            Route::get('/assignments', [AssignmentController::class, 'index']);
            Route::get('/assignments/{id}', [AssignmentController::class, 'show']);
            Route::get('/assignments/{id}/file', [AssignmentController::class, 'download']);
            Route::post('/assignments', [AssignmentController::class, 'store']);
            Route::patch('/assignments/{id}/status', [AssignmentController::class, 'updateStatus']);
            Route::delete('/assignments/{id}', [AssignmentController::class, 'destroy']);
            Route::get('/form/all-batches', [AssignmentController::class, 'allBatches']);
            Route::get('/attendance', [TeacherController::class, 'attendance']);
            Route::get('/salary', [TeacherController::class, 'salary']);
            Route::get('/class-tests', [TeacherController::class, 'classTests']);
            Route::get('/test-results', [TeacherController::class, 'testResults']);
            Route::post('/change-password', [TeacherController::class, 'changePassword']);

            Route::get('/form/sessions', [TeacherFormController::class, 'sessions']);
            Route::get('/form/courses', [TeacherFormController::class, 'assignedCourses']);
            Route::get('/form/batches', [TeacherFormController::class, 'batches']);
            Route::get('/form/students', [TeacherFormController::class, 'studentsForAttendance']);
            Route::post('/form/attendance', [TeacherFormController::class, 'saveAttendance']);

            Route::get('/form/class-test/courses', [TeacherFormController::class, 'classTestCourses']);
            Route::get('/form/class-test/subjects', [TeacherFormController::class, 'classTestSubjects']);
            Route::get('/form/class-test/tests', [TeacherFormController::class, 'classTestList']);
            Route::post('/form/class-test', [TeacherFormController::class, 'createClassTest']);
            Route::get('/form/class-test/students', [TeacherFormController::class, 'studentsForMarks']);
            Route::post('/form/class-test/marks', [TeacherFormController::class, 'saveClassTestMarks']);
        });

        Route::middleware(EnsureApiRole::class.':student')->prefix('student')->group(function () {
            Route::get('/dashboard', [StudentDashboardController::class, 'index']);
            Route::get('/profile', [StudentDashboardController::class, 'profile']);
            Route::post('/profile', [StudentController::class, 'updateProfile']);
            Route::get('/courses', [StudentController::class, 'courses']);
            Route::get('/attendance', [StudentController::class, 'attendance']);
            Route::get('/assignments', [StudentController::class, 'assignments']);
            Route::get('/assignments/{id}', [StudentController::class, 'showAssignment']);
            Route::get('/assignments/{id}/file', [StudentController::class, 'downloadAssignment']);
            Route::get('/transactions', [StudentController::class, 'transactions']);
            Route::get('/class-test-results', [StudentController::class, 'classTestResults']);
            Route::get('/all-test-marks', [StudentController::class, 'allTestMarks']);
            Route::post('/change-password', [StudentController::class, 'changePassword']);
        });

        Route::middleware(EnsureApiRole::class.':parent')->prefix('parent')->group(function () {
            Route::get('/dashboard', [ParentDashboardController::class, 'index']);
            Route::get('/attendance', [ParentDashboardController::class, 'attendance']);
            Route::get('/assignments', [ParentController::class, 'assignments']);
            Route::get('/assignments/{id}', [ParentController::class, 'showAssignment']);
            Route::get('/assignments/{id}/file', [ParentController::class, 'downloadAssignment']);
            Route::get('/class-test-results', [ParentController::class, 'classTestResults']);
            Route::get('/all-test-marks', [ParentController::class, 'allTestMarks']);
            Route::post('/change-password', [ParentController::class, 'changePassword']);
        });
    });
});
