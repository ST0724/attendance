@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/admin_staff.css') }}" />
@endsection

@section('content')
    <div class="content">
        <h2 class="content-title">スタッフ一覧</h2>

        <div class="user-table">
            <table class="user-teble__inner">
                <tr class="user-table__row">
                    <th class="user-table__header">名前</th>
                    <th class="user-table__header">メールアドレス</th>
                    <th class="user-table__header">月次勤怠</th>
                </tr>
                @foreach($users as $user)
                <tr class="user-table__row">
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>
                        <a class="user-table__detail" href="/admin/attendance/staff/{{ $user['id'] }}">詳細</a>
                    </td>
                </tr>
                @endforeach
            </table>
        </div>
    </div>
@endsection