# 問い合わせ管理Demo Laravel版 / CoreviaDesk

## 概要

Laravelで作成している、問い合わせ管理・チケット管理Demoです。

単なる問い合わせフォームではなく、**問い合わせをチケットとして管理し、対応履歴・コメント・ナレッジを蓄積していく中規模向けの問い合わせ管理Demo**として作成しています。

問い合わせ1件を「チケット」として扱い、管理者・スタッフ・一般ユーザーの権限制御、担当者管理、優先度管理、対応期限、コメントスレッド、変更履歴、ユーザー管理、ナレッジ管理まで含めた構成にしています。

現在は、主に以下の機能を実装しています。

```text
・管理者 / スタッフ / 一般ユーザーの権限制御
・ログイン / ログアウト
・一般ユーザー登録
・問い合わせ登録
・一般ユーザーは自分の問い合わせのみ確認
・管理者 / スタッフは全問い合わせを確認
・コメントスレッド
・担当者、優先度、対応期限の管理
・ステータス管理
・変更履歴
・検索、絞り込み
・担当者未設定検索
・自分の担当検索
・ユーザー管理
・管理者によるユーザー新規作成
・ユーザー詳細
・ユーザー自身のパスワード変更
・管理者による仮パスワード再設定
・問い合わせからナレッジ記事を作成
・ナレッジ一覧、詳細、編集
・ナレッジの公開 / 下書き管理
・ヘッダーのユーザーメニュー
・パンくずメニュー
```

