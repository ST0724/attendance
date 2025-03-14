@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/approval.css') }}" />
@endsection

@section('content')
    <div class="content">
        <h2 class="content-title">勤怠詳細</h2>

        <form class="form" action="/admin/stamp_correction_request/approve/{{ $record['id'] }}" method="post">
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
                </table>
            </div>

                @if($record->approval === 0)
                    <div class="correct">
                        <button class="correct__button" type="submit">承認</button>
                    </div>
                @else
                    <div class="correct">
                        <button class="correct__button--approved" disabled>承認済み</button>
                    </div>
                @endif
            </div>
        </form>
    </div>
@endsection