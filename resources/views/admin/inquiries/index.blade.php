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
    </div>

    <form method="GET" action="{{ route('admin.inquiries.index') }}" class="searchBox">
        <input
            type="text"
            name="keyword"
            value="{{ $keyword }}"
            placeholder="件名・名前・メール・本文で検索"
        >

        <select name="status">
            <option value="">すべてのステータス</option>
            <option value="未対応" @selected($status === '未対応')>未対応</option>
            <option value="対応中" @selected($status === '対応中')>対応中</option>
            <option value="回答済み" @selected($status === '回答済み')>回答済み</option>
            <option value="クローズ" @selected($status === 'クローズ')>クローズ</option>
        </select>

        <select name="category">
            <option value="">すべてのカテゴリ</option>
            <option value="質問" @selected($category === '質問')>質問</option>
            <option value="相談" @selected($category === '相談')>相談</option>
            <option value="不具合" @selected($category === '不具合')>不具合</option>
            <option value="その他" @selected($category === 'その他')>その他</option>
        </select>

        <button type="submit" class="button">検索</button>

        <a href="{{ route('admin.inquiries.index') }}" class="button subButton">
            リセット
        </a>
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
                    <th>名前</th>
                    <th>カテゴリ</th>
                    <th>ステータス</th>
                    <th>受付日時</th>
                    <th>詳細</th>
                    <th>削除</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($inquiries as $inquiry)
                    <tr>
                        <td>{{ $inquiry->id }}</td>
                        <td>{{ $inquiry->title }}</td>
                        <td>{{ $inquiry->name }}</td>
                        <td>{{ $inquiry->category }}</td>
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
                        <td>{{ $inquiry->created_at->format('Y/m/d H:i') }}</td>
                        <td>
                            <a href="{{ route('admin.inquiries.show', $inquiry) }}">
                                詳細
                            </a>
                        </td>
                        <td>
                            <form
                                method="POST"
                                action="{{ route('admin.inquiries.destroy', $inquiry) }}"
                                onsubmit="return confirm('この問い合わせを削除しますか？');"
                            >
                                @csrf
                                @method('DELETE')

                                <button type="submit" class="button deleteButton">
                                    削除
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8">
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