<?php

namespace Database\Seeders;

use App\Models\Inquiry;
use App\Models\InquiryComment;
use App\Models\User;
use Illuminate\Database\Seeder;

class InquirySeeder extends Seeder
{
    public function run(): void
    {
        $adminUser = User::where('email', 'admin@example.com')->first();

        $satoUser = User::where('email', 'sato@example.com')->first();
        $suzukiUser = User::where('email', 'suzuki@example.com')->first();
        $takahashiUser = User::where('email', 'takahashi@example.com')->first();

        $mainUser = User::where('email', 'user@example.com')->first();
        $yamadaUser = User::where('email', 'yamada@example.com')->first();
        $tanakaUser = User::where('email', 'tanaka@example.com')->first();

        $normalUser = $mainUser ?? User::where('role', 'user')->orderBy('id')->first();

        if (! $normalUser) {
            return;
        }

        $staffFallback = User::where('role', 'staff')->orderBy('id')->first() ?? $adminUser;

        $resolveUser = function (?string $key) use (
            $adminUser,
            $satoUser,
            $suzukiUser,
            $takahashiUser,
            $mainUser,
            $yamadaUser,
            $tanakaUser,
            $normalUser,
            $staffFallback
        ) {
            return match ($key) {
                'admin' => $adminUser,

                'sato' => $satoUser ?? $staffFallback,
                'suzuki' => $suzukiUser ?? $staffFallback,
                'takahashi' => $takahashiUser ?? $staffFallback,
                'staff' => $staffFallback,

                'user' => $mainUser ?? $normalUser,
                'yamada' => $yamadaUser ?? $normalUser,
                'tanaka' => $tanakaUser ?? $normalUser,

                default => null,
            };
        };

        $inquiries = [
            [
                'customer' => 'user',
                'title' => '料金プランについて知りたい',
                'category' => '質問',
                'body' => 'サービスの料金プランについて詳しく教えてください。',
                'status' => '回答済み',
                'assignee' => 'admin',
                'priority' => '中',
                'due_date' => now()->addDays(3)->toDateString(),
                'comments' => [
                    ['author' => 'customer', 'body' => 'サービスの料金プランについて詳しく教えてください。'],
                    ['author' => 'admin', 'body' => 'ご利用人数や必要な機能によって料金が変わります。まずは利用予定人数を教えてください。'],
                    ['author' => 'customer', 'body' => '利用人数は10名程度を想定しています。'],
                    ['author' => 'admin', 'body' => '10名程度であれば標準プランでのご利用をおすすめします。'],
                ],
            ],
            [
                'customer' => 'yamada',
                'title' => '管理画面にログインできません',
                'category' => '不具合',
                'body' => '昨日から管理画面にログインできない状態です。確認をお願いします。',
                'status' => '対応中',
                'assignee' => 'sato',
                'priority' => '高',
                'due_date' => now()->addDay()->toDateString(),
                'comments' => [
                    ['author' => 'customer', 'body' => '昨日から管理画面にログインできない状態です。確認をお願いします。'],
                    ['author' => 'sato', 'body' => '現在、ログイン情報とアカウント状態を確認しています。'],
                    ['author' => 'sato', 'body' => 'パスワード誤入力の可能性があるため、必要に応じて仮パスワード再設定を案内します。'],
                ],
            ],
            [
                'customer' => 'tanaka',
                'title' => '導入について相談したい',
                'category' => '相談',
                'body' => '社内システムの導入について、一度相談したいです。',
                'status' => '回答済み',
                'assignee' => 'suzuki',
                'priority' => '中',
                'due_date' => now()->addDays(5)->toDateString(),
                'comments' => [
                    ['author' => 'customer', 'body' => '社内システムの導入について、一度相談したいです。'],
                    ['author' => 'suzuki', 'body' => 'お問い合わせありがとうございます。現在の課題や利用人数を確認した上でご案内します。'],
                    ['author' => 'customer', 'body' => '問い合わせ対応と進捗管理をまとめたいです。'],
                    ['author' => 'suzuki', 'body' => '問い合わせ管理とチケット管理を組み合わせた構成が適していると思われます。'],
                ],
            ],
            [
                'customer' => 'user',
                'title' => '請求書の内容について',
                'category' => '質問',
                'body' => '請求書の明細について確認したい項目があります。',
                'status' => '未対応',
                'assignee' => null,
                'priority' => '低',
                'due_date' => null,
                'comments' => [
                    ['author' => 'customer', 'body' => '請求書の明細について確認したい項目があります。'],
                ],
            ],
            [
                'customer' => 'yamada',
                'title' => '画面表示が崩れています',
                'category' => '不具合',
                'body' => 'スマートフォンで確認した際に、一覧画面の表示が崩れているように見えます。',
                'status' => 'クローズ',
                'assignee' => 'takahashi',
                'priority' => '高',
                'due_date' => now()->subDays(2)->toDateString(),
                'comments' => [
                    ['author' => 'customer', 'body' => 'スマートフォンで確認した際に、一覧画面の表示が崩れているように見えます。'],
                    ['author' => 'takahashi', 'body' => 'スマートフォン表示で検索フォームの幅が崩れていることを確認しました。'],
                    ['author' => 'takahashi', 'body' => 'CSSを修正し、スマートフォン表示でも崩れないことを確認しました。'],
                ],
            ],
            [
                'customer' => 'tanaka',
                'title' => 'メール通知が届きません',
                'category' => '不具合',
                'body' => '問い合わせ後の通知メールが届かないようです。',
                'status' => '未対応',
                'assignee' => null,
                'priority' => '緊急',
                'due_date' => now()->subDay()->toDateString(),
                'comments' => [
                    ['author' => 'customer', 'body' => '問い合わせ後の通知メールが届かないようです。'],
                ],
            ],
            [
                'customer' => 'user',
                'title' => 'サポート対応時間について',
                'category' => '質問',
                'body' => 'サポートの受付時間を教えてください。',
                'status' => '回答済み',
                'assignee' => 'admin',
                'priority' => '低',
                'due_date' => now()->addDays(7)->toDateString(),
                'comments' => [
                    ['author' => 'customer', 'body' => 'サポートの受付時間を教えてください。'],
                    ['author' => 'admin', 'body' => '平日10時から18時まで対応しています。'],
                ],
            ],
            [
                'customer' => 'yamada',
                'title' => '導入費用の見積相談',
                'category' => '相談',
                'body' => '自社で導入する場合の概算費用を知りたいです。',
                'status' => '対応中',
                'assignee' => 'sato',
                'priority' => '高',
                'due_date' => now()->toDateString(),
                'comments' => [
                    ['author' => 'customer', 'body' => '自社で導入する場合の概算費用を知りたいです。'],
                    ['author' => 'sato', 'body' => '概算費用を確認しています。本日中に回答します。'],
                    ['author' => 'customer', 'body' => '利用人数は20名ほどで、問い合わせ管理とFAQ機能を使いたいです。'],
                ],
            ],
            [
                'customer' => 'tanaka',
                'title' => 'パスワードを変更したい',
                'category' => '質問',
                'body' => 'ログイン後に自分のパスワードを変更する方法を知りたいです。',
                'status' => '回答済み',
                'assignee' => 'suzuki',
                'priority' => '中',
                'due_date' => now()->addDays(2)->toDateString(),
                'comments' => [
                    ['author' => 'customer', 'body' => 'ログイン後に自分のパスワードを変更する方法を知りたいです。'],
                    ['author' => 'suzuki', 'body' => '右上メニューの「パスワード変更」から変更できます。現在のパスワードと新しいパスワードを入力してください。'],
                ],
            ],
            [
                'customer' => 'user',
                'title' => 'FAQを確認しても解決しません',
                'category' => '相談',
                'body' => 'FAQを確認しましたが、解決できなかったため問い合わせたいです。',
                'status' => '対応中',
                'assignee' => 'takahashi',
                'priority' => '中',
                'due_date' => now()->addDays(4)->toDateString(),
                'comments' => [
                    ['author' => 'customer', 'body' => 'FAQを確認しましたが、解決できなかったため問い合わせたいです。'],
                    ['author' => 'takahashi', 'body' => 'FAQで解決しない場合は、発生している状況を具体的に確認します。'],
                ],
            ],
        ];

        foreach ($inquiries as $inquiryData) {
            $comments = $inquiryData['comments'];

            $customer = $resolveUser($inquiryData['customer']) ?? $normalUser;
            $assignee = $resolveUser($inquiryData['assignee']);

            unset($inquiryData['comments'], $inquiryData['customer'], $inquiryData['assignee']);

            $inquiry = Inquiry::updateOrCreate(
                ['title' => $inquiryData['title']],
                [
                    'user_id' => $customer->id,
                    'name' => $customer->name,
                    'email' => $customer->email,
                    'category' => $inquiryData['category'],
                    'body' => $inquiryData['body'],
                    'status' => $inquiryData['status'],
                    'assignee_id' => $assignee?->id,
                    'priority' => $inquiryData['priority'],
                    'due_date' => $inquiryData['due_date'],
                ]
            );

            InquiryComment::where('inquiry_id', $inquiry->id)->delete();

            foreach ($comments as $comment) {
                $commentUser = $comment['author'] === 'customer'
                    ? $customer
                    : $resolveUser($comment['author']);

                if (! $commentUser) {
                    continue;
                }

                InquiryComment::create([
                    'inquiry_id' => $inquiry->id,
                    'user_id' => $commentUser->id,
                    'body' => $comment['body'],
                ]);
            }
        }
    }
}
