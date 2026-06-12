@extends('layouts.app')

@section('title', 'ナレッジ一覧')

@section('breadcrumbs')
    <a href="{{ route('admin.inquiries.index') }}">問い合わせ一覧</a>
    <span class="breadcrumbSeparator">＞</span>
    <span>ナレッジ一覧</span>
@endsection

@section('content')
    <div class="pageHeader">
        <div>
            <h1>ナレッジ一覧</h1>
            <p>問い合わせ対応から作成したナレッジや、自由作成したナレッジを検索・確認できます。</p>
        </div>

        <div class="pageActions">
            <a href="{{ route('admin.knowledge.create') }}" class="button">
                ナレッジ新規作成
            </a>

            <a href="{{ route('admin.inquiries.index') }}" class="button subButton">
                問い合わせ一覧へ
            </a>
        </div>
    </div>

    <form method="GET" action="{{ route('admin.knowledge.index') }}" class="searchBox">
        <div class="searchItem searchKeyword">
            <label for="keyword">キーワード</label>
            <input
                type="text"
                id="keyword"
                name="keyword"
                value="{{ $keyword }}"
                placeholder="タイトル・本文・カテゴリで検索"
            >
        </div>

        <div class="searchItem">
            <label for="category">カテゴリ</label>
            <select id="category" name="category">
                <option value="">すべて</option>
                @foreach ($categories as $categoryOption)
                    <option value="{{ $categoryOption }}" @selected($category === $categoryOption)>
                        {{ $categoryOption }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="searchItem">
            <label for="published">公開状態</label>
            <select id="published" name="published">
                <option value="">すべて</option>
                <option value="1" @selected($published === '1')>公開</option>
                <option value="0" @selected($published === '0')>下書き</option>
            </select>
        </div>

        <div class="searchActions">
            <button type="submit" class="button">検索</button>

            <a href="{{ route('admin.knowledge.index') }}" class="button subButton">
                リセット
            </a>
        </div>
    </form>

    <p class="resultText">
        検索結果：{{ $knowledgeArticles->total() }}件
    </p>

    <div class="tableWrapper">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>タイトル</th>
                    <th>カテゴリ</th>
                    <th>公開状態</th>
                    <th>作成者</th>
                    <th>元問い合わせ</th>
                    <th>作成日時</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($knowledgeArticles as $knowledgeArticle)
                    <tr>
                        <td>{{ $knowledgeArticle->id }}</td>

                        <td>
                            <a href="{{ route('admin.knowledge.show', $knowledgeArticle) }}" class="tableLink">
                                {{ $knowledgeArticle->title }}
                            </a>
                        </td>

                        <td>{{ $knowledgeArticle->category ?? '未設定' }}</td>

                        <td>
                            @if ($knowledgeArticle->is_published)
                                <span class="statusBadge statusAnswered">公開</span>
                            @else
                                <span class="statusBadge statusClosed">下書き</span>
                            @endif
                        </td>

                        <td>{{ $knowledgeArticle->creator?->name ?? '不明' }}</td>

                        <td>
                            @if ($knowledgeArticle->inquiry)
                                <a href="{{ route('admin.inquiries.show', $knowledgeArticle->inquiry) }}" class="tableLink">
                                    #{{ $knowledgeArticle->inquiry->id }}
                                </a>
                            @else
                                -
                            @endif
                        </td>

                        <td>{{ $knowledgeArticle->created_at->timezone('Asia/Tokyo')->format('Y/m/d H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">
                            ナレッジ記事はまだありません。
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="paginationArea">
        {{ $knowledgeArticles->links() }}
    </div>
@endsection
