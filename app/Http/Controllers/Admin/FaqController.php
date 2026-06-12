<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    private function ensureAdmin(Request $request): void
    {
        if ($request->user()->role !== 'admin') {
            abort(403);
        }
    }

    public function index(Request $request)
    {
        $this->ensureAdmin($request);

        $keyword = $request->input('keyword');
        $category = $request->input('category');
        $published = $request->input('published');

        $query = Faq::query();

        if ($keyword) {
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                    ->orWhere('body', 'like', "%{$keyword}%");
            });
        }

        if ($category) {
            $query->where('category', $category);
        }

        if ($published === '1') {
            $query->where('is_published', true);
        } elseif ($published === '0') {
            $query->where('is_published', false);
        }

        $faqs = $query
            ->orderBy('sort_order')
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        $categories = Faq::whereNotNull('category')
            ->select('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        return view('admin.faqs.index', compact(
            'faqs',
            'keyword',
            'category',
            'published',
            'categories'
        ));
    }

    public function create(Request $request)
    {
        $this->ensureAdmin($request);

        return view('admin.faqs.create');
    }

    public function store(Request $request)
    {
        $this->ensureAdmin($request);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:100'],
            'category' => ['nullable', 'string', 'max:50'],
            'body' => ['required', 'string', 'max:3000'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        $validated['is_published'] = $request->boolean('is_published');

        Faq::create($validated);

        return redirect()
            ->route('admin.faqs.index')
            ->with('success', 'FAQを登録しました。');
    }

    public function edit(Request $request, Faq $faq)
    {
        $this->ensureAdmin($request);

        return view('admin.faqs.edit', compact('faq'));
    }

    public function update(Request $request, Faq $faq)
    {
        $this->ensureAdmin($request);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:100'],
            'category' => ['nullable', 'string', 'max:50'],
            'body' => ['required', 'string', 'max:3000'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        $validated['is_published'] = $request->boolean('is_published');

        $faq->update($validated);

        return redirect()
            ->route('admin.faqs.index')
            ->with('success', 'FAQを更新しました。');
    }

    public function destroy(Request $request, Faq $faq)
    {
        $this->ensureAdmin($request);

        $faq->delete();

        return redirect()
            ->route('admin.faqs.index')
            ->with('success', 'FAQを削除しました。');
    }
}
