<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', '問い合わせ管理Demo')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>
    <header class="siteHeader">
        <div class="siteHeaderInner">
            @auth
                @if (auth()->user()->role === 'admin')
                    <a href="{{ route('admin.inquiries.index') }}" class="siteTitle">
                        問い合わせ管理Demo
                    </a>
                @else
                    <a href="{{ route('my.inquiries.index') }}" class="siteTitle">
                        問い合わせ管理Demo
                    </a>
                @endif
            @else
                <a href="{{ route('login') }}" class="siteTitle">
                    問い合わせ管理Demo
                </a>
            @endauth

            @auth
                <div class="headerActions">
                    <span class="loginUserName">
                        {{ auth()->user()->name }} さん
                    </span>

                    <a href="{{ route('my.password.edit') }}" class="button subButton">
                        パスワード変更
                    </a>

                    <form method="POST" action="{{ route('logout') }}" class="logoutForm">
                        @csrf
                        <button type="submit" class="button subButton">
                            ログアウト
                        </button>
                    </form>
                </div>
            @endauth
        </div>
    </header>

    <main class="container">
        @if (session('success'))
            <div class="successMessage">
                {{ session('success') }}
            </div>
        @endif

        @yield('content')
    </main>
</body>
</html>
