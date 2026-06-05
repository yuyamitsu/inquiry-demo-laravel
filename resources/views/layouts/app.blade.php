<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', '問い合わせ管理Demo')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>
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
