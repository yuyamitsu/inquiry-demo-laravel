# 問い合わせ管理Demo Laravel版

## 概要

問い合わせ管理DemoサイトのLaravel版です。

静的Demo版では、HTML / CSS / JavaScript / localStorage を使用して、問い合わせの登録・一覧表示・詳細表示・ステータス変更・返答保存などを実装しました。

本プロジェクトでは、それらの機能を Laravel + DB 構成に置き換え、問い合わせデータをサーバー側で管理できる形に拡張しています。

現在はローカル開発環境および公開検証環境で扱いやすい SQLite を使用しています。
実運用や実務に近い構成を想定する段階では、MySQL への移行も検討します。

また、スターレンタルサーバ上で Laravel アプリとして公開検証を行い、ブラウザから主要機能が利用できることを確認済みです。

本Demoは、単なる問い合わせフォームではなく、**中規模の問い合わせ・チケット管理Demo** として、担当者・優先度・対応期限・コメントスレッド・変更履歴・権限制御を持つ構成を目指しています。

---

## 作成目的

このプロジェクトは、単に画面や機能を作るだけではなく、問い合わせ管理システムとして必要な画面構成・DB設計・処理フロー・認証・権限制御を整理しながら、段階的に実装することを目的としています。

小規模な問い合わせフォームでは、多くの利用者から1つの窓口に問い合わせが集まり、少人数の管理者が対応する構成で十分です。

一方で、中規模の問い合わせ管理では、問い合わせ件数や対応者が増えるため、以下のような管理が必要になります。

* 誰が対応するか
* どの問い合わせを優先するか
* いつまでに対応するか
* 管理者と一般ユーザーのやり取りを残せるか
* 変更履歴を追跡できるか
* 管理者と一般ユーザーで見える範囲を制御できるか

そのため、本Demoでは問い合わせ1件を「チケット」のように扱い、ステータス、担当者、優先度、対応期限、コメントスレッド、変更履歴を持つ構成にしています。

静的Demoで作成した以下の流れを、Laravel側で再現・拡張しています。

```text
ユーザーログイン
↓
問い合わせ受付
↓
問い合わせ登録
↓
DB保存
↓
一般ユーザーは自分の問い合わせを確認
↓
管理者は全問い合わせを確認
↓
担当者・優先度・対応期限を設定
↓
コメントスレッドでやり取り
↓
ステータス更新
↓
変更履歴記録
↓
検索・絞り込み
↓
対応完了・クローズ
```

---

## 使用技術

* PHP
* Laravel
* SQLite
* Blade
* HTML
* CSS
* Composer
* Git / GitHub
* Star Rental Server

---

## 現在のDB構成

現在は SQLite を使用しています。

DBファイルは以下です。

```text
database/database.sqlite
```

問い合わせ管理用として、主に以下のテーブルを使用しています。

```text
users
inquiries
inquiry_logs
inquiry_comments
```

Laravel標準のMigrationにより、`sessions`、`cache`、`jobs` などのテーブルも作成されています。

※ `database/database.sqlite` は環境ごとに作成するDBファイルのため、Git管理対象外としています。
テーブル構造は Migration、サンプルデータは Seeder で再現できるようにしています。

---

## テーブル設計

### users テーブル

| カラム名       | 内容      |
| ---------- | ------- |
| id         | ユーザーID  |
| name       | ユーザー名   |
| email      | メールアドレス |
| password   | パスワード   |
| role       | 権限      |
| created_at | 作成日時    |
| updated_at | 更新日時    |

### role

| role  | 意味     |
| ----- | ------ |
| admin | 管理者    |
| user  | 一般ユーザー |

---

### inquiries テーブル

| カラム名        | 内容            |
| ----------- | ------------- |
| id          | 問い合わせID       |
| user_id     | 問い合わせ登録ユーザーID |
| assignee_id | 対応担当者ユーザーID   |
| name        | 問い合わせ者名       |
| email       | メールアドレス       |
| title       | 件名            |
| category    | カテゴリ          |
| body        | 問い合わせ内容       |
| status      | ステータス         |
| priority    | 優先度           |
| due_date    | 対応期限          |
| admin_reply | 旧管理者返答用カラム    |
| created_at  | 作成日時          |
| updated_at  | 更新日時          |

※ `admin_reply` は初期実装時の管理者返答用カラムです。
現在はコメントスレッド機能を追加しているため、画面上のやり取りは `inquiry_comments` を使用しています。
将来的には `admin_reply` の削除または補助項目化を検討します。

