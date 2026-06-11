@extends('layouts.app')

@section('title', '仮パスワード再設定')

@section('content')
    <div class="pageHeader">
        <div>
            <h1>仮パスワード再設定</h1>
            <p>対象ユーザーのログインパスワードを管理者が再設定します。</p>
        </div>

        <a href="{{ route('admin.users.show', $user) }}" class="button subButton">
            ユーザー詳細へ戻る
        </a>
    </div>

    <section class="card">
        <h2>対象ユーザー</h2>

        <dl class="detailList">
            <dt>名前</dt>
            <dd>{{ $user->name }}</dd>

            <dt>メールアドレス</dt>
            <dd>{{ $user->email }}</dd>

            <dt>権限</dt>
            <dd>{{ $user->role }}</dd>
        </dl>
    </section>

    <section class="card">
        <h2>新しい仮パスワード</h2>

        <p class="emptyText">
            メール送信は行いません。設定後、管理者からユーザーへ仮パスワードを共有してください。
        </p>

        @if ($errors->any())
            <div class="errorBox">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.users.password.update', $user) }}">
            @csrf
            @method('PUT')

            <div class="formGroup">
                <label for="password">仮パスワード</label>
                <div class="passwordInputWrap">
                    <input
                        type="password"
                        id="password"
                        name="password"
                        required
                        autocomplete="new-password"
                    >
                    <button
                        type="button"
                        class="passwordToggleButton"
                        data-target="password"
                    >
                        表示
                    </button>
                </div>

                @error('password')
                    <p class="errorText">{{ $message }}</p>
                @enderror
            </div>

            <div class="formGroup">
                <label for="password_confirmation">仮パスワード確認</label>
                <div class="passwordInputWrap">
                    <input
                        type="password"
                        id="password_confirmation"
                        name="password_confirmation"
                        required
                        autocomplete="new-password"
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
                仮パスワードを再設定する
            </button>
        </form>
    </section>

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
