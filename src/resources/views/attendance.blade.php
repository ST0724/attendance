@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/attendance.css') }}" />
@endsection

@section('content')
    <div class="content">
        <div class="status">
            <label class="status__label">{{ $status }}</label>
        </div>
        <div class="date">
           {{ $now->format('Y年n月j日') . '(' . $now->isoFormat('ddd') . ')' }}
        </div>
        <div class="time">
            {{ $now->format('H:i') }}
        </div>
        <form action="/attendance" class="attendance" method="post">
            @csrf
            @if(Auth::user()->status_id == 1)
                <button class="attendance__button" name="action" value="clock_in">出勤</button>
            @elseif(Auth::user()->status_id == 2)
                <button class="attendance__button" name="action" value="clock_out">退勤</button>
                <button class="break__button" name="action" value="break_start">休憩入</button>
            @elseif(Auth::user()->status_id == 3)
                <button class="break__button" name="action" value="break_end">休憩戻</button>
            @elseif(Auth::user()->status_id == 4)
                <p class="attendance__clock-out">お疲れ様でした。</p>
            @endif
        </form>
    </div>
@endsection