---

### inquiry_comments テーブル

| カラム名       | 内容           |
| ---------- | ------------ |
| id         | コメントID       |
| inquiry_id | 対象の問い合わせID   |
| user_id    | コメント投稿ユーザーID |
| body       | コメント本文       |
| created_at | 作成日時         |
| updated_at | 更新日時         |

問い合わせ1件に対して複数のコメントを紐づけることで、管理者と一般ユーザーがスレッド形式でやり取りできるようにしています。

---

### inquiry_logs テーブル

| カラム名         | 内容         |
| ------------ | ---------- |
| id           | 履歴ID       |
| inquiry_id   | 対象の問い合わせID |
| user_id      | 更新したユーザーID |
| action       | 操作種別       |
| field_name   | 変更対象項目     |
| before_value | 変更前の値      |
| after_value  | 変更後の値      |
| message      | 履歴表示用メッセージ |
| created_at   | 作成日時       |
| updated_at   | 更新日時       |

---

### ステータス

| ステータス | 意味              |
| ----- | --------------- |
| 未対応   | まだ管理者が対応していない状態 |
| 対応中   | 管理者が確認・対応している状態 |
| 回答済み  | 利用者への返答が完了した状態  |
| クローズ  | 対応完了として終了した状態   |

---

### 優先度

| 優先度 | 意味             |
| --- | -------------- |
| 低   | 急ぎではない問い合わせ    |
| 中   | 通常対応の問い合わせ     |
| 高   | 優先して対応したい問い合わせ |
| 緊急  | 早急な対応が必要な問い合わせ |

---

## ディレクトリ構成

主に使用するファイルは以下です。

```text
inquiry-demo-laravel
├── app
│   ├── Http
│   │   ├── Controllers
│   │   │   ├── AuthController.php
│   │   │   └── InquiryController.php
│   │   └── Middleware
│   │       └── AdminMiddleware.php
│   └── Models
│       ├── Inquiry.php
│       ├── InquiryLog.php
│       ├── InquiryComment.php
│       └── User.php
├── bootstrap
│   └── app.php
├── database
│   ├── database.sqlite
│   ├── migrations
│   │   ├── xxxx_xx_xx_create_inquiries_table.php
│   │   ├── xxxx_xx_xx_create_inquiry_logs_table.php
│   │   ├── xxxx_xx_xx_create_inquiry_comments_table.php
│   │   ├── xxxx_xx_xx_add_user_id_to_inquiry_logs_table.php
│   │   ├── xxxx_xx_xx_add_role_to_users_table.php
│   │   ├── xxxx_xx_xx_add_user_id_to_inquiries_table.php
│   │   └── xxxx_xx_xx_add_management_fields_to_inquiries_table.php
│   └── seeders
│       ├── AdminUserSeeder.php
│       ├── NormalUserSeeder.php
│       ├── DatabaseSeeder.php
│       └── InquirySeeder.php
├── public
│   └── css
│       └── style.css
├── resources
│   └── views
│       ├── layouts
│       │   └── app.blade.php
│       ├── auth
│       │   └── login.blade.php
│       ├── inquiries
│       │   └── create.blade.php
│       ├── my
│       │   └── inquiries
│       │       ├── index.blade.php
│       │       └── show.blade.php
│       └── admin
│           └── inquiries
│               ├── index.blade.php
│               └── show.blade.php
├── routes
│   └── web.php
└── docs
    ├── design.md
    ├── er-diagram.mmd
    ├── laravel-mapping.md
    └── ticket-management-comparison.md
```

---

## 設計資料

本プロジェクトでは、実装内容だけでなく、設計意図やLaravel版への置き換え内容を整理するために、`docs` フォルダ内に設計資料をまとめています。

| ファイル                                   | 内容                                                           |
| -------------------------------------- | ------------------------------------------------------------ |
| `docs/design.md`                       | 画面構成、機能概要、処理フローなどの基本設計                                       |
| `docs/er-diagram.mmd`                  | Mermaid形式のER図                                                |
| `docs/laravel-mapping.md`              | 静的Demo版の機能をLaravel側のController、Model、Blade、DBにどのように対応させたかの整理 |
| `docs/ticket-management-comparison.md` | Redmine、Backlog、Jira などのチケット管理ツールを参考にした機能比較と本Demoへの反映方針      |

これにより、画面実装だけでなく、DB設計・処理構成・Laravelへの移行方針・中規模問い合わせ管理としての拡張方針も確認できるようにしています。

---

## Laravel内の役割

