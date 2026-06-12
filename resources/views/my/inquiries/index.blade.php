@extends('layouts.app')

@section('title', '自分の問い合わせ一覧')
@section('breadcrumbs')
    <span>自分の問い合わせ一覧</span>
@endsection

@section('content')
    <div class="pageHeader">
        <div>
            <h1>自分の問い合わせ一覧</h1>
            <p>登録した問い合わせの対応状況を確認できます。</p>
        </div>

        <a href="{{ route('inquiries.create') }}" class="button subButton">
            新しく問い合わせる
        </a>
    </div>

    <p class="resultText">
        問い合わせ件数：{{ $inquiries->total() }}件
    </p>

    <div class="tableWrapper">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>件名</th>
                    <th>ステータス</th>
                    <th>担当者</th>
                    <th>対応期限</th>
                    <th>受付日時</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($inquiries as $inquiry)
                    <tr>
                        <td>{{ $inquiry->id }}</td>

                        <td>
                            <a href="{{ route('my.inquiries.show', $inquiry) }}" class="tableLink">
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

                        <td>{{ $inquiry->created_at->timezone('Asia/Tokyo')->format('Y/m/d H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            登録した問い合わせはありません。
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
