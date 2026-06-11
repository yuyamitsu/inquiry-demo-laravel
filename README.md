# 問い合わせ管理Demo Laravel版 / CoreviaDesk

## 概要

Laravelで作成している、問い合わせ管理・チケット管理Demoです。

単なる問い合わせフォームではなく、**問い合わせをチケットとして管理し、対応履歴やコメントを蓄積していく中規模向けの問い合わせ管理Demo**として作成しています。

現在は、問い合わせの登録・一覧・詳細確認に加えて、以下の機能を実装しています。

```text
・管理者 / スタッフ / 一般ユーザーの権限制御
・問い合わせ登録
・一般ユーザーは自分の問い合わせのみ確認
・管理者 / スタッフは全問い合わせを確認
・コメントスレッド
・担当者、優先度、対応期限
・ステータス管理
・変更履歴
・検索、絞り込み
・担当者未設定検索
・自分の担当検索
・ユーザー管理
・ユーザー詳細
・ユーザー自身のパスワード変更
・管理者による仮パスワード再設定
```

今後は、問い合わせ対応で得た情報をナレッジとして蓄積し、再利用できるようにする方向も想定しています。

---

## 作成目的

このプロジェクトは、Laravelを使って、問い合わせ管理システムに必要な画面構成・DB設計・処理フロー・認証・権限制御を整理しながら、段階的に実装することを目的としています。

小規模な問い合わせフォームでは、問い合わせを受け付けて管理者が返答するだけでも成立します。

一方で、中規模の問い合わせ管理では、問い合わせ件数や対応者が増えるため、以下のような管理が必要になります。

```text
・誰が対応するか
・どの問い合わせを優先するか
・いつまでに対応するか
・管理者 / スタッフ / 一般ユーザーで見える範囲を分けられるか
・問い合わせごとのやり取りを残せるか
・変更履歴を追跡できるか
・ユーザーを管理できるか
・パスワード忘れに管理者が対応できるか
```

そのため、本Demoでは問い合わせ1件を「チケット」のように扱い、ステータス、担当者、優先度、対応期限、コメントスレッド、変更履歴、ユーザー管理を持つ構成にしています。

---

## 使用技術

```text
PHP
Laravel
Blade
HTML / CSS
SQLite（ローカル開発）
MySQL / MariaDB（公開環境）
Composer
Git / GitHub
Star Rental Server
```

---

## 環境

### ローカル環境

```text
OS：Windows
DB：SQLite
DBファイル：database/database.sqlite
起動：php artisan serve
```

ローカルではDockerは使用していません。

### 公開環境

```text
サーバー：スターレンタルサーバ
DB：MySQL / MariaDB
Laravel本体：公開ディレクトリ外に配置
公開入口：public_html
```

スターレンタルサーバ上でLaravelアプリとして公開検証済みです。

### メール送信

メール送信機能は実装していません。

以下は現時点では対象外です。

```text
・問い合わせ投稿時のメール通知
・コメント投稿時のメール通知
・メールによるパスワードリセット
```

パスワード忘れ対応は、**管理者による仮パスワード再設定**で対応する方針です。

### Docker

今後、Node.js / Viteビルド等が必要になる場合は、Node.jsを直接サーバーへ入れるのではなく、コンテナ利用も検討します。

---

## 現在のDB構成

主に以下のテーブルを使用しています。

```text
users
inquiries
inquiry_comments
inquiry_logs
```

Laravel標準のMigrationにより、`sessions`、`cache`、`jobs`、`migrations` なども作成されます。

---

## テーブル設計

### users テーブル

| カラム名 | 内容 |
|---|---|
| id | ユーザーID |
| name | ユーザー名 |
| email | メールアドレス |
| password | パスワード |
| role | 権限 |
| remember_token | ログイン保持用トークン |
| created_at | 作成日時 |
| updated_at | 更新日時 |

### role

