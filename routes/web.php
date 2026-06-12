<?php

use App\Http\Controllers\Admin\FaqController as AdminFaqController;
use App\Http\Controllers\Admin\KnowledgeArticleController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FaqController;
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
|
| admin / staff / user の全員が使うルート
|
*/

Route::middleware('auth')->group(function () {
    /*
    |--------------------------------------------------------------------------
    | ログアウト
    |--------------------------------------------------------------------------
    */

    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('logout');


    /*
    |--------------------------------------------------------------------------
    | ユーザー向けFAQ
    |--------------------------------------------------------------------------
    |
    | 問い合わせ前に確認してもらうFAQ。
    | ナレッジとは別の、ユーザー公開用FAQ。
    |
    */

    Route::get('/faq', [FaqController::class, 'index'])
        ->name('faqs.index');

    Route::get('/faq/{faq}', [FaqController::class, 'show'])
        ->name('faqs.show');

    Route::post('/faq/confirm', [FaqController::class, 'confirm'])
        ->name('faqs.confirm');


    /*
    |--------------------------------------------------------------------------
    | 問い合わせ登録
    |--------------------------------------------------------------------------
    |
    | InquiryController 側で、
    | 一般ユーザーはFAQ確認済みでないと問い合わせフォームへ進めないようにする。
    |
    */

    Route::get('/', [InquiryController::class, 'create'])
        ->name('inquiries.create');

    Route::post('/inquiries', [InquiryController::class, 'store'])
        ->name('inquiries.store');

    Route::post('/inquiries/{inquiry}/comments', [InquiryController::class, 'storeComment'])
        ->name('inquiries.comments.store');
});


/*
|--------------------------------------------------------------------------
| 管理側ルート
|--------------------------------------------------------------------------
|
| /admin 配下。
| AdminMiddleware 側で admin / staff を許可。
| ただし、ユーザー管理・仮パスワード再設定・FAQ管理などは
| 各Controller側で admin のみに制限する。
|
*/

Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | 問い合わせ管理
        |--------------------------------------------------------------------------
        */

        Route::get('/inquiries', [InquiryController::class, 'index'])
            ->name('inquiries.index');

        Route::get('/inquiries/{inquiry}/knowledge/create', [KnowledgeArticleController::class, 'createFromInquiry'])
            ->name('inquiries.knowledge.create');

        Route::get('/inquiries/{inquiry}', [InquiryController::class, 'show'])
            ->name('inquiries.show');

        Route::put('/inquiries/{inquiry}', [InquiryController::class, 'update'])
            ->name('inquiries.update');

        Route::delete('/inquiries/{inquiry}', [InquiryController::class, 'destroy'])
            ->name('inquiries.destroy');


        /*
        |--------------------------------------------------------------------------
        | ナレッジ管理
        |--------------------------------------------------------------------------
        |
        | 管理者・staff向けの内部ナレッジ。
        | ユーザーには直接見せない。
        |
        */

        Route::get('/knowledge', [KnowledgeArticleController::class, 'index'])
            ->name('knowledge.index');

        Route::post('/knowledge', [KnowledgeArticleController::class, 'store'])
            ->name('knowledge.store');

        Route::get('/knowledge/{knowledgeArticle}/edit', [KnowledgeArticleController::class, 'edit'])
            ->name('knowledge.edit');

        Route::put('/knowledge/{knowledgeArticle}', [KnowledgeArticleController::class, 'update'])
            ->name('knowledge.update');

        Route::get('/knowledge/{knowledgeArticle}', [KnowledgeArticleController::class, 'show'])
            ->name('knowledge.show');


        /*
        |--------------------------------------------------------------------------
        | FAQ管理
        |--------------------------------------------------------------------------
        |
        | ユーザー向けFAQの管理画面。
        | /admin 配下にはあるが、実際の操作は
        | Admin\FaqController 側で admin のみに制限する。
        |
        */

        Route::resource('faqs', AdminFaqController::class)
            ->except(['show']);


        /*
        |--------------------------------------------------------------------------
        | ユーザー管理
        |--------------------------------------------------------------------------
        |
        | ルート自体は /admin 配下にあるが、
        | 実際の利用は Admin\UserController 側で admin のみに制限する。
        |
        */

        Route::get('/users', [AdminUserController::class, 'index'])
            ->name('users.index');

        Route::get('/users/create', [AdminUserController::class, 'create'])
            ->name('users.create');

        Route::post('/users', [AdminUserController::class, 'store'])
            ->name('users.store');

        Route::get('/users/{user}/password', [AdminUserController::class, 'editPassword'])
            ->name('users.password.edit');

        Route::put('/users/{user}/password', [AdminUserController::class, 'updatePassword'])
            ->name('users.password.update');

        Route::get('/users/{user}', [AdminUserController::class, 'show'])
            ->name('users.show');
    });


/*
|--------------------------------------------------------------------------
| マイページルート
|--------------------------------------------------------------------------
|
| ログイン済みユーザー本人用。
| 一般ユーザーだけでなく、admin / staff も自分のパスワード変更で使用する。
|
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
    