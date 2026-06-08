@extends('layouts.app')

@section('title', 'お問い合わせフォーム')

@section('content')
    <div class="pageHeader">
        <div>
            <h1>お問い合わせ</h1>
            <p>お問い合わせ内容を入力してください。</p>
        </div>

        @auth
            @if (auth()->user()->role === 'admin')
                <a href="{{ route('admin.inquiries.index') }}" class="button subButton">
                    管理者画面へ
                </a>
            @else
                <a href="{{ route('my.inquiries.index') }}" class="button subButton">
                    自分の問い合わせ一覧へ
                </a>
            @endif
        @endauth
    </div>

    <form method="POST" action="{{ route('inquiries.store') }}">
        @csrf

        <div class="formGroup">
            <label for="name">お名前</label>
            <input
                type="text"
                id="name"
                name="name"
                value="{{ old('name', auth()->user()->name ?? '') }}"
                placeholder="山田 太郎"
            >
            @error('name')
                <p class="errorText">{{ $message }}</p>
            @enderror
        </div>

        <div class="formGroup">
            <label for="email">メールアドレス</label>
            <input
                type="email"
                id="email"
                name="email"
                value="{{ old('email', auth()->user()->email ?? '') }}"
                placeholder="sample@example.com"
            >
            @error('email')
                <p class="errorText">{{ $message }}</p>
            @enderror
        </div>

        <div class="formGroup">
            <label for="title">件名</label>
            <input
                type="text"
                id="title"
                name="title"
                value="{{ old('title') }}"
                placeholder="お問い合わせの件名"
            >
            @error('title')
                <p class="errorText">{{ $message }}</p>
            @enderror
        </div>

        <div class="formGroup">
            <label for="category">カテゴリ</label>
            <select id="category" name="category">
                <option value="">選択してください</option>
                <option value="質問" @selected(old('category') === '質問')>質問</option>
                <option value="相談" @selected(old('category') === '相談')>相談</option>
                <option value="不具合" @selected(old('category') === '不具合')>不具合</option>
                <option value="その他" @selected(old('category') === 'その他')>その他</option>
            </select>
            @error('category')
                <p class="errorText">{{ $message }}</p>
            @enderror
        </div>

        <div class="formGroup">
            <label for="body">お問い合わせ内容</label>
            <textarea
                id="body"
                name="body"
                rows="6"
                placeholder="お問い合わせ内容を入力してください"
            >{{ old('body') }}</textarea>
            @error('body')
                <p class="errorText">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit" class="button">
            送信する
        </button>
    </form>
@endsection