| role | 意味 | 主な権限 |
|---|---|---|
| admin | 管理者 | 全問い合わせ管理、ユーザー管理、削除、仮パスワード再設定 |
| staff | スタッフ / 担当者 | 全問い合わせ確認、管理項目更新、コメント投稿 |
| user | 一般ユーザー | 問い合わせ登録、自分の問い合わせ確認、コメント投稿 |

---

### inquiries テーブル

| カラム名 | 内容 |
|---|---|
| id | 問い合わせID |
| user_id | 問い合わせ登録ユーザーID |
| assignee_id | 対応担当者ユーザーID |
| name | 問い合わせ者名 |
| email | メールアドレス |
| title | 件名 |
| category | カテゴリ |
| body | 問い合わせ内容 |
| status | ステータス |
| priority | 優先度 |
| due_date | 対応期限 |
| created_at | 作成日時 |
| updated_at | 更新日時 |

`admin_reply` カラムは、コメントスレッドへ移行したため削除済みです。

---

### inquiry_comments テーブル

| カラム名 | 内容 |
|---|---|
| id | コメントID |
| inquiry_id | 対象の問い合わせID |
| user_id | コメント投稿ユーザーID |
| body | コメント本文 |
| created_at | 作成日時 |
| updated_at | 更新日時 |

問い合わせ1件に対して複数コメントを紐づけ、管理者・スタッフ・一般ユーザーがスレッド形式でやり取りできるようにしています。

---

### inquiry_logs テーブル

| カラム名 | 内容 |
|---|---|
| id | 履歴ID |
| inquiry_id | 対象の問い合わせID |
| user_id | 更新したユーザーID |
| action | 操作種別 |
| field_name | 変更対象項目 |
| before_value | 変更前の値 |
| after_value | 変更後の値 |
| message | 履歴表示用メッセージ |
| created_at | 作成日時 |
| updated_at | 更新日時 |

ステータス、担当者、優先度、対応期限の変更を記録します。

---

## ステータス

| ステータス | 意味 |
|---|---|
| 未対応 | まだ対応していない状態 |
| 対応中 | 確認・対応している状態 |
| 回答済み | 利用者への回答が完了した状態 |
| クローズ | 対応完了として終了した状態 |

---

## 優先度

| 優先度 | 意味 |
|---|---|
| 低 | 急ぎではない問い合わせ |
| 中 | 通常対応の問い合わせ |
| 高 | 優先して対応したい問い合わせ |
| 緊急 | 早急な対応が必要な問い合わせ |

---

## 主なディレクトリ構成

```text
app
├── Http
│   ├── Controllers
│   │   ├── Admin
│   │   │   └── UserController.php
│   │   ├── AuthController.php
│   │   ├── InquiryController.php
│   │   └── PasswordController.php
│   └── Middleware
│       └── AdminMiddleware.php
└── Models
    ├── Inquiry.php
    ├── InquiryComment.php
    ├── InquiryLog.php
    └── User.php

resources/views
├── admin
│   ├── inquiries
│   │   ├── index.blade.php
│   │   └── show.blade.php
│   └── users
│       ├── index.blade.php
│       ├── show.blade.php
│       └── password.blade.php
├── auth
│   ├── login.blade.php
│   └── register.blade.php
├── inquiries
│   └── create.blade.php
├── layouts
│   └── app.blade.php
└── my
    ├── inquiries
    │   ├── index.blade.php
    │   └── show.blade.php
    └── password
        └── edit.blade.php

public/css
└── style.css

routes
└── web.php

docs
├── design.md
├── er-diagram.mmd
├── laravel-mapping.md
└── ticket-management-comparison.md
```

---

## 主要Controller

| Controller | 役割 |
|---|---|
| `AuthController` | ログイン、ログアウト、一般ユーザー登録 |
| `InquiryController` | 問い合わせ登録、一覧、詳細、更新、削除、コメント投稿 |
| `PasswordController` | ログイン中ユーザー自身のパスワード変更 |
| `Admin\UserController` | 管理者向けユーザー一覧、ユーザー詳細、仮パスワード再設定 |

---

