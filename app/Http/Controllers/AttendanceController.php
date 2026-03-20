<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Classroom;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $classrooms = Classroom::with('teacher')
            ->where('teacher_id', Auth::id())
            ->get();

        return Inertia::render('attendances/index', [
            'classrooms' => $classrooms,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Classroom $classroom)
    {
        abort_if($classroom->teacher_id !== Auth::id(), 403);

        $data = $request->validate([
            'date' => 'required|date',
            'attendances' => 'required|array',
            'attendances.*.student_id' => 'required|exists:students,id',
            'attendances.*.status' => 'required|in:present,absent,permission',
        ]);

        $date = Carbon::parse($data['date'])->toDateString(); // ✅ cast once

        foreach ($data['attendances'] as $record) {
            Attendance::updateOrCreate(
                [
                    'student_id' => $record['student_id'],
                    'date' => $date,
                ],
                [
                    'class_id' => $classroom->id,
                    'status' => $record['status'],
                    'marked_by' => Auth::id(),
                ]
            );
        }

        return back()->with('success', 'Attendance saved successfully.');
    }

    /**
     * Display the specified resource.
     */
    // public function show(Classroom $classroom, Request $request)
    // {
    //     abort_if($classroom->teacher_id !== Auth::id(), 403);

    //     $date = $request->query('date', today()->toDateString());
    //     $students = $classroom->students;

    //     // Get existing attendance for this date
    //     $attendances = Attendance::where('class_id', $classroom->id)
    //         ->where('date', $date)
    //         ->get()
    //         ->keyBy('student_id');

    //     $attendanceCounts = Attendance::where('class_id', $classroom->id)
    //         ->selectRaw('student_id,
    //         COUNT(*) as total,
    //         SUM(CASE WHEN status = "present" THEN 1 ELSE 0 END) as present,
    //         SUM(CASE WHEN status = "absent" THEN 1 ELSE 0 END) as absent,
    //         SUM(CASE WHEN status = "permission" THEN 1 ELSE 0 END) as permission
    //     ')
    //         ->groupBy('student_id')
    //         ->get()
    //         ->keyBy('student_id');

    //     // Merge attendance status into students
    //     $students = $students->map(function ($student) use ($attendances) {
    //         return [
    //             'id' => $student->id,
    //             'name' => $student->name,
    //             'student_code' => $student->student_code,
    //             'status' => $attendances[$student->id]->status ?? 'present',
    //             'count' => [
    //                 'total' => $counts->total ?? 0,
    //                 'absent' => $counts->absent ?? 0,
    //                 'permission' => $counts->permission ?? 0,
    //             ],
    //         ];
    //     });
    //     $alreadyMarked = Attendance::where('class_id', $classroom->id)
    //         ->where('date', $date)
    //         ->exists();

    //     return Inertia::render('attendances/show', [
    //         'classroom' => $classroom,
    //         'students' => $students,
    //         'date' => $date,
    //         'alreadyMarked' => $alreadyMarked,
    //     ]);
    // }
    public function show(Classroom $classroom, Request $request)
    {
        abort_if($classroom->teacher_id !== Auth::id(), 403);

        $date = $request->query('date', today()->format('Y-m-d'));

        $students = $classroom->students;

        $attendances = Attendance::where('class_id', $classroom->id)
            ->where('date', $date)
            ->get()
            ->keyBy('student_id');

        // ✅ Count attendance per student across all dates
        $attendanceCounts = Attendance::where('class_id', $classroom->id)
            ->selectRaw('student_id, 
            COUNT(*) as total,
            SUM(CASE WHEN status = "present" THEN 1 ELSE 0 END) as present,
            SUM(CASE WHEN status = "absent" THEN 1 ELSE 0 END) as absent,
            SUM(CASE WHEN status = "permission" THEN 1 ELSE 0 END) as permission
        ')
            ->groupBy('student_id')
            ->get()
            ->keyBy('student_id');

        $students = $students->map(function ($student) use ($attendances, $attendanceCounts) {
            $counts = $attendanceCounts[$student->id] ?? null;

            return [
                'id' => $student->id,
                'name' => $student->name,
                'student_code' => $student->student_code,
                'status' => $attendances[$student->id]->status ?? 'present',
                'counts' => [                             // ✅
                    'total' => $counts->total ?? 0,
                    'present' => $counts->present ?? 0,
                    'absent' => $counts->absent ?? 0,
                    'permission' => $counts->permission ?? 0,
                ],
            ];
        });

        $alreadyMarked = Attendance::where('class_id', $classroom->id)
            ->where('date', $date)
            ->exists();

        return Inertia::render('attendances/show', [
            'classroom' => $classroom,
            'students' => $students,
            'date' => $date,
            'alreadyMarked' => $alreadyMarked,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
