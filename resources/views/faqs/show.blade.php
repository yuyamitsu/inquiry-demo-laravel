@extends('layouts.app')

@section('title', 'FAQ詳細')

@section('breadcrumbs')
    <a href="{{ route('faqs.index') }}">FAQ</a>
    <span class="breadcrumbSeparator">＞</span>
    <span>{{ $faq->title }}</span>
@endsection

@section('content')
    <div class="pageHeader">
        <div>
            <h1>FAQ詳細</h1>
            <p>問い合わせ前に内容をご確認ください。</p>
        </div>

        <div class="pageActions">
            <a href="{{ route('faqs.index') }}" class="button subButton">
                FAQ一覧に戻る
            </a>
        </div>
    </div>

    <section class="card">
        <p class="faqMeta">
            {{ $faq->category ?? '未分類' }}
        </p>

        <h2>{{ $faq->title }}</h2>

        <div class="faqBody">
            {!! nl2br(e($faq->body)) !!}
        </div>
    </section>

    <section class="card">
        <h2>問い合わせへ進む</h2>
        <p>
            このFAQを確認しても解決しない場合は、以下にチェックを入れて問い合わせフォームへ進んでください。
        </p>

        <form method="POST" action="{{ route('faqs.confirm') }}">
            @csrf

            <div class="formGroup">
                <label>
                    <input type="checkbox" name="faq_checked" value="1" required>
                    FAQを確認しました
                </label>

                @error('faq_checked')
                    <p class="errorText">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="button">
                解決しなかったので問い合わせる
            </button>
        </form>
    </section>
@endsection
