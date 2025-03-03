@extends('layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('css/correction_request.css') }}" />
@endsection

@section('content')
    <div class="content">
        <h2 class="content-title">申請一覧</h2>

        <div class="category">
            @if(Auth::guard('web')->check())
                <a href="{{ url('/stamp_correction_request/list?tab=pending') }}" class="category__title {{ request()->fullUrlIs(url('/stamp_correction_request/list?tab=pending')) ? 'active' : '' }}">承認待ち</a>
                <a href="{{ url('/stamp_correction_request/list?tab=approved') }}" class="category__title {{ request()->fullUrlIs(url('/stamp_correction_request/list?tab=approved')) ? 'active' : '' }}">承認済み</a>
            @elseif(Auth::guard('admin')->check())
                <a href="{{ url('/admin/stamp_correction_request/list?tab=pending') }}" class="category__title {{ request()->fullUrlIs(url('/admin/stamp_correction_request/list?tab=pending')) ? 'active' : '' }}">承認待ち</a>
                <a href="{{ url('/admin/stamp_correction_request/list?tab=approved') }}" class="category__title {{ request()->fullUrlIs(url('/admin/stamp_correction_request/list?tab=approved')) ? 'active' : '' }}">承認済み</a>
            @endif
        </div>

        <div class="record-table">
            <table class="record-teble__inner">
                <tr class="record-table__row">
                    <th class="record-table__header">状態</th>
                    <th class="record-table__header">名前</th>
                    <th class="record-table__header">対象日時</th>
                    <th class="record-table__header">申請理由</th>
                    <th class="record-table__header">申請日時</th>
                    <th class="record-table__header">詳細</th>
                </tr>
                @foreach($records as $record)
                <tr class="record-table__row">
                    @if($record->approval === 0)
                        <td>承認待ち</td>
                    @else
                         <td>承認済み</td>
                    @endif
                    <td>{{ $record->user->name }}</td>
                    <td>{{ \Carbon\Carbon::parse($record->date)->format('Y/m/d') }}</td>
                    <td>{{ $record->remarks }}</td>
                    <td>{{ \Carbon\Carbon::parse($record->created_at)->format('Y/m/d') }}</td>
                    <td>
                        @if(Auth::guard('web')->check())
                            <a class="record-table__detail" href="/attendance/{{ $record['attendance_record_id'] }}">詳細</a>
                        @elseif(Auth::guard('admin')->check())
                            <a class="record-table__detail" href="/admin/stamp_correction_request/approve/{{ $record['id'] }}">詳細</a>
                        @endif
                    </td>
                </tr>
                @endforeach
            </table>
        </div>
    </div>
@endsection