| ファイル                                           | 役割                                    |
| ---------------------------------------------- | ------------------------------------- |
| `routes/web.php`                               | URLとControllerの処理を紐づける                |
| `AuthController.php`                           | ログイン・ログアウト処理を担当                       |
| `InquiryController.php`                        | 登録・一覧・詳細・更新・削除・コメント投稿・一般ユーザー用表示を担当    |
| `AdminMiddleware.php`                          | 管理者画面へのアクセス制御を担当                      |
| `User.php`                                     | `users` テーブルを扱うModel                  |
| `Inquiry.php`                                  | `inquiries` テーブルを扱うModel              |
| `InquiryLog.php`                               | `inquiry_logs` テーブルを扱うModel           |
| `InquiryComment.php`                           | `inquiry_comments` テーブルを扱うModel       |
| `create_inquiries_table.php`                   | `inquiries` テーブルを作成するMigration        |
| `create_inquiry_logs_table.php`                | `inquiry_logs` テーブルを作成するMigration     |
| `create_inquiry_comments_table.php`            | `inquiry_comments` テーブルを作成するMigration |
| `add_management_fields_to_inquiries_table.php` | 担当者・優先度・対応期限を追加するMigration            |
| `AdminUserSeeder.php`                          | 管理者ユーザーを作成するSeeder                    |
| `NormalUserSeeder.php`                         | 一般ユーザーを作成するSeeder                     |
| `InquirySeeder.php`                            | 動作確認用のサンプル問い合わせデータを作成                 |
| `app.blade.php`                                | 共通レイアウト                               |
| `login.blade.php`                              | ログイン画面                                |
| `create.blade.php`                             | 問い合わせフォーム画面                           |
| `admin/inquiries/index.blade.php`              | 管理者用問い合わせ一覧画面                         |
| `admin/inquiries/show.blade.php`               | 管理者用問い合わせ詳細画面                         |
| `my/inquiries/index.blade.php`                 | 一般ユーザー用問い合わせ一覧画面                      |
| `my/inquiries/show.blade.php`                  | 一般ユーザー用問い合わせ詳細画面                      |
| `public/css/style.css`                         | 画面デザイン用CSS                            |

---

## ルーティング

| URL                             |   メソッド | 処理           | Controller                       |
| ------------------------------- | -----: | ------------ | -------------------------------- |
| `/login`                        |    GET | ログイン画面表示     | `AuthController@showLoginForm`   |
| `/login`                        |   POST | ログイン処理       | `AuthController@login`           |
| `/logout`                       |   POST | ログアウト処理      | `AuthController@logout`          |
| `/`                             |    GET | 問い合わせフォーム表示  | `InquiryController@create`       |
| `/inquiries`                    |   POST | 問い合わせ登録      | `InquiryController@store`        |
| `/inquiries/{inquiry}/comments` |   POST | コメント投稿       | `InquiryController@storeComment` |
| `/my/inquiries`                 |    GET | 自分の問い合わせ一覧表示 | `InquiryController@myIndex`      |
| `/my/inquiries/{inquiry}`       |    GET | 自分の問い合わせ詳細表示 | `InquiryController@myShow`       |
| `/admin/inquiries`              |    GET | 管理者一覧表示      | `InquiryController@index`        |
| `/admin/inquiries/{inquiry}`    |    GET | 管理者詳細表示      | `InquiryController@show`         |
| `/admin/inquiries/{inquiry}`    |    PUT | 管理項目更新       | `InquiryController@update`       |
| `/admin/inquiries/{inquiry}`    | DELETE | 問い合わせ削除      | `InquiryController@destroy`      |

---

## ルートごとのアクセス制御

| ルート                             | アクセス権限                    |
| ------------------------------- | ------------------------- |
| `/login`                        | 未ログインでもアクセス可能             |
| `/`                             | ログイン済みユーザー                |
| `/inquiries`                    | ログイン済みユーザー                |
| `/inquiries/{inquiry}/comments` | 管理者、または対象問い合わせを登録した一般ユーザー |
| `/my/*`                         | ログイン済みユーザー                |
| `/admin/*`                      | 管理者のみ                     |

`/admin` 配下のルートには `auth` と `admin` Middleware を適用しています。

```php
Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        // 管理者用ルート
    });
```

これにより、一般ユーザーが `/admin/inquiries` に直接アクセスした場合は 403 になります。

---

## 実装済み機能

### 認証・権限制御

