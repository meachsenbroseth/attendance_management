<?php /** @var \Illuminate\Contracts\Pagination\LengthAwarePaginator $attendances */ ?>

@foreach ($attendances as $attendance)
    {{ $attendance->id }}
@endforeach

