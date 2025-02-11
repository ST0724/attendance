@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/attendance_detail.css') }}" />
@endsection

@section('content')
    <div class="content">
        <h2 class="content-title">勤怠詳細</h2>

        <form class="record-table" action="/attendance/{{ $record['id'] }}" method="post">
            @csrf
            <table class="record-teble__inner">
                <tr class="record-table__row">
                    <th class="record-table__header">名前</th>
                    <td>
                        <h3>{{ $record->user->name }}</h3>
                    </td>
                </tr>
                <tr class="record-table__row">
                    <th class="record-table__header">日付</th>
                    <td>
                        <input type="text" class="record-table__date" name="year" value="{{ \Carbon\Carbon::parse($record->date)->format('Y年') }}">
                        <input type="text" class="record-table__date" name="date" value="{{ \Carbon\Carbon::parse($record->date)->format('n月j日') }}">
                    </td>
                </tr>
                <tr class="record-table__row">
                    <th class="record-table__header">出勤・退勤</th>
                    <td>
                        <input type="text" class="record-table__attendance" name="clock_in" 
                            value="{{ \Carbon\Carbon::parse($record->clock_in)->format('H:i') }}">
                        <span>～</span>
                        <input type="text" class="record-table__attendance" name="clock_out" 
                            value="{{ \Carbon\Carbon::parse($record->clock_out)->format('H:i') }}">
                    </td>
                </tr>
                @foreach($record->breakRecords as $breakRecord)
                    <tr class="record-table__row">
                        <th class="record-table__header">休憩</th>
                        <td>
                            <input type="text" class="record-table__break" name="break_start" 
                                value="{{ \Carbon\Carbon::parse($breakRecord->break_start)->format('H:i') }}">
                                <span>～</span>
                            <input type="text" class="record-table__break" name="break_end" 
                                value="{{ \Carbon\Carbon::parse($breakRecord->break_end)->format('H:i') }}">
                        </td>
                    </tr>
                @endforeach
                <tr class="record-table__row">
                    <th class="record-table__header">備考</th>
                    <td>
                        <textarea name="remarks"></textarea>
                    </td>
                </tr>
            </table>
        </form>
        <div class="correct">
            <button class="correct__button">修正</button>
        </div>
    </div>
@endsection