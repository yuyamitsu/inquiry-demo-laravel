@extends('layouts.app')

@section('title', 'ユーザー詳細')

@section('content')
    <div class="pageHeader">
        <div>
            <h1>ユーザー詳細</h1>
            <p>ユーザー情報と関連する問い合わせを確認できます。</p>
        </div>

        <a href="{{ route('admin.users.index') }}" class="button subButton">
            ユーザー一覧へ
        </a>
    </div>

    <section class="card">
        <h2>ユーザー情報</h2>

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

        <dl class="detailList">
            <dt>ID</dt>
            <dd>{{ $user->id }}</dd>

            <dt>名前</dt>
            <dd>{{ $user->name }}</dd>

            <dt>メールアドレス</dt>
            <dd>{{ $user->email }}</dd>

            <dt>権限</dt>
            <dd>
                <span class="roleBadge {{ $roleClass }}">
                    {{ $roleLabel }}
                </span>
            </dd>

            <dt>登録日時</dt>
            <dd>{{ $user->created_at->timezone('Asia/Tokyo')->format('Y/m/d H:i') }}</dd>

            <dt>更新日時</dt>
            <dd>{{ $user->updated_at->timezone('Asia/Tokyo')->format('Y/m/d H:i') }}</dd>
        </dl>
    </section>

    <section class="card">
        <h2>このユーザーが登録した問い合わせ</h2>

        <div class="tableWrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>件名</th>
                        <th>ステータス</th>
                        <th>担当者</th>
                        <th>優先度</th>
                        <th>対応期限</th>
                        <th>受付日時</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($createdInquiries as $inquiry)
                        <tr>
                            <td>{{ $inquiry->id }}</td>
                            <td>
                                <a href="{{ route('admin.inquiries.show', $inquiry) }}" class="tableLink">
                                    {{ $inquiry->title }}
                                </a>
                            </td>
                            <td>{{ $inquiry->status }}</td>
                            <td>{{ $inquiry->assignee?->name ?? '未設定' }}</td>
                            <td>{{ $inquiry->priority ?? '未設定' }}</td>
                            <td>{{ $inquiry->due_date ?? '未設定' }}</td>
                            <td>{{ $inquiry->created_at->timezone('Asia/Tokyo')->format('Y/m/d H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">このユーザーが登録した問い合わせはありません。</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="paginationArea">
            {{ $createdInquiries->links() }}
        </div>
    </section>

    <section class="card">
        <h2>このユーザーが担当している問い合わせ</h2>

        <div class="tableWrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>件名</th>
                        <th>ステータス</th>
                        <th>登録者</th>
                        <th>優先度</th>
                        <th>対応期限</th>
                        <th>受付日時</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($assignedInquiries as $inquiry)
                        <tr>
                            <td>{{ $inquiry->id }}</td>
                            <td>
                                <a href="{{ route('admin.inquiries.show', $inquiry) }}" class="tableLink">
                                    {{ $inquiry->title }}
                                </a>
                            </td>
                            <td>{{ $inquiry->status }}</td>
                            <td>{{ $inquiry->user?->name ?? '不明' }}</td>
                            <td>{{ $inquiry->priority ?? '未設定' }}</td>
                            <td>{{ $inquiry->due_date ?? '未設定' }}</td>
                            <td>{{ $inquiry->created_at->timezone('Asia/Tokyo')->format('Y/m/d H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">このユーザーが担当している問い合わせはありません。</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="paginationArea">
            {{ $assignedInquiries->links() }}
        </div>
    </section>
@endsection
