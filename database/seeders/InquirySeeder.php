<?php

namespace Database\Seeders;

use App\Models\Inquiry;
use App\Models\InquiryComment;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class InquirySeeder extends Seeder
{
    public function run(): void
    {
        $adminUser = User::where('role', 'admin')->first();
        $normalUser = User::where('role', 'user')->first();

        if (! $normalUser) {
            return;
        }

        $inquiries = [
            [
                'name' => $normalUser->name,
                'email' => $normalUser->email,
                'title' => '料金プランについて知りたい',
                'category' => '質問',
                'body' => 'サービスの料金プランについて詳しく教えてください。',
                'status' => '未対応',
                'assignee_id' => null,
                'priority' => '中',
                'due_date' => now()->addDays(3)->toDateString(),
                'comments' => [
                    [
                        'user_id' => $normalUser->id,
                        'body' => 'サービスの料金プランについて詳しく教えてください。',
                    ],
                ],
            ],
            [
                'name' => $normalUser->name,
                'email' => $normalUser->email,
                'title' => '管理画面にログインできません',
                'category' => '不具合',
                'body' => '昨日から管理画面にログインできない状態です。確認をお願いします。',
                'status' => '対応中',
                'assignee_id' => $adminUser?->id,
                'priority' => '高',
                'due_date' => now()->addDay()->toDateString(),
                'comments' => [
                    [
                        'user_id' => $normalUser->id,
                        'body' => '昨日から管理画面にログインできない状態です。確認をお願いします。',
                    ],
                    [
                        'user_id' => $adminUser?->id,
                        'body' => '現在、原因を確認しています。確認でき次第、こちらで回答します。',
                    ],
                ],
            ],
            [
                'name' => $normalUser->name,
                'email' => $normalUser->email,
                'title' => '導入について相談したい',
                'category' => '相談',
                'body' => '社内システムの導入について、一度相談したいです。',
                'status' => '回答済み',
                'assignee_id' => $adminUser?->id,
                'priority' => '中',
                'due_date' => now()->addDays(5)->toDateString(),
                'comments' => [
                    [
                        'user_id' => $normalUser->id,
                        'body' => '社内システムの導入について、一度相談したいです。',
                    ],
                    [
                        'user_id' => $adminUser?->id,
                        'body' => 'お問い合わせありがとうございます。担当者よりご連絡いたします。',
                    ],
                ],
            ],
            [
                'name' => $normalUser->name,
                'email' => $normalUser->email,
                'title' => '請求書の内容について',
                'category' => '質問',
                'body' => '請求書の明細について確認したい項目があります。',
                'status' => '未対応',
                'assignee_id' => null,
                'priority' => '低',
                'due_date' => null,
                'comments' => [
                    [
                        'user_id' => $normalUser->id,
                        'body' => '請求書の明細について確認したい項目があります。',
                    ],
                ],
            ],
            [
                'name' => $normalUser->name,
                'email' => $normalUser->email,
                'title' => '画面表示が崩れています',
                'category' => '不具合',
                'body' => 'スマートフォンで確認した際に、一覧画面の表示が崩れているように見えます。',
                'status' => 'クローズ',
                'assignee_id' => $adminUser?->id,
                'priority' => '高',
                'due_date' => now()->subDays(2)->toDateString(),
                'comments' => [
                    [
                        'user_id' => $normalUser->id,
                        'body' => 'スマートフォンで確認した際に、一覧画面の表示が崩れているように見えます。',
                    ],
                    [
                        'user_id' => $adminUser?->id,
                        'body' => '修正対応が完了しました。',
                    ],
                ],
            ],
            [
                'name' => $normalUser->name,
                'email' => $normalUser->email,
                'title' => 'メール通知が届きません',
                'category' => '不具合',
                'body' => '問い合わせ後の通知メールが届かないようです。',
                'status' => '未対応',
                'assignee_id' => null,
                'priority' => '緊急',
                'due_date' => now()->subDay()->toDateString(),
                'comments' => [
                    [
                        'user_id' => $normalUser->id,
                        'body' => '問い合わせ後の通知メールが届かないようです。',
                    ],
                ],
            ],
            [
                'name' => $normalUser->name,
                'email' => $normalUser->email,
                'title' => 'サポート対応時間について',
                'category' => '質問',
                'body' => 'サポートの受付時間を教えてください。',
                'status' => '回答済み',
                'assignee_id' => $adminUser?->id,
                'priority' => '低',
                'due_date' => now()->addDays(7)->toDateString(),
                'comments' => [
                    [
                        'user_id' => $normalUser->id,
                        'body' => 'サポートの受付時間を教えてください。',
                    ],
                    [
                        'user_id' => $adminUser?->id,
                        'body' => '平日10時から18時まで対応しています。',
                    ],
                ],
            ],
            [
                'name' => $normalUser->name,
                'email' => $normalUser->email,
                'title' => '導入費用の見積相談',
                'category' => '相談',
                'body' => '自社で導入する場合の概算費用を知りたいです。',
                'status' => '対応中',
                'assignee_id' => $adminUser?->id,
                'priority' => '高',
                'due_date' => now()->toDateString(),
                'comments' => [
                    [
                        'user_id' => $normalUser->id,
                        'body' => '自社で導入する場合の概算費用を知りたいです。',
                    ],
                    [
                        'user_id' => $adminUser?->id,
                        'body' => '概算費用を確認しています。本日中に回答します。',
                    ],
                ],
            ],
        ];

        foreach ($inquiries as $inquiryData) {
            $comments = $inquiryData['comments'];
            unset($inquiryData['comments']);

            $inquiry = Inquiry::create([
                'user_id' => $normalUser->id,
                'name' => $inquiryData['name'],
                'email' => $inquiryData['email'],
                'title' => $inquiryData['title'],
                'category' => $inquiryData['category'],
                'body' => $inquiryData['body'],
                'status' => $inquiryData['status'],
                'assignee_id' => $inquiryData['assignee_id'],
                'priority' => $inquiryData['priority'],
                'due_date' => $inquiryData['due_date'],
            ]);

            foreach ($comments as $comment) {
                if (! $comment['user_id']) {
                    continue;
                }

                InquiryComment::create([
                    'inquiry_id' => $inquiry->id,
                    'user_id' => $comment['user_id'],
                    'body' => $comment['body'],
                ]);
            }
        }
    }
}
