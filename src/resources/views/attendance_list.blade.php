@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/attendance_list.css') }}" />
@endsection

@section('content')
    <div class="content">
        <h2 class="content-title">勤怠一覧</h2>

        <div class="month">
            <img src="" alt="" class="month__icon">
        </div>

        <div class="record-table">
            <table class="record-teble__inner">
                <tr class="record-table__row">
                    <th class="recorf-table__header">日付</th>
                    <th class="recorf-table__header">出勤</th>
                    <th class="recorf-table__header">退勤</th>
                    <th class="recorf-table__header">休憩</th>
                    <th class="recorf-table__header">合計</th>
                    <th class="recorf-table__header">詳細</th>
                </tr>
                @foreach($records as $record)
                <tr class="record-table__row">
                    <td>{{ $record->date }}</td>
                    <td>{{ $record->clock_in ? Carbon\Carbon::parse($record->clock_in)->format('H:i') : '' }}</td>
                    <td>{{ $record->clock_out ? Carbon\Carbon::parse($record->clock_out)->format('H:i') : '' }}</td>
                    <td>{{ $record->total_break_time }}</td>
                    <td>{{ $record->total_work_time }}</td>
                    <td class="record-table__item"><span class="black">詳細</span></td>
                </tr>
                @endforeach
            </table>
        </div>
    </div>
@endsection