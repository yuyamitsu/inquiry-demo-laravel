@extends('layouts.app')

@section('title', 'ナレッジ作成')
@section('breadcrumbs')
    <a href="{{ route('admin.inquiries.index') }}">問い合わせ一覧</a>

    @if ($inquiry)
        <span class="breadcrumbSeparator">＞</span>
        <a href="{{ route('admin.inquiries.show', $inquiry) }}">
            問い合わせ詳細 #{{ $inquiry->id }}
        </a>
    @endif

    <span class="breadcrumbSeparator">＞</span>
    <span>ナレッジ作成</span>
@endsection

@section('content')
    <div class="pageHeader">
        <div>
            <h1>ナレッジ作成</h1>
            <p>問い合わせ内容を元に、再利用できる対応ナレッジを作成します。</p>
        </div>

        @if ($inquiry)
            <a href="{{ route('admin.inquiries.show', $inquiry) }}" class="button subButton">
                問い合わせ詳細へ戻る
            </a>
        @endif
    </div>

    @if ($errors->any())
        <div class="errorBox">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if ($inquiry)
        <section class="card">
            <h2>元の問い合わせ</h2>

            <dl class="detailList">
                <dt>ID</dt>
                <dd>{{ $inquiry->id }}</dd>

                <dt>件名</dt>
                <dd>{{ $inquiry->title }}</dd>

                <dt>カテゴリ</dt>
                <dd>{{ $inquiry->category }}</dd>

                <dt>登録者</dt>
                <dd>{{ $inquiry->user?->name ?? '不明' }}</dd>

                <dt>担当者</dt>
                <dd>{{ $inquiry->assignee?->name ?? '未設定' }}</dd>
            </dl>
        </section>
    @endif

    <section class="card">
        <h2>ナレッジ記事</h2>

        <form method="POST" action="{{ route('admin.knowledge.store') }}">
            @csrf

            <input type="hidden" name="inquiry_id" value="{{ $inquiry?->id }}">

            <div class="formGroup">
                <label for="title">タイトル</label>
                <input
                    type="text"
                    id="title"
                    name="title"
                    value="{{ old('title', $title) }}"
                    required
                >
            </div>

            <div class="formGroup">
                <label for="category">カテゴリ</label>
                <input
                    type="text"
                    id="category"
                    name="category"
                    value="{{ old('category', $category) }}"
                    placeholder="例：ログイン、請求、操作方法"
                >
            </div>

            <div class="formGroup">
                <label for="body">本文</label>
                <textarea
                    id="body"
                    name="body"
                    rows="16"
                    required
                >{{ old('body', $body) }}</textarea>
            </div>

            <div class="formGroup checkboxGroup">
                <label>
                    <input
                        type="checkbox"
                        name="is_published"
                        value="1"
                        @checked(old('is_published', false))
                    >
                    公開する
                </label>
            </div>

            <button type="submit" class="button">
                ナレッジを保存する
            </button>
        </form>
    </section>
@endsection
