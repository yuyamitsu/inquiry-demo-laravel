<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inquiry;
use App\Models\KnowledgeArticle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KnowledgeArticleController extends Controller
{
    public function index(Request $request)
    {
        $keyword = $request->input('keyword');
        $category = $request->input('category');
        $published = $request->input('published');

        $query = KnowledgeArticle::with(['inquiry', 'creator']);

        if ($keyword) {
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                    ->orWhere('body', 'like', "%{$keyword}%")
                    ->orWhere('category', 'like', "%{$keyword}%");
            });
        }

        if ($category) {
            $query->where('category', $category);
        }

        if ($published !== null && $published !== '') {
            $query->where('is_published', $published);
        }

        $knowledgeArticles = $query
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        $categories = KnowledgeArticle::query()
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        return view('admin.knowledge.index', compact(
            'knowledgeArticles',
            'keyword',
            'category',
            'published',
            'categories'
        ));
    }

    public function create()
    {
        return view('admin.knowledge.create', [
            'inquiry' => null,
            'title' => '',
            'category' => '',
            'body' => '',
        ]);
    }

    public function createFromInquiry(Inquiry $inquiry)
    {
        $inquiry->load(['comments.user', 'user', 'assignee']);

        if ($inquiry->knowledgeArticle) {
            return redirect()
                ->route('admin.knowledge.show', $inquiry->knowledgeArticle)
                ->with('success', 'この問い合わせはすでにナレッジ化されています。');
        }

        $body = "【問い合わせ内容】\n";
        $body .= $inquiry->body . "\n\n";

        $body .= "【対応内容】\n";
        foreach ($inquiry->comments as $comment) {
            $roleLabel = match ($comment->user?->role) {
                'admin' => '管理者',
                'staff' => '担当者',
                'user' => '利用者',
                default => '不明',
            };

            $body .= "・{$comment->user?->name}（{$roleLabel}）：{$comment->body}\n";
        }

        $body .= "\n【解決方法】\n";
        $body .= "※ 最終的な解決方法を記入\n\n";

        $body .= "【補足】\n";
        $body .= "※ 再発時の注意点や関連情報を記入\n";

        return view('admin.knowledge.create', [
            'inquiry' => $inquiry,
            'title' => $inquiry->title,
            'category' => $inquiry->category,
            'body' => $body,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'inquiry_id' => ['nullable', 'exists:inquiries,id'],
            'title' => ['required', 'string', 'max:100'],
            'category' => ['nullable', 'string', 'max:50'],
            'body' => ['required', 'string', 'max:5000'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        $knowledgeArticle = KnowledgeArticle::create([
            'inquiry_id' => $validated['inquiry_id'] ?? null,
            'created_by' => Auth::id(),
            'title' => $validated['title'],
            'category' => $validated['category'] ?? null,
            'body' => $validated['body'],
            'is_published' => $request->boolean('is_published'),
        ]);

        return redirect()
            ->route('admin.knowledge.show', $knowledgeArticle)
            ->with('success', 'ナレッジ記事を作成しました。');
    }

    public function show(KnowledgeArticle $knowledgeArticle)
    {
        $knowledgeArticle->load(['inquiry', 'creator']);

        return view('admin.knowledge.show', compact('knowledgeArticle'));
    }

    public function edit(KnowledgeArticle $knowledgeArticle)
    {
        $knowledgeArticle->load(['inquiry', 'creator']);

        return view('admin.knowledge.edit', compact('knowledgeArticle'));
    }

    public function update(Request $request, KnowledgeArticle $knowledgeArticle)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:100'],
            'category' => ['nullable', 'string', 'max:50'],
            'body' => ['required', 'string', 'max:5000'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        $knowledgeArticle->update([
            'title' => $validated['title'],
            'category' => $validated['category'] ?? null,
            'body' => $validated['body'],
            'is_published' => $request->boolean('is_published'),
        ]);

        return redirect()
            ->route('admin.knowledge.show', $knowledgeArticle)
            ->with('success', 'ナレッジ記事を更新しました。');
    }

}
