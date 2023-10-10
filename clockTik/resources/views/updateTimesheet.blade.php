@extends('layout')
@section('content')
    <h2>Update rooster van: {{ $worker->name }}</h2>
    @php
        $startShift = \Carbon\Carbon::parse($timesheet->ClockedIn)->format('H:i');
        $endShift = \Carbon\Carbon::parse($timesheet->ClockedOut)->format('H:i');
        $startBreak = $timesheet->BreakStart ? \Carbon\Carbon::parse($timesheet->BreakStart)->format('H:i') : null;
        $endBreak = $timesheet->BreakStop ? \Carbon\Carbon::parse($timesheet->BreakStop)->format('H:i') : null;
    @endphp
    <div class="formContainer">
        <h3>{{ \Carbon\Carbon::parse($timesheet->Month)->format('d/m/Y') }}</h3>
        <form action="{{ route('updateTimesheet') }}" class="updateTimesheet" method="POST">
            @csrf
            <input type="hidden" name="id" value="{{ $worker->id }}">
            <input type="hidden" name="timesheet" value="{{ $timesheet->id }}">
            <input type="hidden" name="type" value="{{$timesheet->type}}">
            @if ($timesheet->type == 'workday')
                <fieldset>
                    <legend>Gewerkte periode</legend>
                    <div>
                        <label for="startTime">Start:</label>
                        <input class="updateStartTime" name="startTime" type="time" value="{{ $startShift }}">
                        <label for="endTime">End:</label>
                        <input class="updateEndTime" type="time" name="endTime" value="{{ $endShift }}">
                    </div>
                </fieldset>
                <legend>Gepauzeerde periode</legend>
                <div>
                    <label for="startBreak">Start:</label>
                    <input class="updateStartBreak" type="time" name="startBreak" value="{{ $startBreak }}">
                    <label for="endBreak">End:</label>
                    <input type="time" name="endBreak" class="updateEndBreak" value="{{ $endBreak }}">
                </div>
                </fieldset>
                @else
                @php
            $specialDays = ['Ziek', 'Weerverlet', 'Onbetaald verlof', 'Betaald verlof', 'Feestdag', 'Solicitatie verlof'] 
            @endphp
            <select name="updateSpecial" size="1">
                @foreach ($specialDays as $specialDay)
                <option value="{{$specialDay}}" {{$specialDay == $timesheet->type? 'selected':''}}>{{$specialDay}}</option>
                @endforeach
            </select>
            @endif
            <input class="userNoteSubmit" type="submit" value="update">
        </form>
    </div>
    <form action="{{route('delete')}}" class="delete" method="POST">
        @csrf
        <input type="hidden" name="workerId" value="{{ $worker->id }}">
        <input type="hidden" name="deleteSheet" value="{{ $timesheet->id }}">
        <input type="hidden" name="date" value="{{$timesheet->Month}}">
        <input onclick="return confirm('zedde zeker ?')" class="submit" type="image" src="{{ asset('/images/1843344.png') }}"
                    name="deleteThisSheet" alt="Delete">
    </form>
    @if ($timesheet->userNote !== null)
        <fieldset class="userNoteContainer">
            <div><b>Notitie:</b></div>
            <div class="userNote">{{ $timesheet->userNote }}</div>
        </fieldset>
    @endif
@endsection
