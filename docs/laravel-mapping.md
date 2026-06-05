# 静的Demo版とLaravel版の対応整理

## 概要

静的Demo版では、JavaScriptとlocalStorageで問い合わせデータを管理していた。
Laravel版では、Controller、Model、Blade、DBを使って、サーバー側で問い合わせデータを管理する構成に変更した。

## 対応表

| 静的Demo版 | Laravel版 |
| --- | --- |
| HTMLフォーム | Bladeのcreate.blade.php |
| JavaScriptの入力チェック | Laravelのバリデーション |
| localStorage保存 | SQLiteのinquiriesテーブル |
| JavaScriptで一覧生成 | ControllerでDB取得しBladeで表示 |
| JavaScriptで検索 | Controllerでwhere条件を組み立て |
| JavaScriptでステータス更新 | PUTリクエストでController更新 |
| localStorageから削除 | DELETEリクエストでDB削除 |

## Laravel版で追加された要素

- Migrationによるテーブル管理
- ModelによるDB操作
- Controllerによるリクエスト処理
- Bladeによる画面表示
- Seederによるサンプルデータ投入
- ページネーション
- サーバー側バリデーション
