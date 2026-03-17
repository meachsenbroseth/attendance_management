<form method="POST" action="{{ route('attendances.store') }}">
    @csrf

    <select name="class_id">
        @foreach ($classes as $class)
            <option value="{{ $class->id }}">{{ $class->name }}</option>
        @endforeach
    </select>

    <select name="student_id">
        @foreach ($students as $student)
            <option value="{{ $student->id }}">{{ $student->name }}</option>
        @endforeach
    </select>

    <input type="date" name="date" value="{{ now()->toDateString() }}">

    <select name="status">
        <option value="present">Present</option>
        <option value="absent">Absent</option>
        <option value="late">Late</option>
    </select>

    <button type="submit">Save</button>
</form>

