@extends('layouts.app')

@section('title', 'ユーザー登録')

@section('content')
    <div class="loginWrapper">
        <div class="loginCard">
            <h1>ユーザー登録</h1>
            <p>問い合わせを登録・確認するためのアカウントを作成します。</p>

            @if ($errors->any())
                <div class="errorBox">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register.store') }}" class="formArea">
                @csrf

                <div class="formGroup">
                    <label for="name">お名前</label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name') }}"
                        required
                    >
                </div>

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
                    <div class="passwordInputWrap">
                        <input
                            type="password"
                            id="password"
                            name="password"
                            required
                        >
                        <button
                            type="button"
                            class="passwordToggleButton"
                            data-target="password"
                        >
                            表示
                        </button>
                    </div>
                </div>

                <div class="formGroup">
                    <label for="password_confirmation">パスワード確認</label>
                    <div class="passwordInputWrap">
                        <input
                            type="password"
                            id="password_confirmation"
                            name="password_confirmation"
                            required
                        >
                        <button
                            type="button"
                            class="passwordToggleButton"
                            data-target="password_confirmation"
                        >
                            表示
                        </button>
                    </div>
                </div>

                <button type="submit" class="button">
                    登録する
                </button>
            </form>

            <div class="linkArea">
                <a href="{{ route('login') }}">
                    すでにアカウントをお持ちの方はこちら
                </a>
            </div>
        </div>
    </div>
    <script>
        document.querySelectorAll('.passwordToggleButton').forEach((button) => {
            button.addEventListener('click', () => {
                const targetId = button.dataset.target;
                const input = document.getElementById(targetId);

                if (!input) {
                    return;
                }

                if (input.type === 'password') {
                    input.type = 'text';
                    button.textContent = '非表示';
                } else {
                    input.type = 'password';
                    button.textContent = '表示';
                }
            });
        });
    </script>
@endsection
