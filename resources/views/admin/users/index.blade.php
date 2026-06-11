@extends('layouts.app')

@section('title', 'ユーザー管理')
@section('breadcrumbs')
    <a href="{{ route('admin.inquiries.index') }}">問い合わせ一覧</a>
    <span class="breadcrumbSeparator">＞</span>
    <span>ユーザー管理</span>
@endsection

@section('content')
    <div class="pageHeader">
        <div>
            <h1>ユーザー管理</h1>
            <p>登録済みユーザーと権限、問い合わせ件数を確認できます。</p>
        </div>

        <a href="{{ route('admin.inquiries.index') }}" class="button subButton">
            問い合わせ一覧へ
        </a>
    </div>

    <form method="GET" action="{{ route('admin.users.index') }}" class="searchBox">
        <div class="searchItem searchKeyword">
            <label for="keyword">キーワード</label>
            <input
                type="text"
                id="keyword"
                name="keyword"
                value="{{ $keyword }}"
                placeholder="名前・メールアドレスで検索"
            >
        </div>

        <div class="searchItem">
            <label for="role">権限</label>
            <select id="role" name="role">
                <option value="">すべて</option>
                <option value="admin" @selected($role === 'admin')>管理者</option>
                <option value="staff" @selected($role === 'staff')>スタッフ</option>
                <option value="user" @selected($role === 'user')>一般ユーザー</option>
            </select>
        </div>

        <div class="searchActions">
            <button type="submit" class="button">検索</button>

            <a href="{{ route('admin.users.index') }}" class="button subButton">
                リセット
            </a>
        </div>
    </form>

    <p class="resultText">
        検索結果：{{ $users->total() }}件
    </p>

    <div class="tableWrapper">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>名前</th>
                    <th>メールアドレス</th>
                    <th>権限</th>
                    <th>問い合わせ件数</th>
                    <th>担当中件数</th>
                    <th>登録日時</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($users as $user)
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td>
                            <a href="{{ route('admin.users.show', $user) }}" class="tableLink">
                                {{ $user->name }}
                            </a>
                        </td>
                        <td>{{ $user->email }}</td>

                        <td>
                            @php
                                $roleLabel = match ($user->role) {
                                    'admin' => '管理者',
                                    'staff' => 'スタッフ',
                                    'user' => '一般ユーザー',
                                    default => $user->role,
                                };

                                $roleClass = match ($user->role) {
                                    'admin' => 'roleAdmin',
                                    'staff' => 'roleStaff',
                                    'user' => 'roleUser',
                                    default => 'roleUser',
                                };
                            @endphp

                            <span class="roleBadge {{ $roleClass }}">
                                {{ $roleLabel }}
                            </span>
                        </td>

                        <td>{{ $user->inquiries_count }}</td>
                        <td>{{ $user->assigned_inquiries_count }}</td>

                        <td>{{ $user->created_at->timezone('Asia/Tokyo')->format('Y/m/d H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">
                            条件に一致するユーザーはありません。
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="paginationArea">
        {{ $users->links() }}
    </div>
@endsection
