<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Classroom;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $today = today()->format('Y-m-d');
        $user = Auth::user();

        if ($user->role === 'teacher') {
            $classroomCount = Classroom::where('teacher_id', $user->id)->count();

            return Inertia::render('Dashboard', [
                'role' => 'teacher',
                'classroomCount' => $classroomCount,
            ]);
        }

        // Stats cards
        $totalUsers = User::count();
        $totalTeachers = User::where('role', 'teacher')->count();
        $totalStudents = Student::count();
        $totalClassrooms = Classroom::count();

        // Today's attendance rate
        $todayAttendances = Attendance::where('date', $today)->count();
        $todayPresent = Attendance::where('date', $today)->where('status', 'present')->count();
        $todayRate = $todayAttendances > 0
            ? round(($todayPresent / $todayAttendances) * 100, 1)
            : 0;

        // Today's class attendance
        $classrooms = Classroom::with('students')->get();
        $todayClassAttendance = $classrooms->map(function ($classroom) use ($today) {
            $total = $classroom->students->count();
            $present = Attendance::where('class_id', $classroom->id)
                ->where('date', $today)
                ->where('status', 'present')
                ->count();

            return [
                'name' => $classroom->name,
                'present' => $present,
                'total' => $total,
                'rate' => $total > 0 ? round(($present / $total) * 100, 1) : 0,
            ];
        })->sortByDesc('rate')->values();

        // Weekly attendance trend (Mon–Fri this week)
        $weeklyTrend = collect();
        for ($i = 0; $i < 5; $i++) {
            $date = Carbon::now()->startOfWeek()->addDays($i)->format('Y-m-d');
            $total = Attendance::where('date', $date)->count();
            $present = Attendance::where('date', $date)->where('status', 'present')->count();
            $weeklyTrend->push([
                'day' => Carbon::parse($date)->format('D'),
                'rate' => $total > 0 ? round(($present / $total) * 100, 1) : 0,
            ]);
        }

        // Bottom stats
        $studentsPresent = Attendance::where('date', $today)->where('status', 'present')->count();
        $studentsAbsent = Attendance::where('date', $today)->where('status', 'absent')->count();
        $teachersActive = Attendance::where('date', $today)
            ->distinct('marked_by')
            ->count('marked_by');

        return Inertia::render('Dashboard', [
            'role' => 'admin',
            'stats' => [
                'totalUsers' => $totalUsers,
                'totalTeachers' => $totalTeachers,
                'totalStudents' => $totalStudents,
                'totalClassrooms' => $totalClassrooms,
                'todayRate' => $todayRate,
            ],
            'todayClassAttendance' => $todayClassAttendance,
            'weeklyTrend' => $weeklyTrend,
            'bottomStats' => [
                'studentsPresent' => $studentsPresent,
                'studentsAbsent' => $studentsAbsent,
                'teachersActive' => $teachersActive,
            ],
        ]);
    }
}