* 管理者ログイン
* 一般ユーザーログイン
* ログアウト
* `role` による管理者・一般ユーザーの分岐
* ログイン後の遷移先分岐
* 管理者Middlewareによる `/admin` 配下のアクセス制御
* 管理者は全問い合わせを確認可能
* 一般ユーザーは自分の問い合わせのみ確認可能
* 他人の問い合わせ詳細へのアクセス制限
* 管理者以外の管理者画面アクセス制限

---

### 一般ユーザー側

* 問い合わせフォーム表示
* ログイン中ユーザーの名前・メールアドレスの初期表示
* 問い合わせ登録
* 問い合わせ登録時の `user_id` 保存
* 自分の問い合わせ一覧表示
* 自分の問い合わせ詳細表示
* ステータス確認
* 担当者確認
* 優先度確認
* 対応期限確認
* コメントスレッド確認
* コメント投稿
* サーバー側バリデーション
* バリデーションメッセージの日本語化
* 登録後、自分の問い合わせ詳細へ遷移

---

### 管理者側

* 全問い合わせ一覧表示
* 問い合わせ詳細表示
* ステータス変更
* 担当者設定
* 優先度設定
* 対応期限設定
* コメントスレッド確認
* コメント投稿
* 問い合わせ削除
* キーワード検索
* ステータス絞り込み
* カテゴリ絞り込み
* 担当者絞り込み
* 優先度絞り込み
* 期限状態絞り込み
* 件数サマリー表示
* ページネーション
* 変更履歴表示
* 変更履歴への更新者表示

---

### コメントスレッド

問い合わせ詳細画面では、管理者と一般ユーザーがコメント形式でやり取りできます。

コメントには以下を記録します。

* 対象問い合わせ
* 投稿者
* コメント本文
* 投稿日時

例：

```text
2026/06/08 14:30　投稿者：一般ユーザー　利用者
追加で確認したいことがあります。

2026/06/08 14:45　投稿者：管理者　運営
確認いたします。
```

これにより、従来の `admin_reply` のような1つの返答欄ではなく、問い合わせ1件に対して複数回のやり取りを残せるようにしています。

---

### 管理項目

中規模の問い合わせ管理を想定し、問い合わせごとに以下の管理項目を持たせています。

* ステータス
* 担当者
* 優先度
* 対応期限

これにより、問い合わせ件数や対応者が増えた場合でも、誰が・どの優先度で・いつまでに対応するかを管理できる構成にしています。

---

### 変更履歴

問い合わせ詳細画面では、管理者による管理項目の変更履歴を表示します。

履歴には以下を記録します。

* 操作日時
* 更新者
* 変更対象項目
* 変更前の値
* 変更後の値
* 表示用メッセージ

記録対象の例：

* ステータス変更
* 担当者変更
* 優先度変更
* 対応期限変更

例：

```text
2026/06/08 15:10　更新者：管理者
ステータスを「未対応」から「対応中」に変更しました。

2026/06/08 15:15　更新者：管理者
優先度を「中」から「高」に変更しました。
```

---

### ステータス管理

* 未対応
* 対応中
* 回答済み
* クローズ

---

### 開発補助

* Seederによるサンプルデータ投入
* SQLiteによるローカルDB管理
* DB Browser for SQLiteでのDB確認
* Git / GitHubによるソースコード管理
* 設計資料の整理
* スターレンタルサーバでの公開検証

---

## 検索・絞り込み機能

管理者一覧画面では、以下の条件で問い合わせを検索できます。

* キーワード
* ステータス
* カテゴリ
* 担当者
* 優先度
* 期限状態

キーワード検索では、以下の項目を対象にしています。

* 件名
* 名前
* メールアドレス
* 問い合わせ本文

期限状態では、以下の条件で絞り込みできます。

| 条件    | 内容                          |
| ----- | --------------------------- |
| 期限切れ  | 対応期限が今日より前で、クローズされていない問い合わせ |
| 今日まで  | 対応期限が今日の問い合わせ               |
| 期限未設定 | 対応期限が設定されていない問い合わせ          |

検索条件はController側で受け取り、DB検索条件として反映しています。

一般ユーザー側は、自分が登録した問い合わせのみを一覧・詳細確認できる構成とし、検索機能は管理者側に限定しています。

---

## 件数サマリー

管理者一覧画面では、問い合わせの状態を把握しやすくするために、件数サマリーを表示しています。

表示対象は以下です。

* 全件
* 未対応
* 対応中
* 回答済み
* クローズ

---

## ページネーション

管理者一覧画面および一般ユーザー用問い合わせ一覧画面では、問い合わせを10件ずつ表示するページネーションを実装しています。

