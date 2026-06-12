@extends('layouts.app')

@section('title', 'よくある質問')

@section('breadcrumbs')
    <span>FAQ</span>
@endsection

@section('content')
    <div class="pageHeader">
        <div>
            <h1>よくある質問</h1>
            <p>問い合わせ前に、まずはこちらをご確認ください。</p>
        </div>
    </div>

    @if (session('error'))
        <div class="errorMessage">
            {{ session('error') }}
        </div>
    @endif

    @if (session('success'))
        <div class="successMessage">
            {{ session('success') }}
        </div>
    @endif

    <section class="card">
        <form method="GET" action="{{ route('faqs.index') }}" class="searchBox">
            <div class="searchItem searchKeyword">
                <label for="keyword">キーワード</label>
                <input
                    type="text"
                    id="keyword"
                    name="keyword"
                    value="{{ $keyword }}"
                    placeholder="FAQを検索"
                >
            </div>

            <div class="searchItem">
                <label for="category">カテゴリ</label>
                <select id="category" name="category">
                    <option value="">すべて</option>
                    @foreach ($categories as $categoryItem)
                        <option value="{{ $categoryItem }}" @selected($category === $categoryItem)>
                            {{ $categoryItem }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="searchActions">
                <button type="submit" class="button">検索</button>
                <a href="{{ route('faqs.index') }}" class="button subButton">リセット</a>
            </div>
        </form>
    </section>

    <section class="card">
        <h2>FAQ一覧</h2>

        @if ($faqs->isEmpty())
            <p class="emptyText">
                現在公開されているFAQはありません。
            </p>
        @else
            <div class="faqList">
                @foreach ($faqs as $faq)
                    <article class="faqItem">
                        <div class="faqMeta">
                            <span>{{ $faq->category ?? '未分類' }}</span>
                        </div>

                        <h3>
                            <a href="{{ route('faqs.show', $faq) }}">
                                {{ $faq->title }}
                            </a>
                        </h3>

                        <p>
                            {{ \Illuminate\Support\Str::limit($faq->body, 120) }}
                        </p>
                    </article>
                @endforeach
            </div>

            {{ $faqs->links() }}
        @endif
    </section>

    <section class="card">
        <h2>問い合わせへ進む</h2>
        <p>
            FAQを確認しても解決しない場合は、以下にチェックを入れて問い合わせフォームへ進んでください。
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
