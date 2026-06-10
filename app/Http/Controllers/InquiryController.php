<?php

namespace App\Http\Controllers;

use App\Models\Inquiry;
use App\Models\InquiryLog;
use App\Models\InquiryComment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

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
        ]);

        if (in_array(Auth::user()->role, ['admin', 'staff'], true)) {
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
        if (! in_array(Auth::user()->role, ['admin', 'staff'], true)) {
            abort(403);
        }

        $keyword = $request->input('keyword');
        $status = $request->input('status');
        $category = $request->input('category');
        $assigneeId = $request->input('assignee_id');
        $priority = $request->input('priority');
        $dueStatus = $request->input('due_status');

        $query = Inquiry::with('assignee');

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

        if ($assigneeId === 'unassigned') {
            $query->whereNull('assignee_id');
        } elseif ($assigneeId === 'me') {
            $query->where('assignee_id', Auth::id());
        } elseif ($assigneeId) {
            $query->where('assignee_id', $assigneeId);
        }

        if ($priority) {
            $query->where('priority', $priority);
        }

        if ($dueStatus === 'overdue') {
            $query->whereNotNull('due_date')
                ->whereDate('due_date', '<', now()->toDateString())
                ->where('status', '!=', 'クローズ');
        }

        if ($dueStatus === 'today') {
            $query->whereDate('due_date', now()->toDateString());
        }

        if ($dueStatus === 'unset') {
            $query->whereNull('due_date');
        }

        $inquiries = $query
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        $assignees = User::whereIn('role', ['admin', 'staff'])
            ->orderBy('name')
            ->get();

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
            'assigneeId',
            'priority',
            'dueStatus',
            'assignees',
            'totalCount',
            'newCount',
            'progressCount',
            'answeredCount',
            'closedCount'
        ));
    }

    public function show(Inquiry $inquiry)
    {
        if (! in_array(Auth::user()->role, ['admin', 'staff'], true)) {
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

        $assignees = User::whereIn('role', ['admin', 'staff'])
            ->orderBy('name')
            ->get();

        return view('admin.inquiries.show', compact(
            'inquiry',
            'logs',
            'comments',
            'assignees'
        ));
    }

    public function myIndex()
    {
        $inquiries = Inquiry::with('assignee')
            ->where('user_id', Auth::id())
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
        if (! in_array(Auth::user()->role, ['admin', 'staff'], true)) {
            abort(403);
        }

        $validated = $request->validate([
            'status' => ['required', 'in:未対応,対応中,回答済み,クローズ'],
            'assignee_id' => [
                'nullable',
                Rule::exists('users', 'id')->where(function ($query) {
                    $query->whereIn('role', ['admin', 'staff']);
                }),
            ],
            'priority' => ['nullable', 'in:低,中,高,緊急'],
            'due_date' => ['nullable', 'date'],
        ]);

        $beforeStatus = $inquiry->status;
        $beforeAssigneeId = $inquiry->assignee_id;
        $beforePriority = $inquiry->priority;
        $beforeDueDate = $inquiry->due_date;

        $inquiry->update($validated);

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

        if ((string) $beforeAssigneeId !== (string) $inquiry->assignee_id) {
            $beforeAssigneeName = $beforeAssigneeId
                ? User::find($beforeAssigneeId)?->name
                : '未設定';

            $afterAssigneeName = $inquiry->assignee?->name ?? '未設定';

            InquiryLog::create([
                'inquiry_id' => $inquiry->id,
                'user_id' => Auth::id(),
                'action' => 'updated',
                'field_name' => 'assignee_id',
                'before_value' => $beforeAssigneeName,
                'after_value' => $afterAssigneeName,
                'message' => "担当者を「{$beforeAssigneeName}」から「{$afterAssigneeName}」に変更しました。",
            ]);
        }

        if ($beforePriority !== $inquiry->priority) {
            InquiryLog::create([
                'inquiry_id' => $inquiry->id,
                'user_id' => Auth::id(),
                'action' => 'updated',
                'field_name' => 'priority',
                'before_value' => $beforePriority ?? '未設定',
                'after_value' => $inquiry->priority ?? '未設定',
                'message' => "優先度を「" . ($beforePriority ?? '未設定') . "」から「" . ($inquiry->priority ?? '未設定') . "」に変更しました。",
            ]);
        }

        if ((string) $beforeDueDate !== (string) $inquiry->due_date) {
            InquiryLog::create([
                'inquiry_id' => $inquiry->id,
                'user_id' => Auth::id(),
                'action' => 'updated',
                'field_name' => 'due_date',
                'before_value' => $beforeDueDate ?? '未設定',
                'after_value' => $inquiry->due_date ?? '未設定',
                'message' => "対応期限を「" . ($beforeDueDate ?? '未設定') . "」から「" . ($inquiry->due_date ?? '未設定') . "」に変更しました。",
            ]);
        }

        return redirect()
            ->route('admin.inquiries.show', $inquiry)
            ->with('success', '問い合わせ情報を保存しました。');
    }

    public function destroy(Inquiry $inquiry)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403);
        }

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

        if (
            ! in_array(Auth::user()->role, ['admin', 'staff'], true)
            && $inquiry->user_id !== Auth::id()
        ) {
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
