@extends('layouts.app')

@section('title', 'ナレッジ編集')
@section('breadcrumbs')
    <a href="{{ route('admin.dashboard.index') }}">ダッシュボード</a>
    <span class="breadcrumbSeparator">＞</span>
    <a href="{{ route('admin.knowledge.index') }}">ナレッジ一覧</a>
    <span class="breadcrumbSeparator">＞</span>
    <a href="{{ route('admin.knowledge.show', $knowledgeArticle) }}">
        ナレッジ詳細 #{{ $knowledgeArticle->id }}
    </a>
    <span class="breadcrumbSeparator">＞</span>
    <span>ナレッジ編集</span>
@endsection

@section('content')
    <div class="pageHeader">
        <div>
            <h1>ナレッジ編集</h1>
            <p>作成済みのナレッジ記事を編集します。</p>
        </div>

        <div class="pageActions">
            <a href="{{ route('admin.knowledge.show', $knowledgeArticle) }}" class="button subButton">
                詳細へ戻る
            </a>

            <a href="{{ route('admin.knowledge.index') }}" class="button subButton">
                ナレッジ一覧へ
            </a>
        </div>
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

    @if ($knowledgeArticle->inquiry)
        <section class="card">
            <h2>元の問い合わせ</h2>

            <dl class="detailList">
                <dt>ID</dt>
                <dd>{{ $knowledgeArticle->inquiry->id }}</dd>

                <dt>件名</dt>
                <dd>
                    <a href="{{ route('admin.inquiries.show', $knowledgeArticle->inquiry) }}" class="tableLink">
                        {{ $knowledgeArticle->inquiry->title }}
                    </a>
                </dd>

                <dt>カテゴリ</dt>
                <dd>{{ $knowledgeArticle->inquiry->category }}</dd>
            </dl>
        </section>
    @endif

    <section class="card">
        <h2>ナレッジ記事</h2>

        <form method="POST" action="{{ route('admin.knowledge.update', $knowledgeArticle) }}">
            @csrf
            @method('PUT')

            <div class="formGroup">
                <label for="title">タイトル</label>
                <input
                    type="text"
                    id="title"
                    name="title"
                    value="{{ old('title', $knowledgeArticle->title) }}"
                    required
                >

                @error('title')
                    <p class="errorText">{{ $message }}</p>
                @enderror
            </div>

            <div class="formGroup">
                <label for="category">カテゴリ</label>
                <input
                    type="text"
                    id="category"
                    name="category"
                    value="{{ old('category', $knowledgeArticle->category) }}"
                    placeholder="例：ログイン、請求、操作方法"
                >

                @error('category')
                    <p class="errorText">{{ $message }}</p>
                @enderror
            </div>

            <div class="formGroup">
                <label for="body">本文</label>
                <textarea
                    id="body"
                    name="body"
                    rows="16"
                    required
                >{{ old('body', $knowledgeArticle->body) }}</textarea>

                @error('body')
                    <p class="errorText">{{ $message }}</p>
                @enderror
            </div>

            <div class="formGroup checkboxGroup">
                <label>
                    <input
                        type="checkbox"
                        name="is_published"
                        value="1"
                        @checked(old('is_published', $knowledgeArticle->is_published))
                    >
                    公開する
                </label>
            </div>

            <button type="submit" class="button">
                更新する
            </button>
        </form>
    </section>
@endsection
