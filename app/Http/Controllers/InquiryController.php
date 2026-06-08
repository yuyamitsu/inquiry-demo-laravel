<?php

namespace App\Http\Controllers;

use App\Models\Inquiry;
use App\Models\InquiryLog;
use App\Models\InquiryComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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

        $inquiry = Inquiry::create([
            'user_id' => Auth::id(),
            'name' => $validated['name'],
            'email' => $validated['email'],
            'title' => $validated['title'],
            'category' => $validated['category'],
            'body' => $validated['body'],
            'status' => '未対応',
            'admin_reply' => null,
        ]);

        if (Auth::user()->role === 'admin') {
            return redirect()
                ->route('admin.inquiries.index')
                ->with('success', 'お問い合わせを登録しました。');
        }

        return redirect()
            ->route('my.inquiries.show', $inquiry)
            ->with('success', 'お問い合わせを登録しました。');
    }
    public function index(Request $request)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }
        
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

        $inquiries = $query
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        $totalCount = Inquiry::count();
        $newCount = Inquiry::where('status', '未対応')->count();
        $progressCount = Inquiry::where('status', '対応中')->count();
        $answeredCount = Inquiry::where('status', '回答済み')->count();
        $closedCount = Inquiry::where('status', 'クローズ')->count();

        return view('admin.inquiries.index', compact(
            'inquiries',
            'keyword',
            'status',
            'category',
            'totalCount',
            'newCount',
            'progressCount',
            'answeredCount',
            'closedCount'
        ));
    }

    public function show(Inquiry $inquiry)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        $logs = $inquiry->logs()
            ->with('user')
            ->oldest()
            ->get();

        $comments = $inquiry->comments()
            ->with('user')
            ->oldest()
            ->get();

        return view('admin.inquiries.show', compact('inquiry', 'logs', 'comments'));
    }

    public function myIndex()
    {
        $inquiries = Inquiry::where('user_id', Auth::id())
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(10);

        return view('my.inquiries.index', compact('inquiries'));
    }

    public function myShow(Inquiry $inquiry)
    {
        if ($inquiry->user_id !== Auth::id()) {
            abort(403);
        }

        $comments = $inquiry->comments()
            ->with('user')
            ->oldest()
            ->get();

        return view('my.inquiries.show', compact('inquiry', 'comments'));
    }

    public function update(Request $request, Inquiry $inquiry)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

        $validated = $request->validate([
            'status' => ['required', 'in:未対応,対応中,回答済み,クローズ'],
        ]);

        $beforeStatus = $inquiry->status;

        $inquiry->update([
            'status' => $validated['status'],
        ]);

        if ($beforeStatus !== $inquiry->status) {
            InquiryLog::create([
                'inquiry_id' => $inquiry->id,
                'user_id' => Auth::id(),
                'action' => 'updated',
                'field_name' => 'status',
                'before_value' => $beforeStatus,
                'after_value' => $inquiry->status,
                'message' => "ステータスを「{$beforeStatus}」から「{$inquiry->status}」に変更しました。",
            ]);
        }

        return redirect()
            ->route('admin.inquiries.show', $inquiry)
            ->with('success', '問い合わせ情報を保存しました。');
    }

    public function destroy(Inquiry $inquiry)
    {
        Log::warning('問い合わせが削除されました。', [
            'inquiry_id' => $inquiry->id,
            'title' => $inquiry->title,
            'status' => $inquiry->status,
        ]);

        $inquiry->delete();

        return redirect()
            ->route('admin.inquiries.index')
            ->with('success', '問い合わせを削除しました。');
    }

    public function storeComment(Request $request, Inquiry $inquiry)
    {
        $validated = $request->validate([
            'body' => ['required', 'string', 'max:2000'],
        ]);

        if (Auth::user()->role !== 'admin' && $inquiry->user_id !== Auth::id()) {
            abort(403);
        }

        InquiryComment::create([
            'inquiry_id' => $inquiry->id,
            'user_id' => Auth::id(),
            'body' => $validated['body'],
        ]);

        return back()
            ->with('success', 'コメントを投稿しました。');
    }

}
