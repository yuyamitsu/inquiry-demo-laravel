@extends('layouts.app')

@section('title', '問い合わせ一覧')

@section('content')
    <div class="pageHeader">
        <div>
            <h1>問い合わせ一覧</h1>
            <p>登録された問い合わせを確認・検索・管理できます。</p>
        </div>

        <a href="{{ route('inquiries.create') }}" class="button subButton">
            問い合わせフォームへ
        </a>
    </div>

    <div class="summaryArea">
        <div class="summaryCard">
            <span class="summaryLabel">全件</span>
            <strong>{{ $totalCount }}</strong>
        </div>

        <div class="summaryCard">
            <span class="summaryLabel">未対応</span>
            <strong>{{ $newCount }}</strong>
        </div>

        <div class="summaryCard">
            <span class="summaryLabel">対応中</span>
            <strong>{{ $progressCount }}</strong>
        </div>

        <div class="summaryCard">
            <span class="summaryLabel">回答済み</span>
            <strong>{{ $answeredCount }}</strong>
        </div>

        <div class="summaryCard">
            <span class="summaryLabel">クローズ</span>
            <strong>{{ $closedCount }}</strong>
        </div>
    </div>

    <form method="GET" action="{{ route('admin.inquiries.index') }}" class="searchBox">
        <div class="searchItem searchKeyword">
            <label for="keyword">キーワード</label>
            <input
                type="text"
                id="keyword"
                name="keyword"
                value="{{ $keyword }}"
                placeholder="件名・名前・メール・本文で検索"
            >
        </div>

        <div class="searchItem">
            <label for="status">ステータス</label>
            <select id="status" name="status">
                <option value="">すべて</option>
                <option value="未対応" @selected($status === '未対応')>未対応</option>
                <option value="対応中" @selected($status === '対応中')>対応中</option>
                <option value="回答済み" @selected($status === '回答済み')>回答済み</option>
                <option value="クローズ" @selected($status === 'クローズ')>クローズ</option>
            </select>
        </div>

        <div class="searchItem">
            <label for="category">カテゴリ</label>
            <select id="category" name="category">
                <option value="">すべて</option>
                <option value="質問" @selected($category === '質問')>質問</option>
                <option value="相談" @selected($category === '相談')>相談</option>
                <option value="不具合" @selected($category === '不具合')>不具合</option>
                <option value="その他" @selected($category === 'その他')>その他</option>
            </select>
        </div>

        <div class="searchItem">
            <label for="assignee_id">担当者</label>
            <select id="assignee_id" name="assignee_id">
                <option value="">すべて</option>
                @foreach ($assignees as $assignee)
                    <option value="{{ $assignee->id }}" @selected((string) $assigneeId === (string) $assignee->id)>
                        {{ $assignee->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="searchItem">
            <label for="priority">優先度</label>
            <select id="priority" name="priority">
                <option value="">すべて</option>
                <option value="低" @selected($priority === '低')>低</option>
                <option value="中" @selected($priority === '中')>中</option>
                <option value="高" @selected($priority === '高')>高</option>
                <option value="緊急" @selected($priority === '緊急')>緊急</option>
            </select>
        </div>

        <div class="searchItem">
            <label for="due_status">期限</label>
            <select id="due_status" name="due_status">
                <option value="">すべて</option>
                <option value="overdue" @selected($dueStatus === 'overdue')>期限切れ</option>
                <option value="today" @selected($dueStatus === 'today')>今日まで</option>
                <option value="unset" @selected($dueStatus === 'unset')>期限未設定</option>
            </select>
        </div>

        <div class="searchActions">
            <button type="submit" class="button">検索</button>

            <a href="{{ route('admin.inquiries.index') }}" class="button subButton">
                リセット
            </a>
        </div>
    </form>

    <p class="resultText">
        検索結果：{{ $inquiries->total() }}件
    </p>

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
                    <th>名前</th>
                    <th>カテゴリ</th>
                    <th>受付日時</th>
                    <th>削除</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($inquiries as $inquiry)
                    <tr>
                        <td>{{ $inquiry->id }}</td>

                        <td>
                            <a href="{{ route('admin.inquiries.show', $inquiry) }}" class="tableLink">
                                {{ $inquiry->title }}
                            </a>
                        </td>

                        <td>
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
                        </td>

                        <td>{{ $inquiry->assignee?->name ?? '未設定' }}</td>

                        <td>
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
                        </td>
                        <td>
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
                                @else
                                    未設定
                                @endif
                            </span>
                        </td>

                        <td>{{ $inquiry->name }}</td>

                        <td>{{ $inquiry->category }}</td>

                        <td>{{ $inquiry->created_at->timezone('Asia/Tokyo')->format('Y/m/d H:i') }}</td>

                        <td>
                            <form
                                method="POST"
                                action="{{ route('admin.inquiries.destroy', $inquiry) }}"
                                onsubmit="return confirm('この問い合わせを削除しますか？');"
                            >
                                @csrf
                                @method('DELETE')

                                <button type="submit" class="button deleteButton smallButton">
                                    削除
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10">
                            条件に一致する問い合わせはありません。
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="paginationArea">
        {{ $inquiries->links() }}
    </div>
@endsection