管理者一覧画面では、検索条件を指定した状態でページ移動しても条件が維持されるように、`withQueryString()` を使用しています。

---

## バリデーション

問い合わせ登録時、管理者更新時、コメント投稿時に、Laravelのバリデーション機能を使用しています。

### 問い合わせ登録

主なチェック内容は以下です。

* お名前：必須、文字列、50文字以内
* メールアドレス：必須、メール形式、255文字以内
* 件名：必須、文字列、100文字以内
* カテゴリ：必須、文字列、20文字以内
* 問い合わせ内容：必須、文字列、1000文字以内

### 管理者更新

主なチェック内容は以下です。

* ステータス：必須、指定されたステータスのみ許可
* 担当者：任意、管理者ユーザーのみ指定可能
* 優先度：任意、指定された優先度のみ許可
* 対応期限：任意、日付形式

### コメント投稿

主なチェック内容は以下です。

* コメント本文：必須、文字列、2000文字以内

バリデーションメッセージは `lang/ja/validation.php` により日本語化しています。

---

## Seederについて

開発・動作確認用に以下のSeederを作成しています。

| Seeder             | 内容              |
| ------------------ | --------------- |
| `AdminUserSeeder`  | 管理者ユーザーを作成      |
| `NormalUserSeeder` | 一般ユーザーを作成       |
| `InquirySeeder`    | サンプル問い合わせデータを作成 |

Demo用ログイン情報は以下です。

### 管理者

```text
メールアドレス：admin@example.com
パスワード：password
```

### 一般ユーザー

```text
メールアドレス：user@example.com
パスワード：password
```

Seederを実行することで、ユーザーや問い合わせデータを手入力せずに登録できます。

```bash
php artisan db:seed
```

DBを初期化してサンプルデータを入れ直す場合は、以下を使用します。

```bash
php artisan migrate:fresh --seed
```

---

## セットアップ手順

### 1. 依存パッケージをインストール

```bash
composer install
```

### 2. `.env` を作成

`.env.example` をコピーして `.env` を作成します。

Windows PowerShellの場合：

```bash
copy .env.example .env
```

macOS / Linuxの場合：

```bash
cp .env.example .env
```

### 3. APP_KEYを生成

```bash
php artisan key:generate
```

### 4. SQLiteファイルを作成

Windows PowerShellの場合：

```bash
New-Item -ItemType File database\database.sqlite
```

macOS / Linuxの場合：

```bash
touch database/database.sqlite
```

### 5. `.env` のDB設定を確認

SQLiteを使用するため、`.env` を以下のように設定します。

```env
DB_CONNECTION=sqlite
```

Laravelのバージョンや `.env` の状態によっては、MySQL用の設定が残っている場合があります。
SQLiteを使用する場合は、必要に応じて以下のようなDB接続情報は削除またはコメントアウトします。

```env
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=
```

### 6. アプリケーション言語設定

バリデーションメッセージなどを日本語表示にするため、`.env` を以下のように設定します。

```env
APP_LOCALE=ja
APP_FALLBACK_LOCALE=ja
APP_FAKER_LOCALE=ja_JP
```

### 7. タイムゾーン設定

`.env` に以下を設定します。

```env
APP_TIMEZONE=Asia/Tokyo
```

`config/app.php` の `timezone` は以下のように設定します。

```php
'timezone' => env('APP_TIMEZONE', 'Asia/Tokyo'),
```

設定変更後は以下を実行します。

```bash
php artisan config:clear
php artisan cache:clear
```

### 8. マイグレーション実行

```bash
php artisan migrate
```

サンプルデータも同時に入れる場合は以下を使用します。

```bash
php artisan migrate:fresh --seed
```

### 9. Laravelを起動

```bash
php artisan serve
```

ブラウザで以下を開きます。

```text
http://127.0.0.1:8000
```

---

## 動作確認手順

### 1. ログイン確認

以下のユーザーでログインできることを確認します。

管理者：

```text
admin@example.com
password
```

一般ユーザー：

```text
user@example.com
password
```

管理者でログインした場合は管理者一覧画面へ、一般ユーザーでログインした場合は自分の問い合わせ一覧画面へ遷移することを確認します。

---

### 2. 問い合わせ登録

一般ユーザーでログインし、問い合わせフォームから問い合わせを登録します。

ログイン中ユーザーの名前とメールアドレスは、フォームに初期表示されます。

```text
件名：料金について
カテゴリ：質問
問い合わせ内容：料金プランを教えてください。
```

