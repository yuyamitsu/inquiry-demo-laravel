# 問い合わせ管理Demo Laravel版

## 概要

問い合わせ管理DemoサイトのLaravel版です。

静的Demo版では、HTML / CSS / JavaScript / localStorage を使用して、問い合わせの登録・一覧表示・詳細表示・ステータス変更・返答保存などを実装しました。

本プロジェクトでは、それらの機能をLaravel + DB構成に置き換え、問い合わせデータをサーバー側で管理できる形に拡張しています。

現在はローカル開発環境で扱いやすいSQLiteを使用しています。
実運用や実務に近い構成を想定する段階では、MySQLへの移行も検討します。

---

## 作成目的

このプロジェクトは、単に画面や機能を作るだけではなく、問い合わせ管理システムとして必要な画面構成・DB設計・処理フローを整理しながら、段階的に実装することを目的としています。

静的Demoで作成した以下の流れを、Laravel側で再現・拡張しています。

```text
問い合わせ受付
↓
問い合わせ登録
↓
DB保存
↓
管理者一覧
↓
問い合わせ詳細確認
↓
ステータス更新
↓
管理者返答
↓
検索・絞り込み
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

---

## 現在のDB構成

現在はSQLiteを使用しています。

DBファイルは以下です。

```text
database/database.sqlite
```

問い合わせ管理用として、主に以下のテーブルを使用しています。

```text
inquiries
```

Laravel標準のMigrationにより、`users`、`sessions`、`cache`、`jobs` などのテーブルも作成されていますが、問い合わせ管理機能として独自に追加した主なテーブルは `inquiries` です。

---

## テーブル設計

### inquiries テーブル

| カラム名        | 内容      |
| ----------- | ------- |
| id          | 問い合わせID |
| name        | 問い合わせ者名 |
| email       | メールアドレス |
| title       | 件名      |
| category    | カテゴリ    |
| body        | 問い合わせ内容 |
| status      | ステータス   |
| admin_reply | 管理者返答   |
| created_at  | 作成日時    |
| updated_at  | 更新日時    |

### ステータス

| ステータス | 意味              |
| ----- | --------------- |
| 未対応   | まだ管理者が対応していない状態 |
| 対応中   | 管理者が確認・対応している状態 |
| 回答済み  | 利用者への返答が完了した状態  |
| クローズ  | 対応完了として終了した状態   |

---

## ディレクトリ構成

主に使用するファイルは以下です。

```text
inquiry-demo-laravel
├── app
│   ├── Http
│   │   └── Controllers
│   │       └── InquiryController.php
│   └── Models
│       └── Inquiry.php
├── database
│   ├── database.sqlite
│   ├── migrations
│   │   └── xxxx_xx_xx_create_inquiries_table.php
│   └── seeders
│       ├── DatabaseSeeder.php
│       └── InquirySeeder.php
├── public
│   └── css
│       └── style.css
├── resources
│   └── views
│       ├── layouts
│       │   └── app.blade.php
│       ├── inquiries
│       │   └── create.blade.php
│       └── admin
│           └── inquiries
│               ├── index.blade.php
│               └── show.blade.php
├── routes
│   └── web.php
└── docs
    ├── design.md
    ├── er-diagram.mmd
    └── laravel-mapping.md
