@extends('layouts.app')

@section('title', 'FAQ管理')

@section('breadcrumbs')
    <span>FAQ管理</span>
@endsection

@section('content')
    <div class="pageHeader">
        <div>
            <h1>FAQ管理</h1>
            <p>ユーザー向けに公開するFAQを管理します。</p>
        </div>

        <div class="pageActions">
            <a href="{{ route('admin.faqs.create') }}" class="button">
                FAQを登録する
            </a>
        </div>
    </div>

    <section class="card">
        <form method="GET" action="{{ route('admin.faqs.index') }}" class="searchBox">
            <div class="searchItem searchKeyword">
                <label for="keyword">キーワード</label>
                <input
                    type="text"
                    id="keyword"
                    name="keyword"
                    value="{{ $keyword }}"
                    placeholder="タイトル・本文で検索"
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

            <div class="searchItem">
                <label for="published">公開状態</label>
                <select id="published" name="published">
                    <option value="">すべて</option>
                    <option value="1" @selected($published === '1')>公開中</option>
                    <option value="0" @selected($published === '0')>非公開</option>
                </select>
            </div>

            <div class="searchActions">
                <button type="submit" class="button">検索</button>
                <a href="{{ route('admin.faqs.index') }}" class="button subButton">リセット</a>
            </div>
        </form>
    </section>

    <section class="card">
        <h2>FAQ一覧</h2>

        @if ($faqs->isEmpty())
            <p class="emptyText">FAQはまだ登録されていません。</p>
        @else
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>タイトル</th>
                        <th>カテゴリ</th>
                        <th>公開状態</th>
                        <th>表示順</th>
                        <th>登録日時</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($faqs as $faq)
                        <tr>
                            <td>{{ $faq->id }}</td>
                            <td>
                                <a href="{{ route('admin.faqs.edit', $faq) }}">
                                    {{ $faq->title }}
                                </a>
                            </td>
                            <td>{{ $faq->category ?? '未設定' }}</td>
                            <td>
                                @if ($faq->is_published)
                                    <span class="status statusAnswered">公開中</span>
                                @else
                                    <span class="status statusClosed">非公開</span>
                                @endif
                            </td>
                            <td>{{ $faq->sort_order }}</td>
                            <td>{{ $faq->created_at->timezone('Asia/Tokyo')->format('Y/m/d H:i') }}</td>
                            <td>
                                <div class="tableActions">
                                    <a href="{{ route('admin.faqs.edit', $faq) }}" class="button smallButton">
                                        編集
                                    </a>

                                    <form
                                        method="POST"
                                        action="{{ route('admin.faqs.destroy', $faq) }}"
                                        onsubmit="return confirm('このFAQを削除しますか？');"
                                    >
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit" class="button deleteButton smallButton">
                                            削除
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{ $faqs->links() }}
        @endif
    </section>
@endsection
