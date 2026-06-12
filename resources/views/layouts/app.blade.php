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
                @if (in_array(auth()->user()->role, ['admin', 'staff'], true))
                    <a href="{{ route('admin.dashboard.index') }}" class="siteTitle">
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
                    <div class="userMenu">
                        <button type="button" class="userMenuButton">
                            {{ auth()->user()->name }} さん

                            @php
                                $roleLabel = match (auth()->user()->role) {
                                    'admin' => '管理者',
                                    'staff' => '担当者',
                                    'user' => '利用者',
                                    default => '不明',
                                };

                                $roleClass = match (auth()->user()->role) {
                                    'admin' => 'roleAdmin',
                                    'staff' => 'roleStaff',
                                    'user' => 'roleUser',
                                    default => '',
                                };
                            @endphp

                            <span class="roleBadge {{ $roleClass }}">
                                {{ $roleLabel }}
                            </span>

                            <span class="userMenuArrow">▼</span>
                        </button>

                        <div class="userMenuDropdown">
                            @if (in_array(auth()->user()->role, ['admin', 'staff'], true))
                                <a href="{{ route('admin.dashboard.index') }}">
                                    ダッシュボード
                                </a>

                                <a href="{{ route('admin.inquiries.index') }}">
                                    問い合わせ一覧
                                </a>

                                <a href="{{ route('admin.knowledge.index') }}">
                                    ナレッジ
                                </a>
                            @else
                                <a href="{{ route('my.inquiries.index') }}">
                                    自分の問い合わせ
                                </a>
                            @endif

                            <a href="{{ route('faqs.index') }}">
                                FAQ
                            </a>

                            @if (auth()->user()->role === 'admin')
                                <a href="{{ route('admin.faqs.index') }}">
                                    FAQ管理
                                </a>

                                <a href="{{ route('admin.users.index') }}">
                                    ユーザー管理
                                </a>
                            @endif

                            <a href="{{ route('my.password.edit') }}">
                                パスワード変更
                            </a>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit">
                                    ログアウト
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endauth
        </div>
    </header>

    <main class="container">
        @hasSection('breadcrumbs')
            <nav class="breadcrumb">
                @yield('breadcrumbs')
            </nav>
        @endif

        @if (session('success'))
            <div class="successMessage">
                {{ session('success') }}
            </div>
        @endif

        @yield('content')
    </main>
</body>
</html>
