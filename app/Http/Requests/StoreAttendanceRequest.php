<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreAttendanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        return $user !== null && ($user->isAdmin() || $user->isTeacher());
    }

    public function rules(): array
    {
        return [
            'student_id' => ['required', 'exists:students,id'],
            'class_id' => ['required', 'exists:classrooms,id'],
            'date' => [
                'required',
                'date',
                Rule::unique('attendances')
                    ->where(fn ($query) => $query
                        ->where('student_id', $this->input('student_id'))
                        ->where('date', $this->input('date'))),
            ],
            'status' => ['required', Rule::in(['present', 'absent', 'late'])],
        ];
    }
}

