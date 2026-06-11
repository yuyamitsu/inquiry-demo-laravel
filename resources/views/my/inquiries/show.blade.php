@extends('layouts.app')

@section('title', '自分の問い合わせ詳細')
@section('breadcrumbs')
    <a href="{{ route('my.inquiries.index') }}">自分の問い合わせ一覧</a>
    <span class="breadcrumbSeparator">＞</span>
    <span>問い合わせ詳細 #{{ $inquiry->id }}</span>
@endsection

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

            <dt>優先度</dt>
            <dd>
                @php
                    $priorityClass = match ($inquiry->priority) {
                        '低' => 'priorityLow',
                        '中' => 'priorityMiddle',
                        '高' => 'priorityHigh',
                        '緊急' => 'priorityUrgent',
                        default => 'priorityUnset',
                    };
                @endphp

                <span class="priorityBadge {{ $priorityClass }}">
                    {{ $inquiry->priority ?? '未設定' }}
                </span>
            </dd>

            <dt>対応期限</dt>
            <dd>
                @php
                    $dueDate = $inquiry->due_date
                        ? \Carbon\Carbon::parse($inquiry->due_date)
                        : null;

                    $dueClass = 'dueUnset';

                    if ($dueDate) {
                        if ($dueDate->isPast() && ! $dueDate->isToday() && $inquiry->status !== 'クローズ') {
                            $dueClass = 'dueOver';
                        } elseif ($dueDate->isToday() && $inquiry->status !== 'クローズ') {
                            $dueClass = 'dueToday';
                        } else {
                            $dueClass = 'dueNormal';
                        }
                    }
                @endphp

                <span class="dueBadge {{ $dueClass }}">
                    @if ($dueDate)
                        {{ $dueDate->format('Y/m/d') }}

                        @if ($dueClass === 'dueOver')
                            （期限切れ）
                        @elseif ($dueClass === 'dueToday')
                            （今日まで）
                        @endif
                    @else
                        未設定
                    @endif
                </span>
            </dd>

            <dt>受付日時</dt>
            <dd>{{ $inquiry->created_at->timezone('Asia/Tokyo')->format('Y/m/d H:i') }}</dd>

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
                    <span>{{ $comment->created_at->timezone('Asia/Tokyo')->format('Y/m/d H:i') }}</span>
                    <span>投稿者：{{ $comment->user?->name ?? '不明' }}</span>

                    @if ($comment->user?->role === 'admin')
                        <span class="commentRole adminRole">運営</span>
                    @else
                        <span class="commentRole userRole">利用者</span>
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
