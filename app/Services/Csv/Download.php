<?php

namespace App\Services\Csv;

use App\Repositories\Survey\SurveyRepository;
use Closure;

class Download
{
    const HEADER = [
        '企業ID',
        '企業名',
        '診断ID',
        '診断名',
        '回答期限',
        '診断開始日時',
        '診断終了日時',
        '受診者ID',
        '氏名',
        'チーム名・部署名',
        '役職名',
        'メールアドレス',
        '診断回答ID',
    ];

    protected SurveyRepository $surveyRepo;

    public function __construct(SurveyRepository $surveyRepo)
    {
        $this->surveyRepo = $surveyRepo;
    }

    /**
     * CSV用ヘッダー情報
     */
    public function getHeader(): array
    {
        return [
            'Content-Type' => 'application/octet-stream'
        ];
    }

    /**
     * 会社IDでデータを取得
     */
    public function downloadByCompany(int $companyId): Closure
    {
        return $this->generateCsvData(compact('companyId'));
    }

    /**
     *　回答期間でデータを取得
     */
    public function downloadbyPeriod(string $startsAt, string $expiresAt): Closure
    {
        $period = [$startsAt, $expiresAt];

        return $this->generateCsvData(compact('period'));
    }

    /**
     * 診断メールIDでデータを取得
     */
    public function downloadBySurvey(int $surveyId): Closure
    {
        return $this->generateCsvData(compact('surveyId'));
    }

    /**
     * データを取得してCSV用にフォーマットする
     */
    private function generateCsvData(array $params): Closure
    {
        return function () use ($params) {

            // 出力バッファをopen
            $stream = fopen('php://output', 'w');
            // 文字コードをShift-JISに変換
            stream_filter_prepend($stream, 'convert.iconv.utf-8/cp932//TRANSLIT');
            // ヘッダー用設問取得
            list($questionHeader, $questionIds) = $this->surveyRepo->collectQuestionForDownload($params);
            // ヘッダー行
            fputcsv($stream, array_merge(static::HEADER, $questionHeader));
            // データ取得
            $surveyAnswers = $this->surveyRepo->builderDownloadDataBy($params);
            // ２行目以降の出力
            // cursor()メソッドで１レコードずつストリームに流す処理を実現できる。
            foreach ($surveyAnswers->cursor() as $surveyAnswer) {
                $data = [
                    optional($surveyAnswer->survey->company)->id,
                    optional($surveyAnswer->survey->company)->name,
                    optional($surveyAnswer->survey)->id,
                    optional($surveyAnswer->survey)->title,
                    optional($surveyAnswer->survey)->expires_at->format('Y-m-d'),
                    $surveyAnswer->survey->starts_at->format('Y-m-d'),
                    $surveyAnswer->survey->expires_at->format('Y-m-d'),
                    optional($surveyAnswer->surveyTargetUser->user)->id,
                    optional($surveyAnswer->surveyTargetUser)->team ? optional($surveyAnswer->surveyTargetUser->user)->name : null,
                    optional($surveyAnswer->surveyTargetUser)->team,
                    optional($surveyAnswer->surveyTargetUser)->post,
                    optional($surveyAnswer->surveyTargetUser->user)->email,
                    $surveyAnswer->id,
                ];

                // 各設問の回答データを追加
                foreach ($questionIds as $qid) {
                    $detail = $surveyAnswer->surveyAnswerDetails->firstWhere('survey_question_id', $qid);
                    $data[] = optional($detail)->score ?? optional($detail)->text ?? '';
                }
                fputcsv($stream, $data);
            }
            fclose($stream);
        };
    }
}
