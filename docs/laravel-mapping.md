# 問い合わせ管理Demo Laravel化 対応表

## 静的DemoからLaravel版への対応

| 静的Demo | Laravel版 |
|---|---|
| `index.html` | `resources/views/inquiries/create.blade.php` |
| `pages/admin-list.html` | `resources/views/admin/inquiries/index.blade.php` |
| `pages/admin-detail.html` | `resources/views/admin/inquiries/show.blade.php` |
| `assets/css/style.css` | `public/css/style.css` または `resources/css` |
| `assets/js/script.js` | 基本処理はControllerへ移行 |
| `localStorage` | SQLite / MySQL |

## ルーティング

| URL | メソッド | 処理 |
|---|---:|---|
| `/` | GET | 問い合わせフォーム表示 |
| `/inquiries` | POST | 問い合わせ登録 |
| `/admin/inquiries` | GET | 管理者一覧表示 |
| `/admin/inquiries/{inquiry}` | GET | 問い合わせ詳細表示 |
| `/admin/inquiries/{inquiry}` | PUT | ステータス・返答更新 |
| `/admin/inquiries/{inquiry}` | DELETE | 問い合わせ削除 |
