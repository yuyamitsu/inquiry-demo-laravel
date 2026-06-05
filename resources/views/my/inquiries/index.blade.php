@extends('layouts.app')

@section('title', '自分の問い合わせ一覧')

@section('content')
    <div class="pageHeader">
        <div>
            <h1>自分の問い合わせ一覧</h1>
            <p>自分が登録した問い合わせを確認できます。</p>
        </div>

        <a href="{{ route('inquiries.create') }}" class="button">
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
                    <th>カテゴリ</th>
                    <th>ステータス</th>
                    <th>受付日時</th>
                    <th>詳細</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($inquiries as $inquiry)
                    <tr>
                        <td>{{ $inquiry->id }}</td>
                        <td>{{ $inquiry->title }}</td>
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
                            <a href="{{ route('my.inquiries.show', $inquiry) }}">
                                詳細
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            まだ問い合わせはありません。
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
