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
     * Determine if the user can manage (edit/add students/qr) a classroom.
     */
    private function canManage(Request $request, Classroom $classroom): bool
    {
        $user = $request->user();
        return $user->isAdmin() || $user->id === $classroom->teacher_id;
    }

    /**
     * List classrooms — admin sees all, teacher sees their own.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        abort_unless(in_array($user->role, ['admin', 'teacher']), 403);

        $classrooms = Classroom::with('teacher')
            ->when($user->role === 'teacher', fn($q) => $q->where('teacher_id', $user->id))
            ->latest()
            ->paginate(10);

        return Inertia::render('classrooms/index', [
            'classrooms' => $classrooms,
            'canCreate'  => $user->isAdmin(),
        ]);
    }

    /**
     * Show create form — admin only.
     */
    public function create(Request $request)
    {
        abort_unless($request->user()->isAdmin(), 403);

        return Inertia::render('classrooms/create', [
            'teachers' => User::where('role', 'teacher')->get(['id', 'name']),
        ]);
    }

    /**
     * Store a new classroom — admin only.
     */
    public function store(Request $request)
    {
        abort_unless($request->user()->isAdmin(), 403);

        $data = $request->validate([
            'name'       => 'required|string|max:255',
            'teacher_id' => 'required|exists:users,id',
            'image'      => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('classrooms', 'public');
        }

        Classroom::create($data);

        return redirect()->route('classrooms.index')
            ->with('success', 'Classroom created successfully.');
    }

    /**
     * Show a classroom — admin or assigned teacher.
     */
    public function show(Request $request, Classroom $classroom)
    {
        abort_unless($this->canManage($request, $classroom), 403);

        return Inertia::render('classrooms/show', [
            'classroom' => $classroom->load(['students', 'teacher']),
            'canEdit'   => $request->user()->isAdmin(),
            'canDelete' => $request->user()->isAdmin(),
        ]);
    }

    /**
     * Show edit form — admin only.
     */
    public function edit(Request $request, Classroom $classroom)
    {
        abort_unless($request->user()->isAdmin(), 403);

        return Inertia::render('classrooms/edit', [
            'classroom' => $classroom->load('students'),
            'teachers'  => User::where('role', 'teacher')->get(['id', 'name']),
        ]);
    }

    /**
     * Update a classroom — admin only.
     */
    public function update(Request $request, Classroom $classroom)
    {
        abort_unless($request->user()->isAdmin(), 403);

        $data = $request->validate([
            'name'       => 'required|string|max:255',
            'teacher_id' => 'required|exists:users,id',
            'image'      => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($classroom->image) {
                Storage::disk('public')->delete($classroom->image);
            }
            $data['image'] = $request->file('image')->store('classrooms', 'public');
        }

        $classroom->update($data);

        return redirect()->route('classrooms.index')
            ->with('success', 'Classroom updated successfully.');
    }

    /**
     * Delete a classroom — admin only.
     */
    public function destroy(Request $request, Classroom $classroom)
    {
        abort_unless($request->user()->isAdmin(), 403);

        if ($classroom->image) {
            Storage::disk('public')->delete($classroom->image);
        }

        $classroom->delete();

        return redirect()->route('classrooms.index')
            ->with('success', 'Classroom deleted.');
    }

    /**
     * Add a student to a classroom — admin or assigned teacher.
     */
    public function addStudent(Request $request, Classroom $classroom)
    {
        abort_unless($this->canManage($request, $classroom), 403);

        $data = $request->validate([
            'name'   => 'required|string|max:255',
            'gender' => 'required|in:male,female',
        ]);

        $classroom->students()->create([
            'name'         => $data['name'],
            'gender'       => $data['gender'],
            'student_code' => $this->generateStudentCode(),
        ]);

        return back()->with('success', 'Student added successfully.');
    }

    /**
     * Remove a student from a classroom — admin or assigned teacher.
     */
    public function removeStudent(Request $request, Student $student)
    {
        abort_unless($this->canManage($request, $student->classroom), 403);

        $student->delete();

        return back()->with('success', 'Student removed.');
    }

    /**
     * Generate and return a QR code for student self-registration.
     * Admin or assigned teacher only.
     */
    public function qrcode(Request $request, Classroom $classroom)
    {
        abort_unless($this->canManage($request, $classroom), 403);

        $url = route('students.register.show', $classroom->id);

        $svg = (new Writer(
            new ImageRenderer(
                new RendererStyle(300),
                new SvgImageBackEnd()
            )
        ))->writeString($url);

        return response()->json([
            'svg' => 'data:image/svg+xml;base64,' . base64_encode($svg),
        ]);
    }

    /**
     * Generate a unique student code for the current year.
     */
    private function generateStudentCode(): string
    {
        $year = now()->year;

        $lastStudent = Student::whereYear('created_at', $year)
            ->orderByDesc('id')
            ->lockForUpdate()
            ->first();

        $next = $lastStudent
            ? (int) substr($lastStudent->student_code, -4) + 1
            : 1;

        return 'STU-' . $year . '-' . str_pad($next, 4, '0', STR_PAD_LEFT);
    }
}