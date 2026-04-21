<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\Student;
use App\Models\User;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class ClassroomController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        abort_unless($request->user()->isAdmin(), 403);

        $classrooms = Classroom::with('teacher')->latest()->paginate(10);

        return Inertia::render('classrooms/index', [
            'classrooms' => $classrooms,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        abort_unless($request->user()->isAdmin(), 403);

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
        abort_unless($request->user()->isAdmin(), 403);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'teacher_id' => 'required|exists:users,id',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('classrooms', 'public');
        }

        Classroom::create($data);

        return redirect()->route('classrooms.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Classroom $classroom)
    {
        abort_unless($request->user()->isAdmin(), 403);

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
    public function edit(Request $request, Classroom $classroom)
    {
        abort_unless($request->user()->isAdmin(), 403);

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
        abort_unless($request->user()->isAdmin(), 403);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'teacher_id' => 'required|exists:users,id',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile('image')) {
            // Delete old image
            if ($classroom->image) {
                Storage::disk('public')->delete($classroom->image);
            }
            $data['image'] = $request->file('image')->store('classrooms', 'public');
        }
        $classroom->update($data);

        return redirect()->route('classrooms.index');
    }

    /**
     * Delete class
     */
    public function destroy(Request $request, Classroom $classroom)
    {
        abort_unless($request->user()->isAdmin(), 403);

        $classroom->delete();

        return redirect()->route('classrooms.index');
    }

    /**
     *  Add student to class
     */
    public function addStudent(Request $request, Classroom $classroom)
    {
        abort_unless($request->user()->isAdmin(), 403);

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

        $code = 'STU-' . $year . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

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
    public function removeStudent(Request $request, Student $student)
    {
        abort_unless($request->user()->isAdmin(), 403);

        $student->delete();

        return back();
    }



    //generate qr
    public function qrcode(Request $request, Classroom $classroom)
    {
        abort_unless($request->user()->isAdmin(), 403);

        $url = route('students.register.show', $classroom->id);

        $renderer = new ImageRenderer(
            new RendererStyle(300),
            new SvgImageBackEnd()
        );

        $writer = new Writer($renderer);
        $svg = $writer->writeString($url);

        // ✅ Return as JSON with base64 so img tag loads correctly
        return response()->json([
            'svg' => 'data:image/svg+xml;base64,' . base64_encode($svg),
        ]);
    }
}
