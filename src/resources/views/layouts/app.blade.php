<!DOCTYPE html>
<html lang="ja">

<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>coachtech勤怠</title>
  <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}" />
  <link rel="stylesheet" href="{{ asset('css/common.css') }}" />
  @yield('css')
</head>


<body>
  <header class="header">
    <div class="header__inner">

      <a class="header__logo" href="/login">
        <img class="header__logo--image" src="{{ asset('storage/logo.svg') }}" alt="画像">
      </a>

      @if(Auth::guard('web')->check())
        <div class="header__nav">
          <button class="header__nav--button" onclick="location.href='/attendance'">勤怠</button>
          <button class="header__nav--button" onclick="location.href='/attendance/list'">勤怠一覧</button>
          <button class="header__nav--button" onclick="location.href='/stamp_correction_request/list'">申請</button>
          <form class="logout_form" action="/logout" method="post">
            @csrf
            <button class="header__nav--button">ログアウト</button>
          </form>
        </div>
      @elseif(Auth::guard('admin')->check())
        <div class="header__nav">
          <button class="header__nav--button" onclick="location.href='/admin/attendance/list/'">勤怠一覧</button>
          <button class="header__nav--button" onclick="location.href='/admin/staff/list'">スタッフ一覧</button>
          <button class="header__nav--button" onclick="location.href='/admin/stamp_correction_request/list'">申請一覧</button>
          <form class="logout_form" action="/logout" method="post">
            @csrf
            <button class="header__nav--button">ログアウト</button>
          </form>
        </div>
      @endif

    </div>
  </header>

  <main>
    @yield('content')
  </main>
  
</body>
</html>