## 主要Model

| Model | 役割 |
|---|---|
| `User` | ユーザー、権限、問い合わせ登録者、担当者、コメント投稿者 |
| `Inquiry` | 問い合わせ本体 |
| `InquiryComment` | 問い合わせコメント |
| `InquiryLog` | 変更履歴 |

---

## 主なリレーション

### User

```php
public function inquiries()
{
    return $this->hasMany(Inquiry::class);
}

public function assignedInquiries()
{
    return $this->hasMany(Inquiry::class, 'assignee_id');
}

public function inquiryComments()
{
    return $this->hasMany(InquiryComment::class);
}
```

### Inquiry

```php
public function user()
{
    return $this->belongsTo(User::class);
}

public function assignee()
{
    return $this->belongsTo(User::class, 'assignee_id');
}

public function comments()
{
    return $this->hasMany(InquiryComment::class);
}

public function logs()
{
    return $this->hasMany(InquiryLog::class);
}
```

---

## ルーティング概要

### 認証

| URL | メソッド | 処理 |
|---|---:|---|
| `/login` | GET | ログイン画面 |
| `/login` | POST | ログイン処理 |
| `/register` | GET | 一般ユーザー登録画面 |
| `/register` | POST | 一般ユーザー登録処理 |
| `/logout` | POST | ログアウト処理 |

### 共通

| URL | メソッド | 処理 |
|---|---:|---|
| `/` | GET | 問い合わせフォーム |
| `/inquiries` | POST | 問い合わせ登録 |
| `/inquiries/{inquiry}/comments` | POST | コメント投稿 |

### マイページ

| URL | メソッド | 処理 |
|---|---:|---|
| `/my/inquiries` | GET | 自分の問い合わせ一覧 |
| `/my/inquiries/{inquiry}` | GET | 自分の問い合わせ詳細 |
| `/my/password` | GET | 自分のパスワード変更画面 |
| `/my/password` | PUT | 自分のパスワード変更処理 |

### 管理側問い合わせ

| URL | メソッド | 処理 |
|---|---:|---|
| `/admin/inquiries` | GET | 問い合わせ一覧 |
| `/admin/inquiries/{inquiry}` | GET | 問い合わせ詳細 |
| `/admin/inquiries/{inquiry}` | PUT | 管理項目更新 |
| `/admin/inquiries/{inquiry}` | DELETE | 問い合わせ削除 |

### 管理者向けユーザー管理

| URL | メソッド | 処理 |
|---|---:|---|
| `/admin/users` | GET | ユーザー一覧 |
| `/admin/users/{user}` | GET | ユーザー詳細 |
| `/admin/users/{user}/password` | GET | 仮パスワード再設定画面 |
| `/admin/users/{user}/password` | PUT | 仮パスワード再設定処理 |

---

## アクセス制御

| 種別 | できること |
|---|---|
| admin | 全問い合わせ確認、管理項目更新、コメント、削除、ユーザー管理、仮パスワード再設定 |
| staff | 全問い合わせ確認、管理項目更新、コメント、自分の担当検索 |
| user | 問い合わせ登録、自分の問い合わせ確認、コメント、自分のパスワード変更 |

### admin

```text
・/admin/inquiries にアクセス可能
・/admin/users にアクセス可能
・問い合わせ削除可能
・仮パスワード再設定可能
```

### staff

```text
・/admin/inquiries にアクセス可能
・全問い合わせ一覧、詳細を確認可能
・管理項目更新可能
・コメント投稿可能
・削除不可
・/admin/users はアクセス不可
```

### user

```text
・/my/inquiries にアクセス可能
・自分の問い合わせのみ確認可能
・他人の問い合わせ詳細は403
・/admin 配下はアクセス不可
```

---

## 実装済み機能

### 認証・ユーザー登録

```text
・ログイン
・ログアウト
・一般ユーザー登録
・登録後の自動ログイン
・role による遷移先分岐
・ログイン画面 / 登録画面のパスワード表示・非表示切替
```

