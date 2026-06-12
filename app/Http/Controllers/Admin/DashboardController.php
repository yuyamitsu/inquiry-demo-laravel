<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inquiry;
use App\Models\KnowledgeArticle;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $totalCount = Inquiry::count();

        $newCount = Inquiry::where('status', '未対応')->count();
        $progressCount = Inquiry::where('status', '対応中')->count();
        $answeredCount = Inquiry::where('status', '回答済み')->count();
        $closedCount = Inquiry::where('status', 'クローズ')->count();

        $unassignedCount = Inquiry::whereNull('assignee_id')->count();

        $urgentCount = Inquiry::where('priority', '緊急')
            ->where('status', '!=', 'クローズ')
            ->count();

        $today = now()->toDateString();

        $dueTodayCount = Inquiry::whereDate('due_date', $today)
            ->where('status', '!=', 'クローズ')
            ->count();

        $overdueCount = Inquiry::whereNotNull('due_date')
            ->whereDate('due_date', '<', $today)
            ->where('status', '!=', 'クローズ')
            ->count();

        $myAssignedCount = Inquiry::where('assignee_id', $user->id)
            ->where('status', '!=', 'クローズ')
            ->count();

        $recentInquiries = Inquiry::with(['user', 'assignee'])
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->take(5)
            ->get();

        $myAssignedInquiries = Inquiry::with(['user', 'assignee'])
            ->where('assignee_id', $user->id)
            ->where('status', '!=', 'クローズ')
            ->orderByRaw("CASE WHEN due_date IS NULL THEN 1 ELSE 0 END")
            ->orderBy('due_date')
            ->orderByDesc('created_at')
            ->take(5)
            ->get();

        $overdueInquiries = Inquiry::with(['user', 'assignee'])
            ->whereNotNull('due_date')
            ->whereDate('due_date', '<', $today)
            ->where('status', '!=', 'クローズ')
            ->orderBy('due_date')
            ->take(5)
            ->get();

        $recentKnowledgeArticles = KnowledgeArticle::with(['creator', 'inquiry'])
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->take(5)
            ->get();

        return view('admin.dashboard.index', compact(
            'totalCount',
            'newCount',
            'progressCount',
            'answeredCount',
            'closedCount',
            'unassignedCount',
            'urgentCount',
            'dueTodayCount',
            'overdueCount',
            'myAssignedCount',
            'recentInquiries',
            'myAssignedInquiries',
            'overdueInquiries',
            'recentKnowledgeArticles'
        ));
    }
}
