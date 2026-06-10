<?php

use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\InquiryController;
use App\Http\Controllers\PasswordController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| 認証不要ルート
|--------------------------------------------------------------------------
*/

Route::get('/login', [AuthController::class, 'showLoginForm'])
    ->name('login');

Route::post('/login', [AuthController::class, 'login'])
    ->name('login.store');

Route::get('/register', [AuthController::class, 'showRegisterForm'])
    ->name('register');

Route::post('/register', [AuthController::class, 'register'])
    ->name('register.store');


/*
|--------------------------------------------------------------------------
| ログイン済み共通ルート
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('logout');

    Route::get('/', [InquiryController::class, 'create'])
        ->name('inquiries.create');

    Route::post('/inquiries', [InquiryController::class, 'store'])
        ->name('inquiries.store');

    Route::post('/inquiries/{inquiry}/comments', [InquiryController::class, 'storeComment'])
        ->name('inquiries.comments.store');
});


/*
|--------------------------------------------------------------------------
| 管理者ルート
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/inquiries', [InquiryController::class, 'index'])
            ->name('inquiries.index');

        Route::get('/users', [AdminUserController::class, 'index'])
            ->name('users.index');

        Route::get('/users/{user}', [AdminUserController::class, 'show'])
            ->name('users.show');

        Route::get('/inquiries/{inquiry}', [InquiryController::class, 'show'])
            ->name('inquiries.show');

        Route::put('/inquiries/{inquiry}', [InquiryController::class, 'update'])
            ->name('inquiries.update');

        Route::delete('/inquiries/{inquiry}', [InquiryController::class, 'destroy'])
            ->name('inquiries.destroy');
    });


/*
|--------------------------------------------------------------------------
| マイページルート
|--------------------------------------------------------------------------
*/

Route::middleware('auth')
    ->prefix('my')
    ->name('my.')
    ->group(function () {
        Route::get('/inquiries', [InquiryController::class, 'myIndex'])
            ->name('inquiries.index');

        Route::get('/inquiries/{inquiry}', [InquiryController::class, 'myShow'])
            ->name('inquiries.show');

        Route::get('/password', [PasswordController::class, 'edit'])
            ->name('password.edit');

        Route::put('/password', [PasswordController::class, 'update'])
            ->name('password.update');
    });