ログイン後の遷移は以下です。

```text
admin → /admin/inquiries
staff → /admin/inquiries
user  → /my/inquiries
```

---

### 問い合わせ登録

```text
・ログイン済みユーザーのみ登録可能
・登録時に user_id を保存
・name / email はログイン中ユーザー情報を初期表示
・status は 未対応 で保存
```

---

### 一般ユーザー側

```text
・自分の問い合わせ一覧
・自分の問い合わせ詳細
・件名クリックで詳細へ遷移
・ステータス、担当者、優先度、対応期限の確認
・コメントスレッド確認
・コメント投稿
・自分のパスワード変更
```

---

### 管理者 / スタッフ側問い合わせ管理

```text
・全問い合わせ一覧
・問い合わせ詳細
・ステータス変更
・担当者変更
・優先度変更
・対応期限変更
・コメント投稿
・変更履歴表示
・検索、絞り込み
・担当者未設定検索
・自分の担当検索
・件数サマリー
・ページネーション
```

削除はadminのみ可能です。

---

### ユーザー管理

adminのみ利用できます。

```text
・ユーザー一覧
・名前、メールアドレスで検索
・権限別検索
・権限バッジ表示
・問い合わせ件数表示
・担当中件数表示
・名前クリックでユーザー詳細へ遷移
・ユーザー詳細で登録問い合わせを確認
・ユーザー詳細で担当問い合わせを確認
```

---

### パスワード変更

#### ユーザー自身のパスワード変更

URL：

```text
/my/password
```

対象：

```text
admin / staff / user
```

仕様：

```text
・ログイン中ユーザー本人のみ変更
・現在のパスワード確認あり
・新しいパスワード確認入力あり
・8文字以上
・表示 / 非表示切替あり
```

URLにユーザーIDを含まないため、他人のパスワードは変更できません。

#### 管理者による仮パスワード再設定

URL：

```text
/admin/users/{user}/password
```

対象：

```text
adminのみ
```

仕様：

```text
・管理者が対象ユーザーの仮パスワードを再設定
・現在のパスワード確認なし
・新しいパスワード確認入力あり
・8文字以上
・表示 / 非表示切替あり
・メール送信なし
```

再設定後は、管理者が仮パスワードをユーザーへ別途共有する運用です。

---

### コメントスレッド

問い合わせ詳細画面で、管理者・スタッフ・一般ユーザーがコメントを投稿できます。

コメントには以下を記録します。

```text
・問い合わせID
・投稿者ID
・コメント本文
・投稿日時
```

表示ラベル：

```text
admin → 管理者
staff → 担当者
user  → 利用者
```

---

### 管理項目

問い合わせごとに以下を管理します。

```text
・ステータス
・担当者
・優先度
・対応期限
```

担当者候補は以下です。

```text
role = admin
role = staff
```

---

### 変更履歴

管理項目の変更時に履歴を保存します。

記録対象：

```text
・ステータス変更
・担当者変更
・優先度変更
・対応期限変更
```

保存内容：

```text
・更新者
・変更対象項目
・変更前
・変更後
・表示用メッセージ
・変更日時
```

adminだけでなくstaffが変更した場合も、更新者として履歴に残ります。

---

### 検索・絞り込み

管理側一覧画面では、以下の条件で検索できます。

```text
・キーワード
・ステータス
・カテゴリ
・担当者
・担当者未設定
・自分の担当
・優先度
・期限状態
```

キーワード検索対象：

```text
・件名
・名前
・メールアドレス
・問い合わせ本文
```

期限状態：

| 条件 | 内容 |
|---|---|
| 期限切れ | 対応期限が今日より前、かつクローズではない問い合わせ |
| 今日まで | 対応期限が今日の問い合わせ |
| 期限未設定 | 対応期限が設定されていない問い合わせ |

---

### 件数サマリー

管理側問い合わせ一覧画面では、ステータス別件数をカードで表示します。

```text
・全件
・未対応
・対応中
・回答済み
・クローズ
```

