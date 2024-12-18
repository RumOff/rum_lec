<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class SurveyDelivery extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'targetable_id',
        'targetable_type',
        'job_id',
        'subject',
        'body',
        'scheduled_sending_at',
        'started_sending_at',
        'completed_sending_at',
        'sending_count',
    ];

    protected $casts = [
        'scheduled_sending_at' => 'datetime',
        'started_sending_at' => 'datetime',
        'completed_sending_at' => 'datetime',
        'sending_count' => 'integer',
    ];

    public function survey(): BelongsTo
    {
        return $this->belongsTo(Survey::class);
    }

    public function targetable(): MorphTo
    {
        return $this->morphTo();
    }

    public static function getEmailTemplate()
    {
        return [
            'subject' => '「[[診断名]]」受診のお願い - [[企業名]]',
            'body' => '

このメールは【[[企業名]]】様より委託を受け、
株式会社NEWONE事務局が皆様に配信しております。

このメールは皆様の組織に関する意識調査になります。
下記URLにアクセスの上、アンケートへの回答をお願いいたします。
15分以内で終了する内容となっております。

[アンケートリンク:[[フォームURL]]?survey=[[診断ID]]&answer=[[カスタムキー]]]
パスワード:[[パスワード]]

受診期間  :[[受診期限日]]まで


---------------------------------------------------------------
株式会社NEWONE運営事務局
example@new-one.co.jp
            '
        ];
    }

    public static function getEmailTemplateAtResend()
    {
        return [
            'subject' => '【リマインド】「[[診断名]]」受診のお願い - [[企業名]]',
            'body' => '

サーベイ未受診の皆さま

先日、メールでお願いしておりましたサーベイを受診されていないようなので、早めの受診をお願いいたします。

15分以内で終了する内容となっております。
お忙しいとは存じますが、[[受診期限日]]までに完了していただきますよう、
よろしくお願いいたします。

[アンケートリンク:[[フォームURL]]?survey=[[診断ID]]&answer=[[カスタムキー]]]
パスワード:[[パスワード]]

受診期間  :[[受診期限日]]まで


---------------------------------------------------------------
株式会社NEWONE運営事務局
example@new-one.co.jp
            '
        ];
    }

    public static function swapEmailSubjectTemplate(
        string $subject,
        string $companyName,
        string $surveyTitle,
    ) {
        return  Str::swap([
            '[[企業名]]' => $companyName,
            '[[診断名]]' => $surveyTitle,
        ], $subject);
    }

    public static function swapEmailBodyTemplate(
        string $body,
        array $replacements // 必要なプレースホルダーと置換文字列を連想配列で渡す
    ) {
        // プレースホルダーと値が存在するもののみ置換
        $placeholders = [
            // '[[氏名]]' => $replacements['name'] ?? null,
            '[[企業名]]' => $replacements['companyName'] ?? null,
            '[[フォームURL]]' => $replacements['formUrl'] ?? null,
            '[[パスワード]]' => $replacements['formPassword'] ?? null,
            '[[診断ID]]' => $replacements['surveyId'] ?? null,
            '[[カスタムキー]]' => $replacements['customKey'] ?? null,
            '[[受診者名]]' => $replacements['userName'] ?? null,
            '[[メールアドレス]]' => $replacements['userEmail'] ?? null,
            '[[受診期限日]]' => $replacements['expiresAt'] ?? null,
            'example@new-one.co.jp' => config('app.url'),
        ];

        // パスワードがない場合に行を削除
        if (empty($replacements['formPassword'])) {
            $body = preg_replace('/^.*パスワード:\[\[パスワード\]\].*$(\R)?/m', '', $body);
        }

        // 値があるもののみ swap する
        $filteredPlaceholders = array_filter($placeholders, function ($value) {
            return !is_null($value);
        });

        return Str::swap($filteredPlaceholders, $body);
    }
}
