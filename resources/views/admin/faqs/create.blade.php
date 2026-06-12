@extends('layouts.app')

@section('title', 'FAQ登録')

@section('breadcrumbs')
    <a href="{{ route('admin.faqs.index') }}">FAQ管理</a>
    <span class="breadcrumbSeparator">＞</span>
    <span>FAQ登録</span>
@endsection

@section('content')
    <div class="pageHeader">
        <div>
            <h1>FAQ登録</h1>
            <p>ユーザー向けに公開するFAQを登録します。</p>
        </div>

        <div class="pageActions">
            <a href="{{ route('admin.faqs.index') }}" class="button subButton">
                FAQ一覧に戻る
            </a>
        </div>
    </div>

    <section class="card">
        <form method="POST" action="{{ route('admin.faqs.store') }}">
            @csrf

            <div class="formGroup">
                <label for="title">タイトル</label>
                <input
                    type="text"
                    id="title"
                    name="title"
                    value="{{ old('title') }}"
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
                    value="{{ old('category') }}"
                    placeholder="例：ログイン、操作方法、パスワード"
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
                >{{ old('body') }}</textarea>
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
                    value="{{ old('sort_order', 0) }}"
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
                        @checked(old('is_published'))
                    >
                    <span>公開する</span>
                </label>
            </div>

            <button type="submit" class="button">
                登録する
            </button>
        </form>
    </section>
@endsection