登録後、自分の問い合わせ詳細画面へ遷移することを確認します。

---

### 3. 一般ユーザー側確認

一般ユーザーで以下を確認します。

* 自分の問い合わせ一覧が表示される
* 一覧でステータス、担当者、優先度、対応期限が確認できる
* 自分の問い合わせ詳細が表示される
* コメントスレッドが表示される
* コメント投稿ができる
* ステータスが確認できる
* 担当者、優先度、対応期限が確認できる
* 他人の問い合わせ詳細にアクセスできない

---

### 4. 管理者一覧確認

管理者で以下を開きます。

```text
http://127.0.0.1:8000/admin/inquiries
```

登録された問い合わせが一覧に表示されることを確認します。

管理者一覧では以下を確認します。

* ステータス
* 担当者
* 優先度
* 対応期限
* 受付日時
* 詳細ボタン
* 削除ボタン

---

### 5. 管理者詳細確認

一覧の「詳細」を押し、問い合わせ詳細画面を確認します。

管理者詳細では以下を確認します。

* 問い合わせ内容
* ステータス
* 担当者
* 優先度
* 対応期限
* コメントスレッド
* 変更履歴

---

### 6. 管理項目更新

詳細画面で以下を設定し、保存します。

```text
ステータス：対応中
担当者：管理者
優先度：高
対応期限：2026/06/15
```

保存後、詳細画面へ戻り、更新内容と変更履歴が表示されることを確認します。

---

### 7. コメント投稿確認

管理者または一般ユーザーで、問い合わせ詳細画面からコメントを投稿します。

確認すること：

* コメントが投稿できる
* 投稿者名が表示される
* 管理者は「運営」として表示される
* 一般ユーザーは「利用者」として表示される
* 管理者側・一般ユーザー側の両方で同じコメントスレッドが確認できる

---

### 8. クローズ確認

詳細画面でステータスを `クローズ` に変更し、保存します。

その後、一覧画面で以下を確認します。

* ステータスが `クローズ` として表示される
* ステータス絞り込みで `クローズ` が検索できる
* 件数サマリーに `クローズ` の件数が反映される

---

### 9. 検索・絞り込み確認

管理者一覧画面で以下を確認します。

* キーワード検索
* ステータス絞り込み
* カテゴリ絞り込み
* 担当者絞り込み
* 優先度絞り込み
* 期限状態絞り込み
* 検索条件リセット
* ページネーション

---

### 10. 権限制御確認

以下を確認します。

* 管理者は `/admin/inquiries` にアクセスできる
* 一般ユーザーは `/admin/inquiries` にアクセスできない
* 一般ユーザーは `/my/inquiries` にアクセスできる
* 一般ユーザーは他人の問い合わせ詳細にアクセスできない

---

### 11. 削除確認

管理者一覧画面の削除ボタンから、対象の問い合わせを削除できることを確認します。

---

## 処理フロー

### ログイン

```text
ログイン画面を開く
↓
POST /login
↓
AuthController@login
↓
メールアドレス・パスワード確認
↓
role を判定
↓
admin は管理者一覧へ
user は自分の問い合わせ一覧へ
```

---

### 問い合わせ登録

```text
ログイン後、問い合わせフォームを開く
↓
POST /inquiries
↓
routes/web.php
↓
InquiryController@store
↓
バリデーション
↓
ログイン中ユーザーIDを user_id に保存
↓
Inquiry Model
↓
inquiries テーブルへ保存
↓
一般ユーザーは自分の問い合わせ詳細へリダイレクト
```

---

### 一般ユーザー一覧表示

```text
GET /my/inquiries
↓
InquiryController@myIndex
↓
ログイン中ユーザーIDを取得
↓
user_id が一致する問い合わせのみ取得
↓
担当者情報を取得
↓
my/inquiries/index.blade.phpへデータを渡す
↓
自分の問い合わせ一覧を表示
```

---

### 一般ユーザー詳細表示

```text
GET /my/inquiries/{inquiry}
↓
InquiryController@myShow
↓
対象問い合わせの user_id を確認
↓
ログイン中ユーザーIDと一致すれば表示
↓
コメント一覧を取得
↓
一致しない場合は 403
```

---

### 管理者一覧表示

```text
GET /admin/inquiries
↓
auth middleware
↓
admin middleware
↓
InquiryController@index
↓
検索条件を取得
↓
Inquiry ModelでDB検索
↓
担当者情報を取得
↓
件数サマリーを取得
↓
admin/inquiries/index.blade.phpへデータを渡す
↓
一覧画面を表示
```

