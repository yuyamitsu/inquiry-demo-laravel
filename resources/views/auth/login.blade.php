@extends('layouts.app')

@section('title', 'ログイン')

@section('content')
    <div class="loginWrapper">
        <div class="loginCard">
            <h1>ログイン</h1>
            <p>問い合わせ管理画面にアクセスするにはログインしてください。</p>

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
        </div>
    </div>
@endsection
