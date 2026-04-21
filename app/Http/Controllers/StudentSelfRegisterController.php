<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\Student;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StudentSelfRegisterController extends Controller
{
    // Show the public form
    public function show(Classroom $classroom)
    {
        return Inertia::render('students/register', [
            'classroom' => $classroom->only('id', 'name'),
        ]);
    }

    // Handle student submission
    public function store(Request $request, Classroom $classroom)
    {
        $data = $request->validate([
            'name'   => 'required|string|max:255',
            'gender' => 'required|in:male,female',
        ]);

        $year = now()->year;
        $lastStudent = Student::whereYear('created_at', $year)
            ->orderByDesc('id')->first();

        $nextNumber = $lastStudent
            ? (int) substr($lastStudent->student_code, -4) + 1
            : 1;

        $code = 'STU-' . $year . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        $classroom->students()->create([
            'name'         => $data['name'],
            'gender'       => $data['gender'],
            'student_code' => $code,
        ]);

        return back()->with('success', 'You have been registered successfully!');
    }
}