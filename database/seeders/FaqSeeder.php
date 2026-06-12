<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    public function run(): void
    {
        $faqs = [
            [
                'title' => 'ログインできない場合はどうすればよいですか？',
                'category' => 'ログイン',
                'body' => "メールアドレスとパスワードが正しいか確認してください。\nパスワードを忘れた場合は、管理者に仮パスワードの再設定を依頼してください。",
                'sort_order' => 10,
                'is_published' => true,
            ],
            [
                'title' => '問い合わせを登録する前に確認することはありますか？',
                'category' => '問い合わせ',
                'body' => "まずFAQに同じ内容がないか確認してください。\nFAQで解決しない場合は、問い合わせフォームから詳細を入力してください。",
                'sort_order' => 20,
                'is_published' => true,
            ],
            [
                'title' => '問い合わせ後に内容を追加できますか？',
                'category' => '問い合わせ',
                'body' => "問い合わせ詳細画面のコメント欄から、追加情報を投稿できます。\n状況やエラーメッセージなどを追記してください。",
                'sort_order' => 30,
                'is_published' => true,
            ],
            [
                'title' => '問い合わせの対応状況はどこで確認できますか？',
                'category' => '問い合わせ',
                'body' => "ログイン後の「自分の問い合わせ」画面から、登録した問い合わせのステータスを確認できます。\nステータスには未対応、対応中、回答済み、クローズがあります。",
                'sort_order' => 40,
                'is_published' => true,
            ],
            [
                'title' => 'パスワードを変更したい場合はどうすればよいですか？',
                'category' => 'アカウント',
                'body' => "ログイン後、右上メニューの「パスワード変更」から変更できます。\n現在のパスワードと新しいパスワードを入力してください。",
                'sort_order' => 50,
                'is_published' => true,
            ],
            [
                'title' => '登録した問い合わせを削除できますか？',
                'category' => '問い合わせ',
                'body' => "一般ユーザーは問い合わせを削除できません。\n削除が必要な場合は、管理者または担当者に相談してください。",
                'sort_order' => 60,
                'is_published' => true,
            ],
            [
                'title' => '対応期限とは何ですか？',
                'category' => '問い合わせ',
                'body' => "対応期限は、管理者または担当者が問い合わせ対応の目安として設定する日付です。\n期限切れの場合は、管理側の一覧で分かるように表示されます。",
                'sort_order' => 70,
                'is_published' => true,
            ],
            [
                'title' => '優先度とは何ですか？',
                'category' => '問い合わせ',
                'body' => "優先度は、問い合わせの重要度を表します。\n低・中・高・緊急の4段階で管理されます。",
                'sort_order' => 80,
                'is_published' => true,
            ],
            [
                'title' => '問い合わせに画像やファイルを添付できますか？',
                'category' => '添付ファイル',
                'body' => "現在のDemoでは、ファイル添付機能は未実装です。\n必要な情報は本文またはコメント欄に記載してください。",
                'sort_order' => 90,
                'is_published' => true,
            ],
            [
                'title' => 'FAQを確認しても解決しない場合はどうすればよいですか？',
                'category' => '問い合わせ',
                'body' => "FAQを確認しても解決しない場合は、「FAQを確認しました」にチェックを入れて、問い合わせフォームへ進んでください。",
                'sort_order' => 100,
                'is_published' => true,
            ],
        ];

        foreach ($faqs as $faq) {
            Faq::updateOrCreate(
                ['title' => $faq['title']],
                $faq
            );
        }
    }
}
