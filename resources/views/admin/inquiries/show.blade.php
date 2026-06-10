@extends('layouts.app')

@section('title', '問い合わせ詳細')

@section('content')
    <h1>問い合わせ詳細</h1>

    <section class="card">
        <h2>問い合わせ内容</h2>

        <dl class="detailList">
            <dt>ID</dt>
            <dd>{{ $inquiry->id }}</dd>

            <dt>お名前</dt>
            <dd>{{ $inquiry->name }}</dd>

            <dt>メールアドレス</dt>
            <dd>{{ $inquiry->email }}</dd>

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

            <dt>担当者</dt>
            <dd>{{ $inquiry->assignee?->name ?? '未設定' }}</dd>

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

            <dt>問い合わせ内容</dt>
            <dd>{!! nl2br(e($inquiry->body)) !!}</dd>

            <dt>受付日時</dt>
            <dd>{{ $inquiry->created_at->timezone('Asia/Tokyo')->format('Y/m/d H:i') }}</dd>
        </dl>
    </section>

    <section class="card">
        <h2>管理項目</h2>

        <form method="POST" action="{{ route('admin.inquiries.update', $inquiry) }}">
            @csrf
            @method('PUT')

            <div class="formGroup">
                <label for="assignee_id">担当者</label>
                <select id="assignee_id" name="assignee_id">
                    <option value="">未設定</option>

                    @foreach ($assignees as $assignee)
                        <option
                            value="{{ $assignee->id }}"
                            @selected((string) old('assignee_id', $inquiry->assignee_id) === (string) $assignee->id)
                        >
                            {{ $assignee->name }}
                        </option>
                    @endforeach
                </select>

                @error('assignee_id')
                    <p class="errorText">{{ $message }}</p>
                @enderror
            </div>

            <div class="formGroup">
                <label for="priority">優先度</label>
                <select id="priority" name="priority">
                    <option value="">未設定</option>
                    <option value="低" @selected(old('priority', $inquiry->priority) === '低')>低</option>
                    <option value="中" @selected(old('priority', $inquiry->priority) === '中')>中</option>
                    <option value="高" @selected(old('priority', $inquiry->priority) === '高')>高</option>
                    <option value="緊急" @selected(old('priority', $inquiry->priority) === '緊急')>緊急</option>
                </select>

                @error('priority')
                    <p class="errorText">{{ $message }}</p>
                @enderror
            </div>

            <div class="formGroup">
                <label for="due_date">対応期限</label>
                <input
                    type="date"
                    id="due_date"
                    name="due_date"
                    value="{{ old('due_date', $inquiry->due_date) }}"
                >

                @error('due_date')
                    <p class="errorText">{{ $message }}</p>
                @enderror
            </div>

            <div class="formGroup">
                <label for="status">ステータス</label>
                <select id="status" name="status">
                    <option value="未対応" @selected(old('status', $inquiry->status) === '未対応')>未対応</option>
                    <option value="対応中" @selected(old('status', $inquiry->status) === '対応中')>対応中</option>
                    <option value="回答済み" @selected(old('status', $inquiry->status) === '回答済み')>回答済み</option>
                    <option value="クローズ" @selected(old('status', $inquiry->status) === 'クローズ')>クローズ</option>
                </select>

                @error('status')
                    <p class="errorText">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="button">
                保存する
            </button>

            <a href="{{ route('admin.inquiries.index') }}" class="button subButton">
                一覧に戻る
            </a>
        </form>
    </section>

    <section class="commentArea">
        <h2>コメントスレッド</h2>

        @forelse ($comments as $comment)
            <div class="commentItem">
                <div class="commentMeta">
                    <span>{{ $comment->created_at->timezone('Asia/Tokyo')->format('Y/m/d H:i') }}</span>
                    <span>投稿者：{{ $comment->user?->name ?? '不明' }}</span>

                    @if ($comment->user?->role === 'admin')
                        <span class="commentRole adminRole">管理者</span>
                    @elseif ($comment->user?->role === 'staff')
                        <span class="commentRole staffRole">担当者</span>
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

    <section class="historyArea">
        <h2>変更履歴</h2>

        @forelse ($logs as $log)
            <div class="historyItem">
                <div class="historyMeta">
                    <span>{{ $log->created_at->timezone('Asia/Tokyo')->format('Y/m/d H:i') }}</span>
                    <span>更新者：{{ $log->user?->name ?? '不明' }}</span>
                </div>

                <p class="historyMessage">
                    {{ $log->message }}
                </p>

                @if ($log->field_name && ($log->before_value || $log->after_value))
                    <div class="historyDetail">
                        <span class="historyField">{{ $log->field_name }}</span>

                        @if ($log->before_value !== null)
                            <span class="historyBefore">変更前：{{ $log->before_value }}</span>
                        @endif

                        @if ($log->after_value !== null)
                            <span class="historyAfter">変更後：{{ $log->after_value }}</span>
                        @endif
                    </div>
                @endif
            </div>
        @empty
            <p class="emptyText">変更履歴はありません。</p>
        @endforelse
    </section>
@endsection