ナレッジ記事は、元問い合わせ、作成者、タイトル、カテゴリ、本文、公開状態を持ち、管理者・スタッフが一覧、詳細、編集画面から管理できる構成にしています。

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
・対応内容をナレッジとして再利用できるか
・ユーザーを管理できるか
・パスワード忘れに管理者が対応できるか
```

そのため、本Demoでは問い合わせ1件を「チケット」のように扱い、ステータス、担当者、優先度、対応期限、コメントスレッド、変更履歴、ユーザー管理、ナレッジ管理を持つ構成にしています。

---

## システム名

```text
CoreviaDesk
```

問い合わせ対応、チケット管理、ナレッジ蓄積を行う社内向け業務デスクのイメージで命名しています。

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

メール送信機能は現時点では実装していません。

以下は現時点では対象外です。

```text
・問い合わせ投稿時のメール通知
・コメント投稿時のメール通知
・メールによるパスワードリセット
```

パスワード忘れ対応は、**管理者による仮パスワード再設定**で対応する方針です。

### Docker

現在のローカル開発ではDockerは使用していません。

今後、Node.js / Viteビルド等が必要になる場合は、Node.jsを直接サーバーへ入れるのではなく、コンテナ利用も検討します。

---

## 現在のDB構成

主に以下のテーブルを使用しています。

```text
users
inquiries
inquiry_comments
inquiry_logs
knowledge_articles
```

Laravel標準のMigrationにより、`sessions`、`cache`、`jobs`、`migrations` なども作成されます。

---

## テーブル設計

### users テーブル

ログインユーザーを管理するテーブルです。

管理者、スタッフ、一般ユーザーは `role` カラムで区別します。

| カラム名 | 内容 |
|---|---|
| id | ユーザーID |
| name | ユーザー名 |
| email | メールアドレス |
| email_verified_at | メール認証日時 |
| password | パスワード |
| role | 権限 |
| remember_token | ログイン保持用トークン |
| created_at | 作成日時 |
| updated_at | 更新日時 |

### role

| role | 意味 | 主な権限 |
|---|---|---|
| admin | 管理者 | 全問い合わせ管理、ユーザー管理、ユーザー作成、削除、仮パスワード再設定、ナレッジ管理 |
| staff | スタッフ / 担当者 | 全問い合わせ確認、管理項目更新、コメント投稿、ナレッジ管理 |
| user | 一般ユーザー | 問い合わせ登録、自分の問い合わせ確認、コメント投稿、自分のパスワード変更 |

---

### inquiries テーブル

問い合わせ本体を管理するテーブルです。

問い合わせ登録者、担当者、ステータス、優先度、対応期限などを保持します。

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

問い合わせに対するコメントを管理するテーブルです。

管理者、スタッフ、一般ユーザーが同じ問い合わせに対してスレッド形式でやり取りできます。

| カラム名 | 内容 |
|---|---|
| id | コメントID |
| inquiry_id | 対象の問い合わせID |
| user_id | コメント投稿ユーザーID |
| body | コメント本文 |
| created_at | 作成日時 |
| updated_at | 更新日時 |

問い合わせ1件に対して複数コメントを紐づけます。

---

### inquiry_logs テーブル

問い合わせの管理項目変更履歴を管理するテーブルです。

ステータス、担当者、優先度、対応期限の変更を記録します。

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

---

### knowledge_articles テーブル

問い合わせ対応で得た内容をナレッジ記事として保存するテーブルです。

問い合わせ詳細画面からナレッジ化することで、問い合わせ内容とコメント履歴をもとに記事を作成できます。

| カラム名 | 内容 |
|---|---|
| id | ナレッジID |
| inquiry_id | 元になった問い合わせID |
| created_by | ナレッジ作成ユーザーID |
| title | ナレッジタイトル |
| category | カテゴリ |
| body | ナレッジ本文 |
| is_published | 公開状態 |
| created_at | 作成日時 |
| updated_at | 更新日時 |

`is_published` により、公開 / 下書きを管理します。

---

## ER図

最新のER図は `docs` ディレクトリに配置する想定です。

```text
docs/er-diagram.mmd
docs/er-diagram.drawio
```

業務上の主要テーブルは以下です。

```text
users
inquiries
inquiry_comments
inquiry_logs
knowledge_articles
```

主な関係は以下です。

| 関係 | 内容 |
|---|---|
| users 1 - 多 inquiries | ユーザーは複数の問い合わせを登録できる |
| users 1 - 多 inquiries | 管理者 / スタッフは複数の問い合わせを担当できる |
| inquiries 1 - 多 inquiry_comments | 問い合わせ1件に複数コメントを持てる |
| users 1 - 多 inquiry_comments | ユーザーは複数コメントを投稿できる |
| inquiries 1 - 多 inquiry_logs | 問い合わせ1件に複数の変更履歴を持てる |
| users 1 - 多 inquiry_logs | ユーザーは複数の変更操作を行える |
| inquiries 1 - 0..1 knowledge_articles | 問い合わせ1件から0件または1件のナレッジ記事を作成できる |
| users 1 - 多 knowledge_articles | ユーザーは複数のナレッジ記事を作成できる |

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
│   │   │   ├── KnowledgeArticleController.php
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
    ├── KnowledgeArticle.php
    └── User.php

resources/views
├── admin
│   ├── inquiries
│   │   ├── index.blade.php
│   │   └── show.blade.php
│   ├── knowledge
│   │   ├── create.blade.php
│   │   ├── edit.blade.php
│   │   ├── index.blade.php
│   │   └── show.blade.php
│   └── users
│       ├── create.blade.php
│       ├── index.blade.php
│       ├── password.blade.php
│       └── show.blade.php
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
├── er-diagram.drawio
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
| `Admin\UserController` | 管理者向けユーザー一覧、ユーザー作成、ユーザー詳細、仮パスワード再設定 |
| `Admin\KnowledgeArticleController` | ナレッジ一覧、作成、詳細、編集、更新 |

---

## 主要Model

| Model | 役割 |
|---|---|
| `User` | ユーザー、権限、問い合わせ登録者、担当者、コメント投稿者、ナレッジ作成者 |
| `Inquiry` | 問い合わせ本体 |
| `InquiryComment` | 問い合わせコメント |
| `InquiryLog` | 変更履歴 |
| `KnowledgeArticle` | ナレッジ記事 |

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

public function knowledgeArticles()
{
    return $this->hasMany(KnowledgeArticle::class, 'created_by');
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

public function knowledgeArticle()
{
    return $this->hasOne(KnowledgeArticle::class);
}
```

### InquiryComment

```php
public function inquiry()
{
    return $this->belongsTo(Inquiry::class);
}

public function user()
{
    return $this->belongsTo(User::class);
}
```

### InquiryLog

```php
public function inquiry()
{
    return $this->belongsTo(Inquiry::class);
}

public function user()
{
    return $this->belongsTo(User::class);
}
```

### KnowledgeArticle

