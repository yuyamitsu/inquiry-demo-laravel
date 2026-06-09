<?php

return [

    'required' => ':attributeは必ず入力してください。',
    'string' => ':attributeは文字列で入力してください。',
    'max' => [
        'string' => ':attributeは:max文字以内で入力してください。',
    ],
    'min' => [
        'array' => ':attributeは:min個以上にしてください。',
        'file' => ':attributeは:min KB以上にしてください。',
        'numeric' => ':attributeは:min以上にしてください。',
        'string' => ':attributeは:min文字以上で入力してください。',
    ],
    'email' => ':attributeは正しいメールアドレス形式で入力してください。',
    'in' => '選択された:attributeが正しくありません。',
    'unique' => ':attributeはすでに使用されています。',
    'confirmed' => ':attributeと確認用の入力が一致しません。',
    'regex' => ':attributeは半角英数字・記号で入力してください。',

    'attributes' => [
        'name' => 'お名前',
        'email' => 'メールアドレス',
        'category' => 'カテゴリ',
        'subject' => '件名',
        'message' => 'お問い合わせ内容',
        'status' => 'ステータス',
        'title' => '件名',
        'body' => 'お問い合わせ内容',
        'password' => 'パスワード',
        'password_confirmation' => 'パスワード確認',
        'assignee_id' => '担当者',
        'priority' => '優先度',
        'due_date' => '対応期限',
    ],

];