サマリーカードはクリック可能で、該当ステータスの絞り込みに遷移します。

---

### 表示改善

```text
・件名クリックで問い合わせ詳細へ遷移
・ユーザー名クリックでユーザー詳細へ遷移
・優先度バッジ
・対応期限バッジ
・権限バッジ
・コメント投稿者ラベル
・長文折り返し対応
・ヘッダーにログインユーザー名表示
・ヘッダーにパスワード変更リンク
・adminのみユーザー管理リンク表示
```

---

## バリデーション

### 問い合わせ登録

```text
name：必須、文字列、50文字以内
email：必須、メール形式、255文字以内
title：必須、文字列、100文字以内
category：必須、文字列、20文字以内
body：必須、文字列、1000文字以内
```

### 管理項目更新

```text
status：必須、未対応 / 対応中 / 回答済み / クローズ
assignee_id：任意、role が admin または staff のユーザーのみ
priority：任意、低 / 中 / 高 / 緊急
due_date：任意、日付形式
```

### コメント投稿

```text
body：必須、文字列、2000文字以内
```

### パスワード

```text
password：必須、8文字以上、確認用と一致
```

---

## Seeder

開発・動作確認用にSeederを用意しています。

| Seeder | 内容 |
|---|---|
| `AdminUserSeeder` | 管理者・スタッフユーザーを作成 |
| `NormalUserSeeder` | 一般ユーザーを作成 |
| `InquirySeeder` | サンプル問い合わせ、コメントを作成 |
| `DatabaseSeeder` | 各Seederを呼び出し |

### Demo用ログイン情報

#### 管理者

```text
メールアドレス：admin@example.com
パスワード：password
```

#### スタッフ

```text
メールアドレス：sato@example.com
パスワード：password
```

```text
メールアドレス：suzuki@example.com
パスワード：password
```

```text
メールアドレス：takahashi@example.com
パスワード：password
```

#### 一般ユーザー

```text
メールアドレス：user@example.com
パスワード：password
```

Seederでは以下の確認ができるサンプルを用意します。

```text
・担当者あり
・担当者未設定
・優先度 低 / 中 / 高 / 緊急
・期限切れ
・今日まで
・期限未設定
・未対応 / 対応中 / 回答済み / クローズ
・コメントスレッド
```

---

## セットアップ手順

### 1. 依存パッケージをインストール

```bash
composer install
```

### 2. `.env` を作成

Windows PowerShell：

```powershell
copy .env.example .env
```

macOS / Linux：

```bash
cp .env.example .env
```

### 3. APP_KEYを生成

```bash
php artisan key:generate
```

### 4. SQLiteファイルを作成

Windows PowerShell：

```powershell
New-Item -ItemType File database\database.sqlite
```

macOS / Linux：

```bash
touch database/database.sqlite
```

### 5. `.env` のDB設定

```env
DB_CONNECTION=sqlite
```

SQLiteを使用する場合は、MySQL用のDB設定が残っていれば削除またはコメントアウトします。

```env
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=
```

### 6. 日本語・タイムゾーン設定

```env
APP_LOCALE=ja
APP_FALLBACK_LOCALE=ja
APP_FAKER_LOCALE=ja_JP
APP_TIMEZONE=Asia/Tokyo
```

`config/app.php`：

```php
'timezone' => env('APP_TIMEZONE', 'Asia/Tokyo'),
```

設定変更後：

```bash
php artisan config:clear
php artisan cache:clear
```

### 7. Migration / Seeder 実行

```bash
php artisan migrate:fresh --seed
```

既存データを残す場合：

```bash
php artisan migrate
php artisan db:seed
```

### 8. 起動

```bash
php artisan serve
```

ブラウザで以下を開きます。

```text
http://127.0.0.1:8000
```

---

## 動作確認手順

### 1. ログイン

```text
admin@example.com / password
sato@example.com / password
user@example.com / password
```

確認内容：

```text
admin → /admin/inquiries
staff → /admin/inquiries
user  → /my/inquiries
```

