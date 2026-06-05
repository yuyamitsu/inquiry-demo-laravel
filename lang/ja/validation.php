<?php

return [

    'required' => ':attributeは必ず入力してください。',
    'string' => ':attributeは文字列で入力してください。',
    'max' => [
        'string' => ':attributeは:max文字以内で入力してください。',
    ],
    'email' => ':attributeは正しいメールアドレス形式で入力してください。',
    'in' => '選択された:attributeが正しくありません。',

    'attributes' => [
        'name' => 'お名前',
        'email' => 'メールアドレス',
        'category' => 'カテゴリ',
        'subject' => '件名',
        'message' => 'お問い合わせ内容',
        'status' => 'ステータス',
        'admin_reply' => '管理者返答',
        'title' => '件名',
        'body' => 'お問い合わせ内容',
    ],

];