```php
public function inquiry()
{
    return $this->belongsTo(Inquiry::class);
}

public function creator()
{
    return $this->belongsTo(User::class, 'created_by');
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
| `/admin/inquiries/{inquiry}/knowledge/create` | GET | 問い合わせからナレッジ作成画面 |

### ナレッジ管理

| URL | メソッド | 処理 |
|---|---:|---|
| `/admin/knowledge` | GET | ナレッジ一覧 |
| `/admin/knowledge` | POST | ナレッジ作成 |
| `/admin/knowledge/{knowledgeArticle}` | GET | ナレッジ詳細 |
| `/admin/knowledge/{knowledgeArticle}/edit` | GET | ナレッジ編集画面 |
| `/admin/knowledge/{knowledgeArticle}` | PUT | ナレッジ更新 |

### 管理者向けユーザー管理

| URL | メソッド | 処理 |
|---|---:|---|
| `/admin/users` | GET | ユーザー一覧 |
| `/admin/users/create` | GET | ユーザー作成画面 |
| `/admin/users` | POST | ユーザー作成処理 |
| `/admin/users/{user}` | GET | ユーザー詳細 |
| `/admin/users/{user}/password` | GET | 仮パスワード再設定画面 |
| `/admin/users/{user}/password` | PUT | 仮パスワード再設定処理 |

---

## アクセス制御

| 種別 | できること |
|---|---|
| admin | 全問い合わせ確認、管理項目更新、コメント、削除、ユーザー管理、ユーザー作成、仮パスワード再設定、ナレッジ管理 |
| staff | 全問い合わせ確認、管理項目更新、コメント、自分の担当検索、ナレッジ管理 |
| user | 問い合わせ登録、自分の問い合わせ確認、コメント、自分のパスワード変更 |

### admin

```text
・/admin/inquiries にアクセス可能
・/admin/users にアクセス可能
・/admin/users/create にアクセス可能
・/admin/knowledge にアクセス可能
・問い合わせ削除可能
・ユーザー作成可能
・仮パスワード再設定可能
・ナレッジ作成、編集可能
```

### staff

```text
・/admin/inquiries にアクセス可能
・/admin/knowledge にアクセス可能
・全問い合わせ一覧、詳細を確認可能
・管理項目更新可能
・コメント投稿可能
・ナレッジ作成、編集可能
・削除不可
・/admin/users はアクセス不可
・/admin/users/create はアクセス不可
```

### user

```text
・/my/inquiries にアクセス可能
・自分の問い合わせのみ確認可能
・他人の問い合わせ詳細は403
・自分の問い合わせにコメント投稿可能
・自分のパスワード変更可能
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

一般ユーザー登録画面では、登録されるユーザーの `role` は必ず `user` になります。

管理者やスタッフを作成する場合は、管理者向けユーザー作成画面から作成します。

---

### 問い合わせ登録

```text
・ログイン済みユーザーのみ登録可能
・登録時に user_id を保存
・name / email はログイン中ユーザー情報を初期表示
・status は 未対応 で保存
```

登録後の遷移は以下です。

```text
admin / staff → 管理側問い合わせ一覧
user          → 自分の問い合わせ詳細
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
・ユーザー新規作成
・名前、メールアドレスで検索
・権限別検索
・権限バッジ表示
・問い合わせ件数表示
・担当中件数表示
・名前クリックでユーザー詳細へ遷移
・ユーザー詳細で登録問い合わせを確認
・ユーザー詳細で担当問い合わせを確認
・仮パスワード再設定
```

#### 管理者によるユーザー新規作成

URL：

```text
/admin/users/create
```

対象：

```text
adminのみ
```

作成時に入力する項目：

```text
・名前
・メールアドレス
・権限 admin / staff / user
・仮パスワード
・仮パスワード確認
```

仕様：

