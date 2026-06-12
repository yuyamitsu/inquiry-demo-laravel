@extends('layouts.app')

@section('title', 'FAQ詳細')

@section('breadcrumbs')
    @if (in_array(auth()->user()->role, ['admin', 'staff'], true))
        <a href="{{ route('admin.dashboard.index') }}">ダッシュボード</a>
        <span class="breadcrumbSeparator">＞</span>
    @else
        <a href="{{ route('my.inquiries.index') }}">自分の問い合わせ</a>
        <span class="breadcrumbSeparator">＞</span>
    @endif

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

    <section class="card faqDetailCard">
        <div class="faqMeta">
            <span class="faqCategory">
                {{ $faq->category ?? '未分類' }}
            </span>
        </div>

        <div class="faqQuestionBlock">
            <span class="faqQuestionLabel">Q</span>

            <h2>
                {{ $faq->title }}
            </h2>
        </div>

        <div class="faqAnswerBlock">
            <span class="faqAnswerLabel">A</span>

            <div class="faqAnswerBody">
                {!! nl2br(e($faq->body)) !!}
            </div>
        </div>
    </section>

    <section class="card faqActionCard">
        <div class="faqActionHeader">
            <span class="faqActionLabel">解決しない場合</span>

            <h2>問い合わせへ進む</h2>

            <p>
                このFAQを確認しても解決しない場合は、以下にチェックを入れて問い合わせフォームへ進んでください。
            </p>
        </div>

        <form method="POST" action="{{ route('faqs.confirm') }}">
            @csrf

            <div class="formGroup">
                <label class="checkboxLabel">
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
