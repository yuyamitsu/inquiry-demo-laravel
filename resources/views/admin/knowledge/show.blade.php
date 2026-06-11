@extends('layouts.app')

@section('title', 'ナレッジ詳細')
@section('breadcrumbs')
    <a href="{{ route('admin.inquiries.index') }}">問い合わせ一覧</a>
    <span class="breadcrumbSeparator">＞</span>
    <a href="{{ route('admin.knowledge.index') }}">ナレッジ一覧</a>
    <span class="breadcrumbSeparator">＞</span>
    <span>ナレッジ詳細 #{{ $knowledgeArticle->id }}</span>
@endsection

@section('content')
    <div class="pageHeader">
        <div>
            <h1>ナレッジ詳細</h1>
            <p>問い合わせから作成した対応ナレッジを確認できます。</p>
        </div>

        <div class="pageActions">
            <a href="{{ route('admin.knowledge.edit', $knowledgeArticle) }}" class="button">
                編集する
            </a>

            <a href="{{ route('admin.knowledge.index') }}" class="button subButton">
                ナレッジ一覧へ
            </a>

            @if ($knowledgeArticle->inquiry)
                <a href="{{ route('admin.inquiries.show', $knowledgeArticle->inquiry) }}" class="button subButton">
                    元の問い合わせへ
                </a>
            @endif
        </div>
    </div>

    <section class="card">
        <h2>{{ $knowledgeArticle->title }}</h2>

        <dl class="detailList">
            <dt>カテゴリ</dt>
            <dd>{{ $knowledgeArticle->category ?? '未設定' }}</dd>

            <dt>公開状態</dt>
            <dd>
                @if ($knowledgeArticle->is_published)
                    <span class="statusBadge statusAnswered">公開</span>
                @else
                    <span class="statusBadge statusClosed">下書き</span>
                @endif
            </dd>

            <dt>作成者</dt>
            <dd>{{ $knowledgeArticle->creator?->name ?? '不明' }}</dd>

            <dt>作成日時</dt>
            <dd>{{ $knowledgeArticle->created_at->timezone('Asia/Tokyo')->format('Y/m/d H:i') }}</dd>

            <dt>更新日時</dt>
            <dd>{{ $knowledgeArticle->updated_at->timezone('Asia/Tokyo')->format('Y/m/d H:i') }}</dd>
        </dl>
    </section>

    <section class="card">
        <h2>本文</h2>

        <div class="knowledgeBody">
            {!! nl2br(e($knowledgeArticle->body)) !!}
        </div>
    </section>
@endsection