```text
・管理者のみ作成可能
・staff / user は直接URLを入力しても403
・メールアドレスの重複不可
・仮パスワードは8文字以上
・確認用パスワードと一致が必要
・作成後はユーザー詳細画面へ遷移
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

### ナレッジ管理

問い合わせ対応で得た内容をナレッジ記事として保存できます。

管理者・スタッフが利用できます。

```text
・問い合わせ詳細画面からナレッジ作成
・問い合わせ本文とコメント履歴をもとに本文を初期生成
・タイトル、カテゴリ、本文を編集可能
・公開 / 下書き管理
・ナレッジ一覧
・ナレッジ詳細
・ナレッジ編集
・キーワード検索
・カテゴリ絞り込み
・公開状態絞り込み
・元問い合わせへのリンク
・作成者の表示
```

#### 問い合わせからナレッジ化

URL：

```text
/admin/inquiries/{inquiry}/knowledge/create
```

仕様：

```text
・問い合わせ詳細画面から作成
・元問い合わせIDを保持
・問い合わせ本文をナレッジ本文に初期反映
・コメント履歴をナレッジ本文に初期反映
・最終的な解決方法や補足を編集して保存
・すでにナレッジ化済みの問い合わせは、既存ナレッジ詳細へ遷移
```

#### ナレッジ一覧

URL：

```text
/admin/knowledge
```

検索条件：

```text
・キーワード
・カテゴリ
・公開状態
```

#### 公開状態

| 状態 | 内容 |
|---|---|
| 公開 | ナレッジとして公開する記事 |
| 下書き | 作成途中、またはまだ公開しない記事 |

---

### 検索・絞り込み

管理側問い合わせ一覧画面では、以下の条件で検索できます。

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
・ナレッジタイトルクリックでナレッジ詳細へ遷移
・優先度バッジ
・対応期限バッジ
・権限バッジ
・公開 / 下書きバッジ
・コメント投稿者ラベル
・長文折り返し対応
・ヘッダーにログインユーザー名表示
・ヘッダーをユーザーメニュー形式に変更
・ユーザーメニュー内にナレッジ、ユーザー管理、パスワード変更、ログアウトを配置
・adminのみユーザー管理リンク表示
・admin / staff のみナレッジリンク表示
・パンくずメニュー
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

### ユーザー作成

```text
name：必須、文字列、50文字以内
email：必須、メール形式、255文字以内、重複不可
role：必須、admin / staff / user
password：必須、8文字以上、確認用と一致
```

### パスワード

```text
password：必須、8文字以上、確認用と一致
```

### ナレッジ記事

```text
title：必須、文字列、100文字以内
category：任意、文字列、50文字以内
body：必須、文字列、5000文字以内
is_published：任意、boolean
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

---

### 2. 問い合わせ登録

一般ユーザーでログインし、問い合わせを登録します。

確認内容：

```text
・問い合わせ登録画面を開ける
・名前、メールアドレスが初期表示される
・問い合わせを登録できる
・登録後、自分の問い合わせ詳細へ遷移する
```

---

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
・担当者未設定で絞り込みできる
・件名クリックで詳細へ遷移できる
・staffには削除ボタンが表示されない
```

---

### 4. 管理側問い合わせ詳細

確認内容：

```text
・問い合わせ内容が表示される
・管理項目を更新できる
・コメント投稿できる
・変更履歴が表示される
・staffが更新した場合も履歴にstaff名が残る
・問い合わせからナレッジ作成画面へ遷移できる
```

---

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
・新規ユーザー作成画面へ遷移できる
```

staffまたはuserで `/admin/users` に直接アクセスすると403になることを確認します。

---

### 6. 管理者によるユーザー新規作成

adminで以下を開きます。

```text
/admin/users/create
```

確認内容：

```text
・ユーザー作成画面が表示される
・名前、メールアドレス、権限、仮パスワードを入力できる
・admin / staff / user を選んで作成できる
・作成後、ユーザー詳細画面へ遷移する
・既存メールアドレスでは登録できない
・確認用パスワードが一致しないとエラーになる
・8文字未満だとエラーになる
```

作成したユーザーでログインできることも確認します。

---

### 7. 仮パスワード再設定

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

---

### 8. 自分のパスワード変更

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

### 9. ナレッジ作成

adminまたはstaffで問い合わせ詳細を開きます。

```text
/admin/inquiries/{inquiry}
```

確認内容：

```text
・ナレッジ作成ボタンが表示される
・問い合わせ内容とコメント履歴が本文に初期反映される
・タイトル、カテゴリ、本文、公開状態を編集できる
・公開または下書きとして保存できる
・作成後、ナレッジ詳細画面へ遷移する
・元問い合わせへのリンクが表示される
```

---

### 10. ナレッジ一覧・詳細・編集

adminまたはstaffで以下を開きます。

```text
/admin/knowledge
```

確認内容：

```text
・ナレッジ一覧が表示される
・キーワード検索ができる
・カテゴリで絞り込みできる
・公開状態で絞り込みできる
・タイトルクリックで詳細へ遷移できる
・詳細画面から編集画面へ遷移できる
・編集後、詳細画面へ戻る
・元問い合わせへ戻れる
```

