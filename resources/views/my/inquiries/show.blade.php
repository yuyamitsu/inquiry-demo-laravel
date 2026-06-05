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

    <div class="card">
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
    </div>

    <div class="card">
        <h2>管理者からの返答</h2>

        @if ($inquiry->admin_reply)
            <p>{!! nl2br(e($inquiry->admin_reply)) !!}</p>
        @else
            <p class="emptyText">まだ管理者からの返答はありません。</p>
        @endif
    </div>
@endsection
