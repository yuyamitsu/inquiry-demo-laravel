@extends('layouts.app')

@section('title', 'パスワード変更')
@section('breadcrumbs')
    @if (in_array(auth()->user()->role, ['admin', 'staff'], true))
        <a href="{{ route('admin.dashboard.index') }}">ダッシュボード</a>
        <span class="breadcrumbSeparator">＞</span>
    @else
        <a href="{{ route('my.inquiries.index') }}">自分の問い合わせ</a>
        <span class="breadcrumbSeparator">＞</span>
    @endif

    <span>パスワード変更</span>
@endsection

@section('content')
    <div class="pageHeader">
        <div>
            <h1>パスワード変更</h1>
            <p>現在のパスワードを確認し、新しいパスワードに変更できます。</p>
        </div>

        <div class="pageActions">
            @if (in_array(auth()->user()->role, ['admin', 'staff'], true))
                <a href="{{ route('admin.dashboard.index') }}" class="button subButton">
                    ダッシュボードへ
                </a>
            @else
                <a href="{{ route('my.inquiries.index') }}" class="button subButton">
                    自分の問い合わせへ
                </a>
            @endif
        </div>
    </div>

    <div class="card">
        @if ($errors->any())
            <div class="errorBox">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('my.password.update') }}">
            @csrf
            @method('PUT')

            <div class="formGroup">
                <label for="current_password">現在のパスワード</label>
                <div class="passwordInputWrap">
                    <input
                        type="password"
                        id="current_password"
                        name="current_password"
                        autocomplete="current-password"
                        required
                    >
                    <button
                        type="button"
                        class="passwordToggleButton"
                        data-target="current_password"
                    >
                        表示
                    </button>
                </div>
            </div>

            <div class="formGroup">
                <label for="password">新しいパスワード</label>
                <div class="passwordInputWrap">
                    <input
                        type="password"
                        id="password"
                        name="password"
                        autocomplete="new-password"
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
                <label for="password_confirmation">新しいパスワード確認</label>
                <div class="passwordInputWrap">
                    <input
                        type="password"
                        id="password_confirmation"
                        name="password_confirmation"
                        autocomplete="new-password"
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
                パスワードを変更する
            </button>
        </form>
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
