@extends('layouts.app')

@section('title', 'FAQ編集')

@section('breadcrumbs')
    <a href="{{ route('admin.dashboard.index') }}">ダッシュボード</a>
    <span class="breadcrumbSeparator">＞</span>
    <a href="{{ route('admin.faqs.index') }}">FAQ管理</a>
    <span class="breadcrumbSeparator">＞</span>
    <span>FAQ編集 #{{ $faq->id }}</span>
@endsection

@section('content')
    <div class="pageHeader">
        <div>
            <h1>FAQ編集</h1>
            <p>ユーザー向けFAQの内容を編集します。</p>
        </div>

        <div class="pageActions">
            <a href="{{ route('admin.faqs.index') }}" class="button subButton">
                FAQ管理に戻る
            </a>
        </div>
    </div>

    <section class="card">
        <form method="POST" action="{{ route('admin.faqs.update', $faq) }}">
            @csrf
            @method('PUT')

            <div class="formGroup">
                <label for="title">タイトル</label>
                <input
                    type="text"
                    id="title"
                    name="title"
                    value="{{ old('title', $faq->title) }}"
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
                    value="{{ old('category', $faq->category) }}"
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
                    rows="10"
                >{{ old('body', $faq->body) }}</textarea>
                @error('body')
                    <p class="errorText">{{ $message }}</p>
                @enderror
            </div>

            <div class="formGroup">
                <label for="sort_order">表示順</label>
                <input
                    type="number"
                    id="sort_order"
                    name="sort_order"
                    value="{{ old('sort_order', $faq->sort_order) }}"
                    min="0"
                >
                @error('sort_order')
                    <p class="errorText">{{ $message }}</p>
                @enderror
            </div>

            <div class="formGroup">
                <label class="checkboxLabel">
                    <input
                        type="checkbox"
                        name="is_published"
                        value="1"
                        @checked(old('is_published', $faq->is_published))
                    >
                    <span>公開する</span>
                </label>
            </div>

            <button type="submit" class="button">
                更新する
            </button>
        </form>
    </section>
@endsection
