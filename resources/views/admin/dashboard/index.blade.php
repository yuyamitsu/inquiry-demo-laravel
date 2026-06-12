@extends('layouts.app')

@section('title', 'ダッシュボード')

@section('breadcrumbs')
    <span>ダッシュボード</span>
@endsection

@section('content')
    <div class="pageHeader">
        <div>
            <h1>ダッシュボード</h1>
            <p>問い合わせの対応状況を確認できます。</p>
        </div>

        <div class="pageActions">
            <a href="{{ route('admin.inquiries.index') }}" class="button">
                問い合わせ一覧
            </a>

            <a href="{{ route('admin.knowledge.index') }}" class="button subButton">
                ナレッジ一覧
            </a>
        </div>
    </div>

    <div class="summaryArea">
        <a href="{{ route('admin.inquiries.index') }}" class="summaryCard summaryAll">
            <span class="summaryLabel">全件</span>
            <strong>{{ $totalCount }}</strong>
            <span class="summaryNote">登録済み</span>
        </a>

        <a href="{{ route('admin.inquiries.index', ['status' => '未対応']) }}" class="summaryCard summaryNew">
            <span class="summaryLabel">未対応</span>
            <strong>{{ $newCount }}</strong>
            <span class="summaryNote">対応が必要</span>
        </a>

        <a href="{{ route('admin.inquiries.index', ['status' => '対応中']) }}" class="summaryCard summaryProgress">
            <span class="summaryLabel">対応中</span>
            <strong>{{ $progressCount }}</strong>
            <span class="summaryNote">処理中</span>
        </a>

        <a href="{{ route('admin.inquiries.index', ['status' => '回答済み']) }}" class="summaryCard summaryAnswered">
            <span class="summaryLabel">回答済み</span>
            <strong>{{ $answeredCount }}</strong>
            <span class="summaryNote">回答完了</span>
        </a>

        <a href="{{ route('admin.inquiries.index', ['status' => 'クローズ']) }}" class="summaryCard summaryClosed">
            <span class="summaryLabel">クローズ</span>
            <strong>{{ $closedCount }}</strong>
            <span class="summaryNote">完了済み</span>
        </a>

        <a href="{{ route('admin.inquiries.index') }}" class="summaryCard summaryOverdue">
            <span class="summaryLabel">期限切れ</span>
            <strong>{{ $overdueCount }}</strong>
            <span class="summaryNote">要確認</span>
        </a>

        <a href="{{ route('admin.inquiries.index') }}" class="summaryCard summaryToday">
            <span class="summaryLabel">本日期限</span>
            <strong>{{ $dueTodayCount }}</strong>
            <span class="summaryNote">本日対応</span>
        </a>

        <a href="{{ route('admin.inquiries.index') }}" class="summaryCard summaryUrgent">
            <span class="summaryLabel">緊急</span>
            <strong>{{ $urgentCount }}</strong>
            <span class="summaryNote">優先対応</span>
        </a>

        <a href="{{ route('admin.inquiries.index') }}" class="summaryCard summaryUnassigned">
            <span class="summaryLabel">担当者未設定</span>
            <strong>{{ $unassignedCount }}</strong>
            <span class="summaryNote">割り当て待ち</span>
        </a>

        <a href="{{ route('admin.inquiries.index') }}" class="summaryCard summaryMine">
            <span class="summaryLabel">自分の担当</span>
            <strong>{{ $myAssignedCount }}</strong>
            <span class="summaryNote">未クローズ</span>
        </a>
    </div>

    <div class="dashboardGrid">
        <section class="card">
            <div class="sectionHeader">
                <h2>最近の問い合わせ</h2>
                <p>新しく登録された問い合わせです。</p>
            </div>

            @forelse ($recentInquiries as $inquiry)
                <div class="dashboardListItem">
                    <a href="{{ route('admin.inquiries.show', $inquiry) }}">
                        #{{ $inquiry->id }} {{ $inquiry->title }}
                    </a>
                    <div class="dashboardMeta">
                        <span>{{ $inquiry->status }}</span>
                        <span>投稿者：{{ $inquiry->user?->name ?? $inquiry->name }}</span>
                        <span>{{ $inquiry->created_at->timezone('Asia/Tokyo')->format('Y/m/d H:i') }}</span>
                    </div>
                </div>
            @empty
                <p class="emptyText">問い合わせはまだありません。</p>
            @endforelse
        </section>

        <section class="card">
            <div class="sectionHeader">
                <h2>自分の担当問い合わせ</h2>
                <p>自分に割り当てられている未クローズの問い合わせです。</p>
            </div>

            @forelse ($myAssignedInquiries as $inquiry)
                <div class="dashboardListItem">
                    <a href="{{ route('admin.inquiries.show', $inquiry) }}">
                        #{{ $inquiry->id }} {{ $inquiry->title }}
                    </a>
                    <div class="dashboardMeta">
                        <span>{{ $inquiry->status }}</span>
                        <span>期限：{{ $inquiry->due_date ?? '未設定' }}</span>
                        <span>優先度：{{ $inquiry->priority ?? '未設定' }}</span>
                    </div>
                </div>
            @empty
                <p class="emptyText">自分の担当問い合わせはありません。</p>
            @endforelse
        </section>

        <section class="card">
            <div class="sectionHeader">
                <h2>期限切れ問い合わせ</h2>
                <p>対応期限を過ぎている未クローズの問い合わせです。</p>
            </div>

            @forelse ($overdueInquiries as $inquiry)
                <div class="dashboardListItem dashboardListWarning">
                    <a href="{{ route('admin.inquiries.show', $inquiry) }}">
                        #{{ $inquiry->id }} {{ $inquiry->title }}
                    </a>
                    <div class="dashboardMeta">
                        <span>期限：{{ $inquiry->due_date }}</span>
                        <span>担当者：{{ $inquiry->assignee?->name ?? '未設定' }}</span>
                        <span>{{ $inquiry->status }}</span>
                    </div>
                </div>
            @empty
                <p class="emptyText">期限切れの問い合わせはありません。</p>
            @endforelse
        </section>

        <section class="card">
            <div class="sectionHeader">
                <h2>最近のナレッジ</h2>
                <p>最近作成されたナレッジです。</p>
            </div>

            @forelse ($recentKnowledgeArticles as $knowledgeArticle)
                <div class="dashboardListItem">
                    <a href="{{ route('admin.knowledge.show', $knowledgeArticle) }}">
                        {{ $knowledgeArticle->title }}
                    </a>
                    <div class="dashboardMeta">
                        <span>{{ $knowledgeArticle->category ?? '未設定' }}</span>
                        <span>作成者：{{ $knowledgeArticle->creator?->name ?? '不明' }}</span>
                        @if ($knowledgeArticle->inquiry)
                            <span>問い合わせ #{{ $knowledgeArticle->inquiry->id }}</span>
                        @else
                            <span>自由作成</span>
                        @endif
                    </div>
                </div>
            @empty
                <p class="emptyText">ナレッジはまだありません。</p>
            @endforelse
        </section>
    </div>
@endsection
