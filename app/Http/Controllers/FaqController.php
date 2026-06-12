<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    public function index(Request $request)
    {
        $keyword = $request->input('keyword');
        $category = $request->input('category');

        $query = Faq::where('is_published', true);

        if ($keyword) {
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                    ->orWhere('body', 'like', "%{$keyword}%");
            });
        }

        if ($category) {
            $query->where('category', $category);
        }

        $faqs = $query
            ->orderBy('sort_order')
            ->orderByDesc('created_at')
            ->paginate(10)
            ->withQueryString();

        $categories = Faq::where('is_published', true)
            ->whereNotNull('category')
            ->select('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');

        return view('faqs.index', compact(
            'faqs',
            'keyword',
            'category',
            'categories'
        ));
    }

    public function show(Faq $faq)
    {
        if (! $faq->is_published) {
            abort(404);
        }

        return view('faqs.show', compact('faq'));
    }

    public function confirm()
    {
        session(['faq_checked' => true]);

        return redirect()
            ->route('inquiries.create')
            ->with('success', 'FAQを確認しました。問い合わせ内容を入力してください。');
    }
}
