@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin_attendance_list.css') }}" />
@endsection

@section('content')
    <div class="content">
        <h2 class="content-title">{{ $now->format('Y/m/d') }}の勤怠</h2>

        <div class="day">
            <a class="day__previous" href="{{ route('admin.attendance.list', ['year' => $prev_day->year, 'month' => $prev_day->month, 'day' => $prev_day->day]) }}">←前日</a>
            <div class="day__heading">
                <img src="{{ asset('storage/calendar.svg') }}" alt="カレンダー" class="day__heading--icon">
                <h3 class="day__heading--title">{{ $now->format('Y/m/d') }}</h3>
            </div>
            <a class="day__next" href="{{ route('admin.attendance.list', ['year' => $next_day->year, 'month' => $next_day->month, 'day' => $next_day->day]) }}">翌日→</a>
        </div>

        <div class="record-table">
            <table class="record-teble__inner">
                <tr class="record-table__row">
                    <th class="record-table__header">名前</th>
                    <th class="record-table__header">出勤</th>
                    <th class="record-table__header">退勤</th>
                    <th class="record-table__header">休憩</th>
                    <th class="record-table__header">合計</th>
                    <th class="record-table__header">詳細</th>
                </tr>
                @foreach($records as $record)
                <tr class="record-table__row">
                    <td>{{ $record->user->name }}</td>
                    <td>{{ $record->clock_in ? Carbon\Carbon::parse($record->clock_in)->format('H:i') : '' }}</td>
                    <td>{{ $record->clock_out ? Carbon\Carbon::parse($record->clock_out)->format('H:i') : '' }}</td>
                    <td>{{ $record->total_break_time }}</td>
                    <td>{{ $record->total_work_time }}</td>
                    <td>
                        <a class="record-table__detail" href="/attendance/{{ $record['id'] }}">詳細</a>
                    </td>
                </tr>
                @endforeach
            </table>
        </div>
    </div>
@endsection