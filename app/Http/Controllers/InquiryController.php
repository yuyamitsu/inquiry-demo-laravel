<?php

namespace App\Http\Controllers;

use App\Models\Inquiry;
use Illuminate\Http\Request;

class InquiryController extends Controller
{
    public function create()
    {
        return view('inquiries.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:50'],
            'email' => ['required', 'email', 'max:255'],
            'title' => ['required', 'string', 'max:100'],
            'category' => ['required', 'string', 'max:20'],
            'body' => ['required', 'string', 'max:1000'],
        ]);

        Inquiry::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'title' => $validated['title'],
            'category' => $validated['category'],
            'body' => $validated['body'],
            'status' => '未対応',
            'admin_reply' => null,
        ]);

        return redirect()
            ->route('admin.inquiries.index')
            ->with('success', 'お問い合わせを登録しました。');
    }

    public function index(Request $request)
    {
        $keyword = $request->input('keyword');
        $status = $request->input('status');
        $category = $request->input('category');

        $query = Inquiry::query();

        if ($keyword) {
            $query->where(function ($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                    ->orWhere('name', 'like', "%{$keyword}%")
                    ->orWhere('email', 'like', "%{$keyword}%")
                    ->orWhere('body', 'like', "%{$keyword}%");
            });
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($category) {
            $query->where('category', $category);
        }

        $inquiries = $query->latest()->paginate(10)->withQueryString();

        $totalCount = Inquiry::count();
        $newCount = Inquiry::where('status', '未対応')->count();
        $progressCount = Inquiry::where('status', '対応中')->count();
        $answeredCount = Inquiry::where('status', '回答済み')->count();

        return view('admin.inquiries.index', compact(
            'inquiries',
            'keyword',
            'status',
            'category',
            'totalCount',
            'newCount',
            'progressCount',
            'answeredCount'
        ));
    }

    public function show(Inquiry $inquiry)
    {
        return view('admin.inquiries.show', compact('inquiry'));
    }

    public function update(Request $request, Inquiry $inquiry)
    {
        $validated = $request->validate([
            'status' => ['required', 'string', 'max:20'],
            'admin_reply' => ['nullable', 'string', 'max:1000'],
        ]);

        $inquiry->update($validated);

        return redirect()
            ->route('admin.inquiries.index')
            ->with('success', '問い合わせ情報を保存しました。');
    }

    public function destroy(Inquiry $inquiry)
    {
        $inquiry->delete();

        return redirect()
            ->route('admin.inquiries.index')
            ->with('success', '問い合わせを削除しました。');
    }
}
