@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/attendance_list.css') }}" />
@endsection

@section('content')
    <div class="content">
        <h2 class="content-title">勤怠一覧</h2>

        <div class="month">
            <a class="month__previous" href="{{ route('attendance.list', ['year' => $prev_month->year, 'month' => $prev_month->month]) }}">←前月</a>
            <div class="month__heading">
                <img src="{{ asset('storage/calendar.svg') }}" alt="カレンダー" class="month__heading--icon">
                <h3 class="month__heading--title">{{ $now->format('Y/m') }}</h3>
            </div>
            <a class="month__next" href="{{ route('attendance.list', ['year' => $next_month->year, 'month' => $next_month->month]) }}">翌月→</a>
        </div>

        <div class="record-table">
            <table class="record-teble__inner">
                <tr class="record-table__row">
                    <th class="record-table__header">日付</th>
                    <th class="record-table__header">出勤</th>
                    <th class="record-table__header">退勤</th>
                    <th class="record-table__header">休憩</th>
                    <th class="record-table__header">合計</th>
                    <th class="record-table__header">詳細</th>
                </tr>
                @foreach($records as $record)
                <tr class="record-table__row">
                    <td>
                        {{ \Carbon\Carbon::parse($record->date)->format('m/d'). \Carbon\Carbon::parse($record->date)->isoFormat('(ddd)') }}
                    </td>
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