---

### 管理者詳細表示

```text
GET /admin/inquiries/{inquiry}
↓
auth middleware
↓
admin middleware
↓
InquiryController@show
↓
問い合わせ情報を取得
↓
コメント一覧を取得
↓
変更履歴を取得
↓
担当者候補を取得
↓
admin/inquiries/show.blade.phpへデータを渡す
↓
詳細画面を表示
```

---

### 管理項目更新

```text
詳細画面でステータス・担当者・優先度・対応期限を入力
↓
PUT /admin/inquiries/{inquiry}
↓
auth middleware
↓
admin middleware
↓
InquiryController@update
↓
バリデーション
↓
変更前の値を保持
↓
対象問い合わせをDB更新
↓
変更内容を inquiry_logs に保存
↓
詳細画面へリダイレクト
```

---

### コメント投稿

```text
詳細画面でコメントを入力
↓
POST /inquiries/{inquiry}/comments
↓
InquiryController@storeComment
↓
バリデーション
↓
管理者、または対象問い合わせの登録ユーザーか確認
↓
inquiry_comments テーブルへ保存
↓
元の詳細画面へリダイレクト
```

---

### 削除

```text
一覧画面で削除ボタンを押す
↓
DELETE /admin/inquiries/{inquiry}
↓
auth middleware
↓
admin middleware
↓
InquiryController@destroy
↓
対象問い合わせをDBから削除
↓
管理者一覧へリダイレクト
```

---

## 静的Demo版との違い

| 静的Demo版                 | Laravel版                     |
| ----------------------- | ---------------------------- |
| HTML / CSS / JavaScript | Laravel / Blade / Controller |
| localStorageに保存         | DBに保存                        |
| ブラウザ内だけで完結              | Laravelがリクエストを処理             |
| JavaScriptで検索           | ControllerでDB検索              |
| 認証なし                    | 管理者・一般ユーザーログインあり             |
| 権限制御なし                  | role と Middleware による権限制御あり  |
| 返答欄のみ                   | コメントスレッドによる複数回のやり取り          |
| 担当者管理なし                 | 担当者設定あり                      |
| 優先度管理なし                 | 優先度設定あり                      |
| 期限管理なし                  | 対応期限設定あり                     |
| 全データをブラウザ側で管理           | サーバー側で権限に応じて表示制御             |
| GitHub Pagesで公開可能       | PHPとDBが動くサーバーが必要             |

---

## SQLiteとMySQLについて

現在はローカル開発および公開検証を優先し、SQLiteを使用しています。

SQLiteは `database/database.sqlite` という1つのファイルをDBとして扱えるため、環境構築が簡単です。

将来的に実務や本番公開に近い構成を想定する場合は、MySQLへの移行を検討します。

| 段階   | DB     | 目的                   |
| ---- | ------ | -------------------- |
| 初期実装 | SQLite | ローカルでDB保存の流れを確認      |
| 公開検証 | SQLite | 中規模Demoとしてサーバー上で動作確認 |
| 実務想定 | MySQL  | 複数ユーザー利用・本番運用に近い構成   |

---

## Git管理について

本プロジェクトはGitでバージョン管理し、GitHub上でソースコードを管理しています。

以下のような環境依存ファイルや自動生成ファイルはGit管理対象外としています。

```text
.env
/vendor
/node_modules
database/database.sqlite
```

`.env` は環境ごとに作成し、`.env.example` を元に設定します。
`vendor` は `composer install`、`node_modules` は必要に応じて `npm install` で再作成します。
SQLiteのDBファイルは、MigrationとSeederにより再作成できる構成にしています。

---

## スターレンタルサーバ公開手順

本プロジェクトは、スターレンタルサーバ上で公開検証を実施しています。

### サーバー環境確認

SSH接続後、以下を確認しました。

```bash
php -v
composer -V
git --version
pwd
ls
```

通常の `php` コマンドは PHP 8.0 系を参照していたため、Laravel の実行には PHP 8.5.5 を使用しました。

```bash
/opt/php-8.5.5/bin/php -v
```

必要に応じて、SSHセッション内で以下のように alias を設定しました。

```bash
alias php='/opt/php-8.5.5/bin/php'
```

---

### GitHubからclone

```bash
cd /home/ss528111
git clone https://github.com/yuyamitsu/inquiry-demo-laravel.git
cd inquiry-demo-laravel
```

---

### Composer install

```bash
php $(which composer) install --no-dev
```

