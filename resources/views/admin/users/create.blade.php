@extends('layouts.app')

@section('title', 'ユーザー作成')

@section('breadcrumbs')
    <a href="{{ route('admin.dashboard.index') }}">ダッシュボード</a>
    <span class="breadcrumbSeparator">＞</span>
    <a href="{{ route('admin.users.index') }}">ユーザー管理</a>
    <span class="breadcrumbSeparator">＞</span>
    <span>ユーザー作成</span>
@endsection

@section('content')
    <div class="pageHeader">
        <div>
            <h1>ユーザー作成</h1>
            <p>管理者がユーザーを作成し、権限と仮パスワードを設定できます。</p>
        </div>

        <a href="{{ route('admin.users.index') }}" class="button subButton">
            ユーザー管理へ戻る
        </a>
    </div>

    <section class="card">
        <h2>新規ユーザー情報</h2>

        @if ($errors->any())
            <div class="errorBox">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.users.store') }}">
            @csrf

            <div class="formGroup">
                <label for="name">名前</label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    value="{{ old('name') }}"
                    required
                >

                @error('name')
                    <p class="errorText">{{ $message }}</p>
                @enderror
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

                @error('email')
                    <p class="errorText">{{ $message }}</p>
                @enderror
            </div>

            <div class="formGroup">
                <label for="role">権限</label>
                <select id="role" name="role" required>
                    <option value="">選択してください</option>
                    <option value="admin" @selected(old('role') === 'admin')>
                        管理者
                    </option>
                    <option value="staff" @selected(old('role') === 'staff')>
                        スタッフ
                    </option>
                    <option value="user" @selected(old('role') === 'user')>
                        一般ユーザー
                    </option>
                </select>

                @error('role')
                    <p class="errorText">{{ $message }}</p>
                @enderror
            </div>

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
                ユーザーを作成する
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
