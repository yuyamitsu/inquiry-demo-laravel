@extends('layouts.app')

@section('title', '問い合わせ詳細')

@section('content')
    <h1>問い合わせ詳細</h1>

    <section class="card">
        <h2>問い合わせ内容</h2>

        <dl class="detailList">
            <dt>ID</dt>
            <dd>{{ $inquiry->id }}</dd>

            <dt>お名前</dt>
            <dd>{{ $inquiry->name }}</dd>

            <dt>メールアドレス</dt>
            <dd>{{ $inquiry->email }}</dd>

            <dt>件名</dt>
            <dd>{{ $inquiry->title }}</dd>

            <dt>カテゴリ</dt>
            <dd>{{ $inquiry->category }}</dd>

            <dt>問い合わせ内容</dt>
            <dd>{{ $inquiry->body }}</dd>

            <dt>受付日時</dt>
            <dd>{{ $inquiry->created_at->format('Y/m/d H:i') }}</dd>
        </dl>
    </section>

    <section class="card">
        <h2>管理者対応</h2>

        <form method="POST" action="{{ route('admin.inquiries.update', $inquiry) }}">
            @csrf
            @method('PUT')

            <div class="formGroup">
                <label for="status">ステータス</label>
                <select id="status" name="status">
                    <option value="未対応" @selected(old('status', $inquiry->status) === '未対応')>未対応</option>
                    <option value="対応中" @selected(old('status', $inquiry->status) === '対応中')>対応中</option>
                    <option value="回答済み" @selected(old('status', $inquiry->status) === '回答済み')>回答済み</option>
                    <option value="クローズ" @selected(old('status', $inquiry->status) === 'クローズ')>クローズ</option>
                </select>
                @error('status')
                    <p class="errorText">{{ $message }}</p>
                @enderror
            </div>

            <div class="formGroup">
                <label for="admin_reply">返答内容</label>
                <textarea
                    id="admin_reply"
                    name="admin_reply"
                    rows="6"
                    placeholder="返答内容を入力してください"
                >{{ old('admin_reply', $inquiry->admin_reply) }}</textarea>
                @error('admin_reply')
                    <p class="errorText">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="button">
                保存する
            </button>

            <a href="{{ route('admin.inquiries.index') }}" class="button subButton">
                一覧に戻る
            </a>
        </form>
        <div class="historyArea">
            <h2>変更履歴</h2>

            @forelse ($logs as $log)
                <div class="historyItem">
                    <div class="historyMeta">
                        <span>{{ $log->created_at->format('Y/m/d H:i') }}</span>
                        <span>更新者：{{ $log->user?->name ?? '不明' }}</span>
                    </div>

                    <p class="historyMessage">
                        {{ $log->message }}
                    </p>

                    @if ($log->field_name && ($log->before_value || $log->after_value))
                        <div class="historyDetail">
                            <span class="historyField">{{ $log->field_name }}</span>

                            @if ($log->before_value !== null)
                                <span class="historyBefore">変更前：{{ $log->before_value }}</span>
                            @endif

                            @if ($log->after_value !== null)
                                <span class="historyAfter">変更後：{{ $log->after_value }}</span>
                            @endif
                        </div>
                    @endif
                </div>
            @empty
                <p class="emptyText">変更履歴はありません。</p>
            @endforelse
        </div>
    </section>
@endsection
