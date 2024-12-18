<?php

return [
    'accepted'             => ':attribute を承認してください。',
    'accepted_if' => ':otherが:valueの場合、:attribute を承認してください。',
    'active_url'           => ':attribute が有効なURLではありません。',
    'after'                => ':attribute には、:dateより後の日付を指定してください。',
    'after_or_equal'       => ':attribute には、:date以降の日付を指定してください。',
    'alpha'                => ':attribute はアルファベットのみがご利用できます。',
    'alpha_dash'           => ':attribute はアルファベットとダッシュ(-)及び下線(_)がご利用できます。',
    'alpha_num'            => ':attribute はアルファベット数字がご利用できます。',
    'array'                => ':attribute は配列でなくてはなりません。',
    'before'               => ':attribute には、:dateより前の日付をご利用ください。',
    'before_or_equal'      => ':attribute には、:date以前の日付をご利用ください。',
    'between'              => [
        'numeric' => ':attribute は、:minから:maxの間で指定してください。',
        'file'    => ':attribute は、:min kBから、:max kBの間で指定してください。',
        'string'  => ':attribute は、:min文字から、:max文字の間で指定してください。',
        'array'   => ':attribute は、:min個から:max個の間で指定してください。',
    ],
    'boolean'              => ':attribute は、trueかfalseを指定してください。',
    'confirmed'            => ':attribute と、確認フィールドとが、一致していません。',
    'current_password'     => 'パスワードが正しくありません。',
    'date'                 => ':attribute には有効な日付を指定してください。',
    'date_equals'          => ':attribute には、:dateと同じ日付けを指定してください。',
    'date_format'          => ':attribute は:format形式で指定してください。',
    'different'            => ':attribute と:otherには、異なった内容を指定してください。',
    'digits'               => ':attribute は:digits桁で指定してください。',
    'digits_between'       => ':attribute は:min桁から:max桁の間で指定してください。',
    'dimensions'           => ':attribute の図形サイズが正しくありません。',
    'distinct'             => ':attribute には異なった値を指定してください。',
    'email'                => ':attribute には、有効なメールアドレスを指定してください。',
    'ends_with'            => ':attribute には、:valuesのどれかで終わる値を指定してください。',
    'exists'               => '選択された:attribute は正しくありません。',
    'file'                 => ':attribute にはファイルを指定してください。',
    'filled'               => ':attribute に値を指定してください。',
    'gt'                   => [
        'numeric' => ':attribute には、:valueより大きな値を指定してください。',
        'file'    => ':attribute には、:value kBより大きなファイルを指定してください。',
        'string'  => ':attribute は、:value文字より長く指定してください。',
        'array'   => ':attribute には、:value個より多くのアイテムを指定してください。',
    ],
    'gte'                  => [
        'numeric' => ':attribute には、:value以上の値を指定してください。',
        'file'    => ':attribute には、:value kB以上のファイルを指定してください。',
        'string'  => ':attribute は、:value文字以上で指定してください。',
        'array'   => ':attribute には、:value個以上のアイテムを指定してください。',
    ],
    'image'                => ':attribute には画像ファイルを指定してください。',
    'in'                   => '選択された:attribute は正しくありません。',
    'in_array'             => ':attribute には:otherの値を指定してください。',
    'integer'              => ':attribute は整数で指定してください。',
    'ip'                   => ':attribute には、有効なIPアドレスを指定してください。',
    'ipv4'                 => ':attribute には、有効なIPv4アドレスを指定してください。',
    'ipv6'                 => ':attribute には、有効なIPv6アドレスを指定してください。',
    'json'                 => ':attribute には、有効なJSON文字列を指定してください。',
    'lt'                   => [
        'numeric' => ':attribute には、:valueより小さな値を指定してください。',
        'file'    => ':attribute には、:value kBより小さなファイルを指定してください。',
        'string'  => ':attribute は、:value文字より短く指定してください。',
        'array'   => ':attribute には、:value個より少ないアイテムを指定してください。',
    ],
    'lte'                  => [
        'numeric' => ':attribute には、:value以下の値を指定してください。',
        'file'    => ':attribute には、:value kB以下のファイルを指定してください。',
        'string'  => ':attribute は、:value文字以下で指定してください。',
        'array'   => ':attribute には、:value個以下のアイテムを指定してください。',
    ],
    'max'                  => [
        'numeric' => ':attribute には、:max以下の数字を指定してください。',
        'file'    => ':attribute には、:max kB以下のファイルを指定してください。',
        'string'  => ':attribute は、:max文字以下で指定してください。',
        'array'   => ':attribute は:max個以下指定してください。',
    ],
    'mimes'                => ':attribute には:valuesタイプのファイルを指定してください。',
    'mimetypes'            => ':attribute には:valuesタイプのファイルを指定してください。',
    'min'                  => [
        'numeric' => ':attribute には、:min以上の数字を指定してください。',
        'file'    => ':attribute には、:min kB以上のファイルを指定してください。',
        'string'  => ':attribute は、:min文字以上で指定してください。',
        'array'   => ':attribute は:min個以上指定してください。',
    ],
    'multiple_of' => ':attribute には、:valueの倍数を指定してください。',
    'not_in'               => '選択された:attribute は正しくありません。',
    'not_regex'            => ':attribute の形式が正しくありません。',
    'numeric'              => ':attribute には、数字を指定してください。',
    'password'             => '正しいパスワードを指定してください。',
    'present'              => ':attribute が存在していません。',
    'regex'                => ':attribute に正しい形式を指定してください。',
    'required'             => ':attribute は必ず指定してください。',
    'required_if'          => ':otherが:valueの場合、:attribute も指定してください。',
    'required_unless'      => ':otherが:valuesでない場合、:attribute を指定してください。',
    'required_with'        => ':valuesを指定する場合は、:attribute も指定してください。',
    'required_with_all'    => ':valuesを指定する場合は、:attribute も指定してください。',
    'required_without'     => ':valuesを指定しない場合は、:attribute を指定してください。',
    'required_without_all' => ':valuesのどれも指定しない場合は、:attribute を指定してください。',
    'prohibited'           => ':attribute は入力禁止です。',
    'prohibited_if' => ':otherが:valueの場合、:attribute は入力禁止です。',
    'prohibited_unless'    => ':otherが:valueでない場合、:attribute は入力禁止です。',
    'prohibits'            => 'attributeは:otherの入力を禁じています。',
    'same'                 => ':attribute と:otherには同じ値を指定してください。',
    'size'                 => [
        'numeric' => ':attribute は:sizeを指定してください。',
        'file'    => ':attribute のファイルは、:sizeキロバイトでなくてはなりません。',
        'string'  => ':attribute は:size文字で指定してください。',
        'array'   => ':attribute は:size個指定してください。',
    ],
    'starts_with'          => ':attribute には、:valuesのどれかで始まる値を指定してください。',
    'string'               => ':attribute は文字列を指定してください。',
    'timezone'             => ':attribute には、有効なゾーンを指定してください。',
    'unique'               => ':attribute の値は既に存在しています。',
    'uploaded'             => ':attribute のアップロードに失敗しました。',
    'url'                  => ':attribute に正しい形式を指定してください。',
    'uuid'                 => ':attribute に有効なUUIDを指定してください。',
    'password.mixed'         => ':attribute には、少なくとも 1 つの大文字と 1 つの小文字を含める必要があります。',
    'password.letters'       => ':attribute には少なくとも 1 つの文字が含まれている必要があります。',
    'password.symbols'       => ':attribute には、少なくとも 1 つの記号が含まれている必要があります。',
    'password.numbers'       => ':attribute には、少なくとも 1 つの数値が含まれている必要があります。',
    'password.uncompromised' => ':attribute はデータ漏洩の可能性があります。 別の :attribute を変更してください。',

    /*
    |--------------------------------------------------------------------------
    | Custom バリデーション言語行
    |--------------------------------------------------------------------------
    |
    | "属性.ルール"の規約でキーを指定することでカスタムバリデーション
    | メッセージを定義できます。指定した属性ルールに対する特定の
    | カスタム言語行を手早く指定できます。
    |
    */

    'custom' => [
        '属性名' => [
            'ルール名' => 'カスタムメッセージ',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | カスタムバリデーション属性名
    |--------------------------------------------------------------------------
    |
    | 以下の言語行は、例えば"email"の代わりに「メールアドレス」のように、
    | 読み手にフレンドリーな表現でプレースホルダーを置き換えるために指定する
    | 言語行です。これはメッセージをよりきれいに表示するために役に立ちます。
    |
    */

    'attributes' => [
        'email' => 'メールアドレス'
    ],

];
