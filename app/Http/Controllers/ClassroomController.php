<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ClassroomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $classrooms = Classroom::with('teacher')->latest()->paginate(10);

        return Inertia::render('classrooms/index', [
            'classrooms' => $classrooms,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $teachers = User::where('role', 'teacher')->get();

        return Inertia::render('classrooms/create', [
            'teachers' => $teachers,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'teacher_id' => 'required|exists:users,id',
        ]);

        Classroom::create($data);

        return redirect()->route('classrooms.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Classroom $classroom)
    {
        return Inertia::render('classrooms/show', [
            'classroom' => $classroom->load(['students', 'teacher']),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    /**
     * Show edit form
     */
    public function edit(Classroom $classroom)
    {
        $teachers = User::where('role', 'teacher')->get();

        return Inertia::render('classrooms/edit', [
            'classroom' => $classroom->load('students'),
            'teachers' => $teachers,
        ]);
    }

    /**
     * Update class
     */
    public function update(Request $request, Classroom $classroom)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'teacher_id' => 'required|exists:users,id',
        ]);

        $classroom->update($data);

        return redirect()->route('classrooms.index');
    }

    /**
     * Delete class
     */
    public function destroy(Classroom $classroom)
    {
        $classroom->delete();

        return redirect()->route('classrooms.index');
    }

    /**
     *  Add student to class
     */
    public function addStudent(Request $request, Classroom $classroom)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'gender' => 'required|in:male,female',
        ]);

        $year = now()->year;
        $lastStudent = Student::whereYear('created_at', $year)
            ->orderByDesc('id')
            ->first();

        $nextNumber = $lastStudent
            ? (int) substr($lastStudent->student_code, -4) + 1
            : 1;

        $code = 'STU-'.$year.'-'.str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        $classroom->students()->create([
            'name' => $data['name'],
            'gender' => $data['gender'],
            'student_code' => $code,
        ]);

        return back();
    }

    /**
     *  Remove student from class
     */
    public function removeStudent(Student $student)
    {
        $student->delete();

        return back();
    }
}
