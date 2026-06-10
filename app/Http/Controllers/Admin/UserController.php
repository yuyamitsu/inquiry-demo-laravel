<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        if ($request->user()->role !== 'admin') {
            abort(403);
        }

        $keyword = $request->input('keyword');
        $role = $request->input('role');

        $query = User::query()
            ->withCount([
                'inquiries',
                'assignedInquiries',
            ]);

        if ($keyword) {
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%{$keyword}%")
                    ->orWhere('email', 'like', "%{$keyword}%");
            });
        }

        if ($role) {
            $query->where('role', $role);
        }

        $users = $query
            ->orderBy('role')
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('admin.users.index', compact(
            'users',
            'keyword',
            'role'
        ));
    }

    public function show(Request $request, User $user)
    {
        if ($request->user()->role !== 'admin') {
            abort(403);
        }

        $createdInquiries = $user->inquiries()
            ->with('assignee')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(5, ['*'], 'created_page');

        $assignedInquiries = $user->assignedInquiries()
            ->with('user')
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(5, ['*'], 'assigned_page');

        return view('admin.users.show', compact(
            'user',
            'createdInquiries',
            'assignedInquiries'
        ));
    }

}
