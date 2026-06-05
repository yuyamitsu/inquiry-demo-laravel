@extends('layouts.app')

@section('title', '管理者ログイン')

@section('content')
    <div class="loginWrapper">
        <div class="loginCard">
            <h1>管理者ログイン</h1>
            <p>問い合わせ管理画面にアクセスするにはログインしてください。</p>

            @if (session('success'))
                <div class="successMessage">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="errorBox">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('login.store') }}" class="formArea">
                @csrf

                <div class="formGroup">
                    <label for="email">メールアドレス</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                    >
                </div>

                <div class="formGroup">
                    <label for="password">パスワード</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        required
                    >
                </div>

                <button type="submit" class="button">
                    ログイン
                </button>
            </form>

            <div class="loginDemoInfo">
                <p>Demo用ログイン情報</p>
                <p>メールアドレス：admin@example.com</p>
                <p>パスワード：password</p>
            </div>
        </div>
    </div>
@endsection
