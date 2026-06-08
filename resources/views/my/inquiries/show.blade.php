@extends('layouts.app')

@section('title', '自分の問い合わせ詳細')

@section('content')
    <div class="pageHeader">
        <div>
            <h1>問い合わせ詳細</h1>
            <p>登録した問い合わせ内容と対応状況を確認できます。</p>
        </div>

        <a href="{{ route('my.inquiries.index') }}" class="button subButton">
            一覧へ戻る
        </a>
    </div>

    <section class="card">
        <h2>問い合わせ内容</h2>

        <dl class="detailList">
            <dt>ID</dt>
            <dd>{{ $inquiry->id }}</dd>

            <dt>件名</dt>
            <dd>{{ $inquiry->title }}</dd>

            <dt>カテゴリ</dt>
            <dd>{{ $inquiry->category }}</dd>

            <dt>ステータス</dt>
            <dd>
                @php
                    $statusClass = match ($inquiry->status) {
                        '未対応' => 'statusNew',
                        '対応中' => 'statusProgress',
                        '回答済み' => 'statusAnswered',
                        'クローズ' => 'statusClosed',
                        default => '',
                    };
                @endphp

                <span class="status {{ $statusClass }}">
                    {{ $inquiry->status }}
                </span>
            </dd>

            <dt>受付日時</dt>
            <dd>{{ $inquiry->created_at->format('Y/m/d H:i') }}</dd>

            <dt>お名前</dt>
            <dd>{{ $inquiry->name }}</dd>

            <dt>メールアドレス</dt>
            <dd>{{ $inquiry->email }}</dd>

            <dt>問い合わせ内容</dt>
            <dd>{!! nl2br(e($inquiry->body)) !!}</dd>
        </dl>
    </section>

    <section class="commentArea">
        <h2>コメントスレッド</h2>

        @forelse ($comments as $comment)
            <div class="commentItem">
                <div class="commentMeta">
                    <span>{{ $comment->created_at->format('Y/m/d H:i') }}</span>
                    <span>{{ $comment->user?->name ?? '不明' }}</span>

                    @if ($comment->user?->role === 'admin')
                        <span class="commentRole adminRole">管理者</span>
                    @else
                        <span class="commentRole userRole">一般ユーザー</span>
                    @endif
                </div>

                <p class="commentBody">
                    {!! nl2br(e($comment->body)) !!}
                </p>
            </div>
        @empty
            <p class="emptyText">コメントはまだありません。</p>
        @endforelse

        <form method="POST" action="{{ route('inquiries.comments.store', $inquiry) }}" class="commentForm">
            @csrf

            <div class="formGroup">
                <label for="commentBody">コメントを投稿</label>
                <textarea
                    id="commentBody"
                    name="body"
                    rows="4"
                    placeholder="コメントを入力してください"
                >{{ old('body') }}</textarea>

                @error('body')
                    <p class="errorText">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="button">
                コメント投稿
            </button>
        </form>
    </section>
@endsection