### 2. 問い合わせ登録

一般ユーザーでログインし、問い合わせを登録します。

登録後、自分の問い合わせ詳細へ遷移することを確認します。

### 3. 管理側問い合わせ一覧

adminまたはstaffで以下を開きます。

```text
/admin/inquiries
```

確認内容：

```text
・全問い合わせが表示される
・検索、絞り込みができる
・自分の担当で絞り込みできる
・件名クリックで詳細へ遷移できる
・staffには削除ボタンが表示されない
```

### 4. 管理側問い合わせ詳細

確認内容：

```text
・問い合わせ内容が表示される
・管理項目を更新できる
・コメント投稿できる
・変更履歴が表示される
・staffが更新した場合も履歴にstaff名が残る
```

### 5. ユーザー管理

adminで以下を開きます。

```text
/admin/users
```

確認内容：

```text
・ユーザー一覧が表示される
・名前、メールアドレスで検索できる
・権限で絞り込みできる
・名前クリックでユーザー詳細へ遷移できる
・登録問い合わせ件数、担当中件数が表示される
```

staffまたはuserで `/admin/users` に直接アクセスすると403になることを確認します。

### 6. 仮パスワード再設定

adminでユーザー詳細から仮パスワード再設定画面へ遷移します。

確認内容：

```text
・仮パスワードを設定できる
・確認用と一致しないとエラーになる
・8文字未満だとエラーになる
・再設定後、新しいパスワードでログインできる
・古いパスワードではログインできない
・staff / user は直接URLを入力しても403になる
```

### 7. 自分のパスワード変更

```text
/my/password
```

確認内容：

```text
・現在のパスワードが違うとエラーになる
・新しいパスワードに変更できる
・ログアウト後、新しいパスワードでログインできる
・古いパスワードではログインできない
```

---

## 処理フロー

### ログイン

```text
ログイン画面
↓
POST /login
↓
AuthController@login
↓
認証
↓
role 判定
↓
admin / staff は管理側一覧へ
user は自分の問い合わせ一覧へ
```

### 問い合わせ登録

```text
問い合わせフォーム
↓
POST /inquiries
↓
InquiryController@store
↓
バリデーション
↓
user_id にログイン中ユーザーIDを保存
↓
inquiries に保存
↓
admin / staff は管理側一覧へ
user は自分の問い合わせ詳細へ
```

### 管理側一覧

```text
GET /admin/inquiries
↓
auth middleware
↓
admin middleware（admin / staff 許可）
↓
InquiryController@index
↓
検索条件を取得
↓
DB検索
↓
件数サマリー取得
↓
admin/inquiries/index.blade.php
```

### 管理項目更新

```text
PUT /admin/inquiries/{inquiry}
↓
InquiryController@update
↓
admin / staff か確認
↓
バリデーション
↓
変更前の値を保持
↓
inquiries 更新
↓
inquiry_logs に履歴保存
↓
詳細画面へ戻る
```

### ユーザー管理

```text
GET /admin/users
↓
Admin\UserController@index
↓
admin のみ許可
↓
ユーザー検索
↓
問い合わせ件数 / 担当件数取得
↓
admin/users/index.blade.php
```

### 仮パスワード再設定

```text
GET /admin/users/{user}/password
↓
Admin\UserController@editPassword
↓
admin のみ許可
↓
再設定画面表示

PUT /admin/users/{user}/password
↓
Admin\UserController@updatePassword
↓
admin のみ許可
↓
バリデーション
↓
対象ユーザーの password 更新
↓
ユーザー詳細へ戻る
```

---

## 静的Demo版との違い

