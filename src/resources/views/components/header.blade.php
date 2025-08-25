<header class="header">
    <div class="header__logo">
        <a href="/"><x-logo class="w-10 h-10 text-blue-500" /></a>
    </div>
    @if( !in_array(Route::currentRouteName(), ['register', 'login', 'verification.notice']) )

    <nav class="header__nav">
        <ul>
            @if(Auth::check())
            @if(Auth::user()->isAdmin())
            {{-- 管理者専用メニュー --}}
            <li><a href="">勤怠一覧</a></li>
            <li><a href="">スタッフ一覧</a></li>
            <li><a href="">申請一覧</a></li>
            @else
            {{-- 一般ユーザーメニュー --}}
            <li><a href="{{ route('attendance') }}">勤怠</a></li>
            <li><a href="{{ route('attendance.list') }}">勤怠一覧</a></li>
            <li><a href="{{ route('stamp_correction_request.list') }}">申請</a></li>
            <li>
                @endif
                <form action="/logout" method="post">
                    @csrf
                    <button class="header__logout">ログアウト</button>
                </form>
            </li>
            @endif
        </ul>
    </nav>
    @endif
</header>