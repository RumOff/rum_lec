<?php

namespace App\Services\Csv;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Support\Facades\Storage;

class Upload
{
    /**
     * csvファイルをアップロードする
     */
    public function upload(UploadedFile $uploadedFile): string
    {
        $orgName = date('YmdHis') . "_" . $uploadedFile->getClientOriginalName();
        $savedPath = $uploadedFile->storeAs('', $orgName);

        logger()->info('Uploadメソッド: ファイル保存後のパス: ' . storage_path('app/' . $savedPath));

        // Storage::existsにはフルパスではなく、相対パスを使う
        if (!Storage::exists($savedPath)) {
            logger()->error('Uploadメソッド: Storageにファイルが存在しません: ' . $savedPath);
            throw new \Exception('Storageにファイルが存在しません: ' . $savedPath);
        }

        return storage_path('app/' . $savedPath);  // ここはフルパスを返す
    }

    /**
     * ファイルの文字コードを取得
     */
    public function getEncode(string $path): string
    {
        return mb_detect_encoding(
            file_get_contents($path),
            ['ASCII', 'ISO-2022-JP', 'UTF-8', 'EUC-JP', 'SJIS'],
            true
        );
    }
    /**
     * CSVファイルのヘッダー行を取得
     * @param string $path CSVファイルのパス
     * @return array CSVヘッダーの配列
     */
    public function getCsvHeaders(string $path): array
    {
        if (empty($path)) {
            return [];
        }

        // ファイルをUTF-8に変換し、タブ区切りをカンマ区切りに変換
        $utf8Path = $this->convertToUtf8AndCommaSeparated($path);

        // FastExcelを使って1行目（ヘッダー部分）だけを取得
        $data = (new FastExcel)->configureCsv(',', '#', 'UTF-8')
            ->import($utf8Path, function ($line) {
                return $line;
            });

        // 最初の行をヘッダーとして取得し、空白を削除
        $headers = array_keys($data->first() ?? []);

        // 空白のヘッダーを除去する
        return array_filter($headers, function ($header) {
            return !empty($header);  // 空白でないヘッダーのみを返す
        });
    }


    /**
     * CSVデータの取得
     */
    // public function getCsvDataWithoutHeader(string $path): array
    // {
    //     if (empty($path)) {
    //         return [];
    //     }

    //     return ((new FastExcel)->configureCsv(',', '#', $this->getEncode($path))
    //         ->importSheets($path))[0];
    // }
    /**
     * CSVデータの取得
     */
    public function getCsvDataWithoutHeader(string $path): array
    {
        // 相対パスでファイルの存在を確認
        $relativePath = str_replace(storage_path('app/'), '', $path);

        if (!Storage::exists($relativePath)) {
            logger()->error("getCsvDataWithoutHeaderメソッド: Storageにファイルが存在しません: " . $relativePath);
            throw new \Exception("Storageにファイルが存在しません: " . $relativePath);
        }

        logger()->info("getCsvDataWithoutHeaderメソッド: ファイルが存在しています: " . $path);

        // ファイルをUTF-8に変換し、タブ区切りをカンマ区切りに変換する
        $utf8Path = $this->convertToUtf8AndCommaSeparated($path);

        // FastExcelを使ってデータを取得
        return ((new FastExcel)->configureCsv(',', '#', 'UTF-8')
            ->importSheets($utf8Path))[0];
    }

    /**
     * ファイルのエンコードを検出して、必要ならUTF-8に変換
     */
    public function convertToUtf8AndCommaSeparated(string $path): string
    {
        // 相対パスに変換
        $relativePath = str_replace(storage_path('app/'), '', $path);

        // ファイルが存在するか確認
        if (!Storage::exists($relativePath)) {
            throw new \Exception("ファイルが存在しません: " . $relativePath);
        }

        // LaravelのStorageを使ってファイルを取得
        $content = Storage::get($relativePath);

        // ファイルのエンコードを検出
        $encoding = mb_detect_encoding($content, ['ASCII', 'ISO-2022-JP', 'UTF-8', 'EUC-JP', 'SJIS', 'UTF-16', 'UTF-16LE'], true);

        // UTF-8に変換が必要な場合
        if ($encoding !== 'UTF-8') {
            // UTF-8に変換
            $content = mb_convert_encoding($content, 'UTF-8', $encoding);
            // タブ区切りをカンマ区切りに変換
            $content = str_replace("\t", ",", $content);
            // 一時ファイルに変換後の内容を保存
            $tmpPath = sys_get_temp_dir() . '/' . basename($path) . '.utf8.csv';
            file_put_contents($tmpPath, $content);

            return $tmpPath;  // 変換されたファイルのパスを返す
        }

        // すでにUTF-8ならそのまま返す
        return $path;
    }

    /**
     * ファイルパスを元にCSVのrow数を返す
     */
    public function count(string $path): int
    {
        return count($this->getCsvDataWithoutHeader($path));
    }

    /**
     * バリデーションをかける
     */
    public function validate(
        string $fileName,
        string $path,
        array $headers,
        array $validate,
        int $maxCount
    ): array {
        $messages = collect();
        $dataCount = $this->count($path);

        // データが0だったらエラー
        if ($dataCount < 1) {
            $messages->push("【${fileName}】データが0件です、ファイルの内容をご確認ください。");
        }

        // データがmax以上だったらエラー
        if ($dataCount > $maxCount) {
            $messages->push("【${fileName}】データが${dataCount}件です、${maxCount}以内でお願いします。");
        }

        $data = $this->getCsvDataWithoutHeader($path);

        // header情報が一致しなかったらエラー
        $dataHeaders = head($data);
        foreach ($headers as $header) {
            if (! array_key_exists($header, $dataHeaders)) {
                $messages->push("【${fileName}】header情報に「${header}」がありません。");
            }
        }

        // 1行ごとにチェック
        foreach ($data as $index => $line) {
            $lineNum = $index + 2;
            // header数とデータ数が一致しない場合はエラー
            if (count($line) !== count($headers)) {
                // $lineNumは0から始まるのでheader行も加味して＋2する
                $messages->push("【${fileName}-${lineNum}行目】項目数が合いません。");
            }

            $validator = Validator::make($line, $validate);
            // エラー文多すぎる場合があるので1行だけエラー内出力
            if ($validator->fails()) {
                foreach ($validator->errors()->all() as $message) {
                    $messages->push("【${fileName}-${lineNum}行目】${message}");
                    break;
                }
            }
            break;
        }

        return $messages->toArray();
    }
}
