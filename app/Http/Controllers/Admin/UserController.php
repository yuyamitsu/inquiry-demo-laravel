<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

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

    public function create(Request $request)
    {
        if ($request->user()->role !== 'admin') {
            abort(403);
        }

        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        if ($request->user()->role !== 'admin') {
            abort(403);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:50'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'role' => ['required', Rule::in(['admin', 'staff', 'user'])],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()
            ->route('admin.users.show', $user)
            ->with('success', 'ユーザーを作成しました。');
    }

    public function editPassword(Request $request, User $user)
    {
        if ($request->user()->role !== 'admin') {
            abort(403);
        }

        return view('admin.users.password', compact('user'));
    }

    public function updatePassword(Request $request, User $user)
    {
        if ($request->user()->role !== 'admin') {
            abort(403);
        }

        $validated = $request->validate([
            'password' => [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/^[!-~]+$/',
            ],
        ]);

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()
            ->route('admin.users.show', $user)
            ->with('success', '仮パスワードを再設定しました。ユーザーへ別途共有してください。');
    }

}