---

### 11. 権限確認

| 操作 | admin | staff | user |
|---|---:|---:|---:|
| 管理側問い合わせ一覧 | ○ | ○ | × |
| 問い合わせ削除 | ○ | × | × |
| ユーザー管理 | ○ | × | × |
| ユーザー作成 | ○ | × | × |
| 仮パスワード再設定 | ○ | × | × |
| ナレッジ管理 | ○ | ○ | × |
| 自分の問い合わせ確認 | ○ | ○ | ○ |
| 自分のパスワード変更 | ○ | ○ | ○ |

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

---

### 一般ユーザー登録

```text
ユーザー登録画面
↓
POST /register
↓
AuthController@register
↓
バリデーション
↓
role = user で users に保存
↓
自動ログイン
↓
自分の問い合わせ一覧へ
```

---

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
status = 未対応 で inquiries に保存
↓
admin / staff は管理側一覧へ
user は自分の問い合わせ詳細へ
```

---

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

---

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

---

### コメント投稿

```text
POST /inquiries/{inquiry}/comments
↓
InquiryController@storeComment
↓
ログインユーザーが投稿可能か確認
↓
バリデーション
↓
inquiry_comments に保存
↓
直前の詳細画面へ戻る
```

---

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

---

### 管理者によるユーザー新規作成

```text
GET /admin/users/create
↓
Admin\UserController@create
↓
admin のみ許可
↓
ユーザー作成画面表示

POST /admin/users
↓
Admin\UserController@store
↓
admin のみ許可
↓
バリデーション
↓
password を Hash 化
↓
users に保存
↓
ユーザー詳細画面へ遷移
```

---

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

### ナレッジ作成

```text
問い合わせ詳細
↓
GET /admin/inquiries/{inquiry}/knowledge/create
↓
Admin\KnowledgeArticleController@createFromInquiry
↓
admin / staff か確認
↓
問い合わせ本文とコメント履歴を取得
↓
ナレッジ作成画面表示

POST /admin/knowledge
↓
Admin\KnowledgeArticleController@store
↓
バリデーション
↓
knowledge_articles に保存
↓
ナレッジ詳細画面へ遷移
```

---

### ナレッジ編集

```text
GET /admin/knowledge/{knowledgeArticle}/edit
↓
Admin\KnowledgeArticleController@edit
↓
ナレッジ編集画面表示

PUT /admin/knowledge/{knowledgeArticle}
↓
Admin\KnowledgeArticleController@update
↓
バリデーション
↓
knowledge_articles を更新
↓
ナレッジ詳細画面へ戻る
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
| ユーザー作成なし | 管理者によるユーザー作成あり |
| パスワード管理なし | 本人変更・管理者再設定あり |
| ナレッジ管理なし | 問い合わせからナレッジ化できる |
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

公開環境で `migrate:fresh --seed` を実行すると既存データが削除されるため、実行前に確認します。

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

現在の機能をベースに、今後は以下の拡張を検討します。

```text
・ナレッジの一般ユーザー向け公開画面
・FAQ表示
・ナレッジの検索性向上
・プロジェクト管理
・グループ管理
・プロジェクトごとの権限制御
・親チケット / 子チケット
・添付ファイル
・ダッシュボード
・期限切れ件数や未対応件数の可視化
・メール通知
・メールによるパスワードリセット
```

---

## 今後の公開環境検討

現在の公開検証はスターレンタルサーバで実施しています。

今後、Demo用サーバーとしてRocky Linux 10環境を使う場合は、ApacheまたはNginxのVirtualHostで複数アプリを切り分ける構成を検討します。

Node.jsが必要な場合は、サーバーへ直接導入せず、コンテナでビルド環境を用意する方針も検討します。

---

## 注意点

本プロジェクトは学習用・提案用Demoです。

本番運用を目的としたものではなく、Laravelの基本構成、DB保存、認証、権限制御、コメントスレッド、担当者管理、変更履歴、ユーザー管理、パスワード再設定、ナレッジ管理、公開手順を理解するためのDemoとして作成しています。

Laravelを公開する場合は、GitHub Pagesではなく、PHPとDBが動作するサーバーが必要です。

また、Laravel本体は公開ディレクトリ外に配置し、`public` ディレクトリの中身のみを公開入口にする構成にしています。