```

---

## Laravel内の役割

| ファイル                         | 役割                             |
| ---------------------------- | ------------------------------ |
| `routes/web.php`             | URLとControllerの処理を紐づける         |
| `InquiryController.php`      | 登録・一覧・詳細・更新・削除などの処理を担当         |
| `Inquiry.php`                | `inquiries` テーブルを扱うModel       |
| `create_inquiries_table.php` | `inquiries` テーブルを作成するMigration |
| `InquirySeeder.php`          | 動作確認用のサンプル問い合わせデータを作成          |
| `app.blade.php`              | 共通レイアウト                        |
| `create.blade.php`           | 問い合わせフォーム画面                    |
| `index.blade.php`            | 管理者一覧画面                        |
| `show.blade.php`             | 管理者詳細画面                        |
| `public/css/style.css`       | 画面デザイン用CSS                     |

---

## ルーティング

| URL                          |   メソッド | 処理          | Controller                  |
| ---------------------------- | -----: | ----------- | --------------------------- |
| `/`                          |    GET | 問い合わせフォーム表示 | `InquiryController@create`  |
| `/inquiries`                 |   POST | 問い合わせ登録     | `InquiryController@store`   |
| `/admin/inquiries`           |    GET | 管理者一覧表示     | `InquiryController@index`   |
| `/admin/inquiries/{inquiry}` |    GET | 問い合わせ詳細表示   | `InquiryController@show`    |
| `/admin/inquiries/{inquiry}` |    PUT | ステータス・返答更新  | `InquiryController@update`  |
| `/admin/inquiries/{inquiry}` | DELETE | 問い合わせ削除     | `InquiryController@destroy` |

---

## 実装済み機能

### 利用者側

* 問い合わせフォーム表示
* 問い合わせ登録
* サーバー側バリデーション
* 登録後、管理者一覧画面へ遷移

### 管理者側

* 問い合わせ一覧表示
* 問い合わせ詳細表示
* ステータス変更
* 管理者返答内容の保存
* 問い合わせ削除
* キーワード検索
* ステータス絞り込み
* カテゴリ絞り込み
* 件数サマリー表示
* ページネーション

### 開発補助

* Seederによるサンプルデータ投入
* SQLiteによるローカルDB管理
* DB Browser for SQLiteでのDB確認

---

## 検索・絞り込み機能

管理者一覧画面では、以下の条件で問い合わせを検索できます。

* キーワード
* ステータス
* カテゴリ

キーワード検索では、以下の項目を対象にしています。

* 件名
* 名前
* メールアドレス
* 問い合わせ本文

検索条件はController側で受け取り、DB検索条件として反映しています。

---

## ページネーション

管理者一覧画面では、問い合わせを10件ずつ表示するページネーションを実装しています。

検索条件を指定した状態でページ移動しても条件が維持されるように、`withQueryString()` を使用しています。

---

## Seederについて

開発・動作確認用に `InquirySeeder` を作成しています。

Seederを実行することで、問い合わせデータを手入力せずに複数件登録できます。

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

```bash
copy .env.example .env
```

### 3. APP_KEYを生成

```bash
php artisan key:generate
```

### 4. SQLiteファイルを作成

```bash
New-Item -ItemType File database\database.sqlite
```

### 5. `.env` のDB設定を確認

```env
DB_CONNECTION=sqlite
```

### 6. タイムゾーン設定

`.env` に以下を追加します。

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

### 7. マイグレーション実行

```bash
php artisan migrate
```

サンプルデータも同時に入れる場合は以下を使用します。

```bash
php artisan migrate:fresh --seed
```

### 8. Laravelを起動

```bash
php artisan serve
```

ブラウザで以下を開きます。

```text
http://127.0.0.1:8000
```

---

## 動作確認手順

### 1. 問い合わせ登録

トップページを開きます。

```text
http://127.0.0.1:8000
```

問い合わせフォームに以下のような内容を入力して送信します。

```text
お名前：山田 太郎
メールアドレス：yamada@example.com
件名：料金について
カテゴリ：質問
問い合わせ内容：料金プランを教えてください。
```

登録後、管理者一覧画面へ遷移します。

### 2. 管理者一覧確認

以下を開きます。

```text
http://127.0.0.1:8000/admin/inquiries
```

登録した問い合わせが一覧に表示されることを確認します。

### 3. 詳細確認

一覧の「詳細」を押し、問い合わせ詳細画面を確認します。

### 4. ステータス・返答更新

詳細画面でステータスと返答内容を入力し、保存します。

例：

```text
ステータス：対応中
返答内容：確認いたします。
```

保存後、一覧へ戻り、再度詳細を開いて内容が保持されていることを確認します。

### 5. 検索・絞り込み確認

管理者一覧画面で以下を確認します。

* キーワード検索
* ステータス絞り込み
* カテゴリ絞り込み
* 検索条件リセット
* ページネーション

### 6. 削除確認

一覧画面の削除ボタンから、対象の問い合わせを削除できることを確認します。

---

## 処理フロー

### 問い合わせ登録

```text
ブラウザで問い合わせフォームを開く
↓
POST /inquiries
↓
routes/web.php
↓
InquiryController@store
↓
バリデーション
↓
Inquiry Model
↓
inquiries テーブルへ保存
↓
管理者一覧へリダイレクト
```

### 管理者一覧表示

```text
GET /admin/inquiries
↓
routes/web.php
↓
InquiryController@index
↓
検索条件を取得
↓
Inquiry ModelでDB検索
↓
index.blade.phpへデータを渡す
↓
一覧画面を表示
```

### ステータス・返答更新

```text
詳細画面でステータス・返答を入力
↓
PUT /admin/inquiries/{inquiry}
↓
InquiryController@update
↓
バリデーション
↓
対象問い合わせをDB更新
↓
管理者一覧へリダイレクト
```

### 削除

```text
一覧画面で削除ボタンを押す
↓
DELETE /admin/inquiries/{inquiry}
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
| GitHub Pagesで公開可能       | PHPとDBが動くサーバーが必要             |

---

## SQLiteとMySQLについて

現在はローカル開発を優先し、SQLiteを使用しています。

SQLiteは `database/database.sqlite` という1つのファイルをDBとして扱えるため、環境構築が簡単です。

将来的に実務や本番公開に近い構成を想定する場合は、MySQLへの移行を検討します。

| 段階   | DB     | 目的                 |
| ---- | ------ | ------------------ |
| 初期実装 | SQLite | ローカルでDB保存の流れを確認    |
| 実務想定 | MySQL  | 複数ユーザー利用・本番運用に近い構成 |

---

## 今後の拡張予定

* バリデーションメッセージの日本語化
* 管理者ログイン機能
* 操作ログ管理
* メール送信機能
* 返答履歴の別テーブル化
* 担当者設定
* 優先度設定
* 対応期限設定
* ダッシュボード機能
* MySQLへの移行
* Web公開対応

---

## 注意点

現在のLaravel版はローカル環境で動作確認している段階です。

```text
http://127.0.0.1:8000
```

これは自分のPC内でのみ確認できるURLです。

Web公開する場合は、GitHub Pagesではなく、PHPとDBが動作するサーバーが必要になります。

Laravelを公開する場合は、`public` ディレクトリを公開入口にし、`.env`、DB接続、Composer、Webサーバー設定などを行う必要があります。

---

## 補足

このLaravel版は、静的Demoで作成した問い合わせ管理フローを、DB保存できるWebアプリ構成に置き換えるためのプロトタイプです。

本番運用を目的としたものではなく、設計・実装の流れ、Laravelの基本構成、DB保存の仕組みを理解するための学習用・提案用Demoとして作成しています。
