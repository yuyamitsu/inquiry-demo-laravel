<?php

namespace Database\Seeders;

use App\Models\Inquiry;
use App\Models\KnowledgeArticle;
use App\Models\User;
use Illuminate\Database\Seeder;

class KnowledgeArticleSeeder extends Seeder
{
    public function run(): void
    {
        $creator = User::whereIn('role', ['admin', 'staff'])
            ->orderBy('id')
            ->first();

        if (! $creator) {
            return;
        }

        $articles = [
            '料金プランについて知りたい' => [
                'title' => '料金プラン問い合わせ時の案内手順',
                'category' => '料金',
                'body' => "料金プランに関する問い合わせでは、まず利用予定人数、利用したい機能、契約期間を確認する。\n\n単純な料金表だけでは判断できない場合があるため、現在の利用状況や導入目的も確認する。\n\n必要に応じて、見積相談や導入相談の問い合わせへ誘導する。",
                'is_published' => true,
            ],
            '管理画面にログインできません' => [
                'title' => '管理画面にログインできない場合の確認手順',
                'category' => 'ログイン',
                'body' => "ログイン不可の問い合わせでは、まずメールアドレスとパスワードの入力ミスを確認する。\n\n次に、対象ユーザーが登録済みか、権限が正しく設定されているかを確認する。\n\nパスワード忘れの場合は、管理者による仮パスワード再設定を行い、ログイン後に本人へパスワード変更を依頼する。",
                'is_published' => true,
            ],
            '導入について相談したい' => [
                'title' => '導入相談問い合わせの初期確認項目',
                'category' => '導入相談',
                'body' => "導入相談では、現在の課題、利用人数、利用したい機能、導入希望時期を確認する。\n\n問い合わせ内容だけで判断せず、必要に応じて追加ヒアリングを行う。\n\n対応履歴はコメントに残し、後から担当者が確認できるようにする。",
                'is_published' => true,
            ],
            '請求書の内容について' => [
                'title' => '請求書内容確認時の対応手順',
                'category' => '請求',
                'body' => "請求書に関する問い合わせでは、対象月、請求番号、確認したい明細項目を確認する。\n\n金額に関する問い合わせは誤案内を防ぐため、回答前に請求情報を確認する。\n\n必要に応じて、管理者または経理担当へエスカレーションする。",
                'is_published' => true,
            ],
            '画面表示が崩れています' => [
                'title' => '画面表示崩れ問い合わせの確認手順',
                'category' => '画面表示',
                'body' => "画面表示が崩れている問い合わせでは、利用端末、ブラウザ、画面サイズ、発生している画面を確認する。\n\nスマートフォンでのみ発生する場合は、レスポンシブ表示の問題を疑う。\n\n修正後は、PC表示とスマートフォン表示の両方で確認する。",
                'is_published' => true,
            ],
            'メール通知が届きません' => [
                'title' => 'メール通知が届かない場合の確認手順',
                'category' => 'メール',
                'body' => "メール通知が届かない問い合わせでは、登録メールアドレス、迷惑メールフォルダ、受信設定を確認する。\n\nシステム側でメール送信機能が未実装または停止中の場合は、その旨を案内する。\n\n今後メール通知を実装する場合は、送信ログやエラー確認の仕組みも必要になる。",
                'is_published' => true,
            ],
            'サポート対応時間について' => [
                'title' => 'サポート対応時間の案内ルール',
                'category' => 'サポート',
                'body' => "サポート対応時間の問い合わせでは、受付時間、回答目安、休日対応の有無を案内する。\n\nDemo環境では、対応時間を仮のルールとして設定しておき、ユーザー向けFAQにも同じ内容を掲載できるようにする。\n\n回答済み後は、必要に応じて問い合わせをクローズする。",
                'is_published' => true,
            ],
            '導入費用の見積相談' => [
                'title' => '導入費用見積相談の対応手順',
                'category' => '見積',
                'body' => "導入費用の見積相談では、利用人数、必要機能、導入範囲、希望時期を確認する。\n\n概算回答だけで済ませず、条件によって費用が変わることを説明する。\n\n優先度が高い問い合わせとして扱い、対応期限を設定して進捗を管理する。",
                'is_published' => true,
            ],
            'パスワードを変更したい' => [
                'title' => 'ユーザー自身のパスワード変更案内',
                'category' => 'アカウント',
                'body' => "ユーザー自身がパスワードを変更したい場合は、右上メニューの「パスワード変更」から変更できる。\n\n変更時には現在のパスワード、新しいパスワード、新しいパスワード確認を入力してもらう。\n\n管理者による仮パスワード再設定とは別機能であるため、本人がログインできる場合はこの画面を案内する。",
                'is_published' => true,
            ],
            'FAQを確認しても解決しません' => [
                'title' => 'FAQ確認後の問い合わせ受付ルール',
                'category' => 'FAQ',
                'body' => "一般ユーザーは問い合わせ前にFAQを確認する。\n\nFAQを確認しても解決しない場合は、確認チェックを入れたうえで問い合わせフォームへ進む。\n\n問い合わせ登録後はFAQ確認状態をリセットし、次回問い合わせ時もFAQ確認を求めることで、自己解決導線を維持する。",
                'is_published' => true,
            ],
        ];

        foreach ($articles as $inquiryTitle => $article) {
            $inquiry = Inquiry::where('title', $inquiryTitle)->first();

            if (! $inquiry) {
                continue;
            }

            KnowledgeArticle::updateOrCreate(
                ['inquiry_id' => $inquiry->id],
                [
                    'created_by' => $creator->id,
                    'title' => $article['title'],
                    'category' => $article['category'],
                    'body' => $article['body'],
                    'is_published' => $article['is_published'],
                ]
            );
        }
    }
}