---

### `.env` 作成

```bash
cp .env.example .env
```

---

### APP_KEY生成

```bash
php artisan key:generate
```

---

### `.env` の本番用設定

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://ss528111.stars.ne.jp
DB_CONNECTION=sqlite
APP_TIMEZONE=Asia/Tokyo
```

---

### SQLiteファイル作成

```bash
touch database/database.sqlite
```

---

### Migration・Seeder実行

```bash
php artisan migrate --seed
```

---

### 権限設定

```bash
chmod -R 775 storage bootstrap/cache
```

---

### public_html への配置

スターサーバーでは、以下のディレクトリが公開対象です。

```text
/home/ss528111/ss528111.stars.ne.jp/public_html
```

Laravel本体は公開ディレクトリの外に置きます。

```text
/home/ss528111/inquiry-demo-laravel
```

Laravelの `public` フォルダの中身のみを `public_html` にコピーします。

```bash
cd /home/ss528111

cp -a ss528111.stars.ne.jp/public_html ss528111.stars.ne.jp/public_html_backup_$(date +%Y%m%d_%H%M%S)

cp -a inquiry-demo-laravel/public/. ss528111.stars.ne.jp/public_html/
```

---

### `public_html/index.php` のパス修正

`public_html/index.php` 内の読み込みパスを、Laravel本体の位置に合わせて修正しました。

修正前：

```php
require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
```

修正後：

```php
require __DIR__.'/../../inquiry-demo-laravel/vendor/autoload.php';

$app = require_once __DIR__.'/../../inquiry-demo-laravel/bootstrap/app.php';
```

---

### ブラウザ確認

以下にアクセスし、Laravelアプリが表示されることを確認しました。

```text
https://ss528111.stars.ne.jp
```

主要機能についても、ブラウザ上で動作確認済みです。

---

## サーバー反映手順

ローカルで修正してGitHubへpushした後、サーバー側では以下を実行します。

```bash
cd /home/ss528111/inquiry-demo-laravel

git pull

alias php='/opt/php-8.5.5/bin/php'

php artisan migrate

php artisan view:clear
php artisan route:clear
php artisan config:clear
```

CSSを変更した場合は、公開ディレクトリ側にも反映します。

```bash
cp /home/ss528111/inquiry-demo-laravel/public/css/style.css /home/ss528111/ss528111.stars.ne.jp/public_html/css/style.css
```

※ `public` フォルダ全体をコピーすると、`public_html/index.php` のパス修正が上書きされる可能性があるため、CSSのみをコピーしています。

---

## 公開時の注意点

Laravelプロジェクト丸ごとを `public_html` に配置しないようにしています。

Webから見える場所には、Laravelの `public` フォルダの中身だけを配置します。

以下のようなファイルやディレクトリは、Web公開領域に置かないようにします。

```text
.env
app/
routes/
database/
storage/
vendor/
composer.json
```

特に `.env` はアプリケーションキーやDB設定などを含むため、公開されない場所に配置する必要があります。

また、公開環境では以下の設定にします。

```env
APP_ENV=production
APP_DEBUG=false
```

---

## 今後の拡張予定

* 一般ユーザー登録画面を作成する
* 優先度や期限切れの表示をさらに強調する
* ダッシュボード機能を追加する
* 期限切れ件数や未対応件数を可視化する
* プロジェクト管理機能を追加する
* グループ管理機能を追加する
* プロジェクトごとの権限制御を追加する
* 添付ファイル機能を追加する
* メール通知機能を検討する
* MySQLへの移行を検討する
* `admin_reply` カラムの整理または削除を検討する

---

## 注意点

現在のLaravel版は、学習用・提案用Demoとして作成しています。

ローカル環境では以下で確認します。

```text
http://127.0.0.1:8000
```

公開検証環境では、スターレンタルサーバ上で動作確認済みです。

Laravelを公開する場合は、GitHub Pagesではなく、PHPとDBが動作するサーバーが必要になります。

また、Laravel本体は公開ディレクトリ外に配置し、`public` ディレクトリの中身のみを公開入口にする構成にしています。

---

## 補足

このLaravel版は、静的Demoで作成した問い合わせ管理フローを、DB保存できるWebアプリ構成に置き換えるためのプロトタイプです。

本番運用を目的としたものではなく、設計・実装の流れ、Laravelの基本構成、DB保存の仕組み、認証、権限制御、コメントスレッド、担当者・優先度・対応期限管理、公開手順を理解するための学習用・提案用Demoとして作成しています。
