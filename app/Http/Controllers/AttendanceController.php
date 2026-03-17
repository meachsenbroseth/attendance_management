<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAttendanceRequest;
use App\Models\Attendance;
use App\Models\Classroom;
use App\Models\Student;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $query = Attendance::query()
            ->with(['student', 'classroom', 'teacher'])
            ->orderByDesc('date');

        if ($user->isTeacher()) {
            $query->whereHas('classroom', function ($q) use ($user): void {
                $q->where('teacher_id', $user->id);
            });
        }

        $attendances = $query->paginate(25);

        return view('attendances.index', [
            'attendances' => $attendances,
        ]);
    }

    public function create(Request $request): View
    {
        $user = $request->user();

        $classesQuery = Classroom::query()->orderBy('name');

        if ($user->isTeacher()) {
            $classesQuery->where('teacher_id', $user->id);
        }

        $classes = $classesQuery->get();

        $students = collect();

        if ($classes->isNotEmpty()) {
            $students = Student::query()
                ->whereIn('class_id', $classes->pluck('id'))
                ->orderBy('name')
                ->get();
        }

        return view('attendances.create', [
            'classes' => $classes,
            'students' => $students,
        ]);
    }

    public function store(StoreAttendanceRequest $request): RedirectResponse
    {
        $user = $request->user();

        $classroom = Classroom::query()->findOrFail($request->integer('class_id'));

        if ($user->isTeacher() && $classroom->teacher_id !== $user->id) {
            abort(403);
        }

        $student = Student::query()->findOrFail($request->integer('student_id'));

        if ($student->class_id !== $classroom->id) {
            abort(422, 'Student does not belong to the selected class.');
        }

        Attendance::firstOrCreate(
            [
                'student_id' => $student->id,
                'date' => $request->date('date'),
            ],
            [
                'class_id' => $classroom->id,
                'status' => $request->string('status'),
                'marked_by' => $user->id,
            ],
        );

        return redirect()
            ->route('attendances.index')
            ->with('status', 'Attendance recorded.');
    }
}

