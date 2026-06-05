<?php

namespace Database\Seeders;

use App\Models\Inquiry;
use Illuminate\Database\Seeder;

class InquirySeeder extends Seeder
{
    public function run(): void
    {
        $inquiries = [
            [
                'name' => '山田 太郎',
                'email' => 'yamada@example.com',
                'title' => '料金プランについて知りたい',
                'category' => '質問',
                'body' => 'サービスの料金プランについて詳しく教えてください。',
                'status' => '未対応',
                'admin_reply' => null,
            ],
            [
                'name' => '佐藤 花子',
                'email' => 'sato@example.com',
                'title' => '管理画面にログインできません',
                'category' => '不具合',
                'body' => '昨日から管理画面にログインできない状態です。確認をお願いします。',
                'status' => '対応中',
                'admin_reply' => '現在、原因を確認しています。',
            ],
            [
                'name' => '田中 一郎',
                'email' => 'tanaka@example.com',
                'title' => '導入について相談したい',
                'category' => '相談',
                'body' => '社内システムの導入について、一度相談したいです。',
                'status' => '回答済み',
                'admin_reply' => 'お問い合わせありがとうございます。担当者よりご連絡いたします。',
            ],
            [
                'name' => '鈴木 次郎',
                'email' => 'suzuki@example.com',
                'title' => '請求書の内容について',
                'category' => '質問',
                'body' => '請求書の明細について確認したい項目があります。',
                'status' => '未対応',
                'admin_reply' => null,
            ],
            [
                'name' => '高橋 美咲',
                'email' => 'takahashi@example.com',
                'title' => '画面表示が崩れています',
                'category' => '不具合',
                'body' => 'スマートフォンで確認した際に、一覧画面の表示が崩れているように見えます。',
                'status' => 'クローズ',
                'admin_reply' => '修正対応が完了しました。',
            ],
            [
                'name' => '伊藤 健',
                'email' => 'ito@example.com',
                'title' => '資料請求について',
                'category' => '質問',
                'body' => 'サービス概要資料を送っていただくことは可能でしょうか。',
                'status' => '未対応',
                'admin_reply' => null,
            ],
            [
                'name' => '渡辺 直子',
                'email' => 'watanabe@example.com',
                'title' => '契約内容の確認',
                'category' => '相談',
                'body' => '契約前に確認したい内容があります。担当者の方と相談したいです。',
                'status' => '対応中',
                'admin_reply' => '担当者へ確認中です。',
            ],
            [
                'name' => '中村 翔',
                'email' => 'nakamura@example.com',
                'title' => 'メール通知が届きません',
                'category' => '不具合',
                'body' => '問い合わせ後の通知メールが届かないようです。',
                'status' => '未対応',
                'admin_reply' => null,
            ],
            [
                'name' => '小林 愛',
                'email' => 'kobayashi@example.com',
                'title' => 'サポート対応時間について',
                'category' => '質問',
                'body' => 'サポートの受付時間を教えてください。',
                'status' => '回答済み',
                'admin_reply' => '平日10時から18時まで対応しています。',
            ],
            [
                'name' => '加藤 誠',
                'email' => 'kato@example.com',
                'title' => '導入費用の見積相談',
                'category' => '相談',
                'body' => '自社で導入する場合の概算費用を知りたいです。',
                'status' => '未対応',
                'admin_reply' => null,
            ],
            [
                'name' => '吉田 玲奈',
                'email' => 'yoshida@example.com',
                'title' => '登録情報の変更について',
                'category' => '質問',
                'body' => '登録しているメールアドレスを変更したいです。',
                'status' => '対応中',
                'admin_reply' => '変更手順を確認中です。',
            ],
            [
                'name' => '山本 大輔',
                'email' => 'yamamoto@example.com',
                'title' => 'エラー画面が表示されます',
                'category' => '不具合',
                'body' => '送信ボタンを押した際にエラー画面が表示されました。',
                'status' => '未対応',
                'admin_reply' => null,
            ],
        ];

        foreach ($inquiries as $inquiry) {
            Inquiry::create($inquiry);
        }
    }
}
