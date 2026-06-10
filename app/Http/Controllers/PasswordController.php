<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class PasswordController extends Controller
{
    public function edit()
    {
        return view('my.password.edit');
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::findOrFail(Auth::id());

        if (! Hash::check($validated['current_password'], $user->password)) {
            return back()
                ->withErrors([
                    'current_password' => '現在のパスワードが正しくありません。',
                ])
                ->withInput();
        }

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()
            ->route('my.password.edit')
            ->with('success', 'パスワードを変更しました。');
    }
}
