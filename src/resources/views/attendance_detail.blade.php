@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/attendance_detail.css') }}" />
@endsection

@section('content')
    <div class="content">
        <h2 class="content-title">勤怠詳細</h2>

        <form class="form" action="/attendance/{{ $record['id'] }}" method="post">
            @csrf
            <div class="record-table">
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
                                <p class="record-table__attendance">{{ \Carbon\Carbon::parse($record->date)->format('Y年') }}</p>
                                <p class="record-table__attendance">{{ \Carbon\Carbon::parse($record->date)->format('n月j日') }}</p>
                            </td>
                        </tr>
                    @if($record instanceof \App\Models\AttendanceRecord)
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
                        @foreach($record->breakRecords as $index => $breakRecord)
                            <tr class="record-table__row">
                                <th class="record-table__header">休憩</th>
                                <td>
                                    <input type="hidden" name="breaks[{{ $index }}][break_record_id]" value="{{ $breakRecord->id }}">
                                    <input type="text" class="record-table__break" name="breaks[{{ $index }}][break_start]" value="{{ \Carbon\Carbon::parse($breakRecord->break_start)->format('H:i') }}">
                                    <span>～</span>
                                    <input type="text" class="record-table__break" name="breaks[{{ $index }}][break_end]" value="{{ \Carbon\Carbon::parse($breakRecord->break_end)->format('H:i') }}">
                                </td>
                            </tr>
                        @endforeach
                        <tr class="record-table__row">
                            <th class="record-table__header">備考</th>
                            <td>
                                <textarea name="remarks"></textarea>
                            </td>
                        </tr>
                    @elseif($record instanceof \App\Models\AttendanceRequest)
                        <tr class="record-table__row">
                            <th class="record-table__header">出勤・退勤</th>
                            <td>
                                <p class="record-table__attendance">{{ \Carbon\Carbon::parse($record->clock_in)->format('H:i') }}</p>
                                <span>～</span>
                                <p class="record-table__attendance">{{ \Carbon\Carbon::parse($record->clock_out)->format('H:i') }}</p>
                            </td>
                        </tr>
                        @foreach($record->breakRequests as $index => $breakRequest)
                            <tr class="record-table__row">
                                <th class="record-table__header">休憩</th>
                                <td>
                                    <input type="hidden" name="breaks[{{ $index }}][break_request_id]" value="{{ $breakRequest->id }}">
                                        <p class="record-table__break">{{ \Carbon\Carbon::parse($breakRequest->break_start)->format('H:i') }}</p>
                                        <span>～</span>
                                        <p class="record-table__break">{{ \Carbon\Carbon::parse($breakRequest->break_end)->format('H:i') }}</p>
                                </td>
                            </tr>
                        @endforeach
                        <tr class="record-table__row">
                            <th class="record-table__header">備考</th>
                            <td>
                                <p class="record-table__attendance">{{ $record->remarks }}</p>
                            </td>
                        </tr>
                    @endif
                </table>
            </div>

                @if($record instanceof \App\Models\AttendanceRecord)
                    <div class="correct">
                        <button class="correct__button" type="submit">修正</button>
                    </div>
                @elseif($record instanceof \App\Models\AttendanceRequest)
                        <p class="correct__pending">*承認待ちのため修正はできません。</p>
                @endif
            </div>
        </form>
    </div>
@endsection