| 静的Demo版 | Laravel版 |
|---|---|
| HTML / CSS / JavaScript | Laravel / Blade / Controller |
| localStorageに保存 | DBに保存 |
| 認証なし | ログインあり |
| 権限制御なし | admin / staff / user の権限制御あり |
| 返答欄のみ | コメントスレッド |
| 担当者管理なし | 担当者設定あり |
| 優先度管理なし | 優先度設定あり |
| 期限管理なし | 対応期限設定あり |
| 変更履歴なし | 管理項目変更履歴あり |
| ユーザー管理なし | 管理者向けユーザー管理あり |
| パスワード管理なし | 本人変更・管理者再設定あり |
| GitHub Pagesで公開可能 | PHPとDBが動くサーバーが必要 |

---

## Git管理について

以下はGit管理対象外です。

```text
.env
/vendor
/node_modules
database/database.sqlite
```

`.env` は環境ごとに作成し、`.env.example` を元に設定します。

SQLiteのDBファイルは、MigrationとSeederにより再作成できる構成にしています。

---

## スターレンタルサーバ公開手順

本プロジェクトは、スターレンタルサーバ上で公開検証を実施しています。

### PHP確認

通常の `php` コマンドが古いバージョンを参照する場合があるため、Laravel実行時はサーバー上の新しいPHPを使用します。

```bash
/opt/php-8.5.5/bin/php -v
```

必要に応じてaliasを設定します。

```bash
alias php='/opt/php-8.5.5/bin/php'
```

### GitHubからclone

```bash
cd /home/ss528111
git clone https://github.com/yuyamitsu/inquiry-demo-laravel.git
cd inquiry-demo-laravel
```

### Composer install

```bash
php $(which composer) install --no-dev
```

### `.env` 作成

```bash
cp .env.example .env
```

### APP_KEY生成

```bash
php artisan key:generate
```

### `.env` 本番用設定例

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://ss528111.stars.ne.jp
APP_TIMEZONE=Asia/Tokyo

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=ss528111_inquiry
DB_USERNAME=MySQLユーザー名
DB_PASSWORD=MySQLパスワード
```

### Migration

```bash
php artisan migrate
```

初回構築やDBを入れ直す場合：

```bash
php artisan migrate:fresh --seed
```

### 権限設定

```bash
chmod -R 775 storage bootstrap/cache
```

### public_html 配置

Laravel本体は公開ディレクトリ外に置き、`public` フォルダの中身だけを `public_html` に配置します。

```text
Laravel本体：
/home/ss528111/inquiry-demo-laravel

公開入口：
/home/ss528111/ss528111.stars.ne.jp/public_html
```

`public_html/index.php` の読み込みパスをLaravel本体に合わせて修正します。

```php
require __DIR__.'/../../inquiry-demo-laravel/vendor/autoload.php';

$app = require_once __DIR__.'/../../inquiry-demo-laravel/bootstrap/app.php';
```

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

公開環境で `migrate:fresh --seed` を実行すると既存データが削除されるため、実行前に確認します。

---

## 今後の拡張予定

```text
・問い合わせからナレッジ化する機能
・FAQ / ナレッジ一覧
・問い合わせ詳細からナレッジ登録
・プロジェクト管理
・グループ管理
・プロジェクトごとの権限制御
・親チケット / 子チケット
・添付ファイル
・ダッシュボード
・期限切れ件数や未対応件数の可視化
```

---

## 今後の公開環境検討

現在の公開検証はスターレンタルサーバで実施しています。

今後、Demo用サーバーとしてRocky Linux 10環境を使う場合は、ApacheまたはNginxのVirtualHostで複数アプリを切り分ける構成を検討します。

Node.jsが必要な場合は、サーバーへ直接導入せず、コンテナでビルド環境を用意する方針も検討します。

---

## 注意点

本プロジェクトは学習用・提案用Demoです。

本番運用を目的としたものではなく、Laravelの基本構成、DB保存、認証、権限制御、コメントスレッド、担当者管理、変更履歴、ユーザー管理、パスワード再設定、公開手順を理解するためのDemoとして作成しています。

Laravelを公開する場合は、GitHub Pagesではなく、PHPとDBが動作するサーバーが必要です。

また、Laravel本体は公開ディレクトリ外に配置し、`public` ディレクトリの中身のみを公開入口にする構成にしています。
