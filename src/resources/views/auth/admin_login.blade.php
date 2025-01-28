@extends('layouts.app')

@section('css')
<link rel="stylesheet" href="{{ asset('css/login.css') }}" />
@endsection

@section('content')
    <div class="content">
        <h2 class="content__title">管理者ログイン</h2>

        <form class="form" action="/admin/login" method="post">
            @csrf
            <div class="form__group">
                <div class="form__group--title">
                    <span class="form__label">メールアドレス</span>
                </div>
                <div class="form__group--input">
                    <input type="email" name="email" value="{{ old('email') }}">
                </div>
                <div class="form__error">
                    @error('email')
                    {{ $message }}
                    @enderror
                </div>
            </div>

            <div class="form__group">
                <div class="form__group--title">
                    <span class="form__label">パスワード</span>
                </div>
                <div class="form__group--input">
                    <input type="password" name="password">
                </div>
                <div class="form__error">
                    @error('password')
                    {{ $message }}
                    @enderror
                </div>
            </div>
            
            <div class="form__button">
                <button class="form__button--submit" type="submit">管理者ログインする</button>
            </div>
        </form>
    </div>
@endsection