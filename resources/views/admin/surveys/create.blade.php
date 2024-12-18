@extends('admin.layout')
@section('title', '新規診断 - 登録')

@section('content')
<div class="main-panel">
    <div class="content">
        <div class="content_wrap">
            <h1 class="content_title">新規診断 - 登録</h1>
        </div>

        @include('admin.messages')

        <form action="{{ route('admin.companies.surveys.confirm', $company) }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <dl class="dlTable confirm">
                            <dt>診断名</dt>
                            <dd><input type="text" name="title" value="{{ old('title') }}" placeholder="診断名を記載してください" required maxlength="50" class="field"></dd>

                            <dt>サーベイ種別</dt>
                            <dd>
                                <select name="survey_type" id="survey-type" required>
                                    <option value="">選択してください</option>
                                    <option value="specified" {{ old('survey_type') == 'specified' ? 'selected' : '' }}>回答者・所属指定</option>
                                    <option value="open" {{ old('survey_type') == 'open' ? 'selected' : '' }}>オープンアンケート</option>
                                </select>
                            </dd>

                            <dt>診断実施期間</dt>
                            <dd>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="starts_at">開始日:</label>
                                        <input type="date" name="starts_at" value="{{ old('starts_at') }}" id="starts_at" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="expires_at">終了日:</label>
                                        <input type="date" name="expires_at" value="{{ old('expires_at') }}" id="expires_at" required>
                                    </div>
                                </div>
                            </dd>

                            <dt>対象者CSV</dt>
                            <dd>
                                <input type="file" name="csv_users" accept="csv" required>
                                <div id="specified-csv" style="display: none;">
                                    <p class="d-flex text-muted mt-2">
                                        ※回答者・所属指定用CSVをアップロードしてください
                                        <a class="download ml-2" href="/files/sample-user.csv" download>回答者・所属指定用CSVサンプル</a>
                                    </p>
                                </div>
                                <div id="open-csv" style="display: none;">
                                    <p class="d-flex text-muted mt-2">
                                        ※オープンアンケート用CSVをアップロードしてください
                                        <a class="download ml-2" href="/files/sample-user-open.csv" download>オープンアンケート用CSVサンプル</a>
                                    </p>
                                </div>
                            </dd>

                            <dt>フォームURL</dt>
                            <dd>
                                <input type="url" name="form_url" value="{{ old('form_url') }}" placeholder="クリエイティブサーベイにて作成したアンケートフォームのURLを入力してください" required class="field">
                            </dd>

                            <dt>パスワード</dt>
                            <dd>
                                <input type="text" name="form_password" value="{{ old('form_password') }}" placeholder="アンケートフォームのパスワードを入力してください" class="field">
                            </dd>
                        </dl>
                    </div>
                </div>
            </div>

            <!--btn-area-->
            <div class="btn-area">
                <p class="button defalt-btn"><a href="{{ route('admin.companies.show', $company) }}">キャンセル</a></p>
                <p class="button next-btn"><input type="submit" class="submit" value="確認"></p>
            </div>
        </form>
    </div>
</div>
@endsection

@push('script')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const surveyTypeSelect = document.getElementById('survey-type');
    const specifiedCsvInfo = document.getElementById('specified-csv');
    const openCsvInfo = document.getElementById('open-csv');

    function updateCsvInfo() {
        if (surveyTypeSelect.value === 'specified') {
            specifiedCsvInfo.style.display = 'block';
            openCsvInfo.style.display = 'none';
        } else if (surveyTypeSelect.value === 'open') {
            specifiedCsvInfo.style.display = 'none';
            openCsvInfo.style.display = 'block';
        } else {
            specifiedCsvInfo.style.display = 'none';
            openCsvInfo.style.display = 'none';
        }
    }

    surveyTypeSelect.addEventListener('change', updateCsvInfo);
    updateCsvInfo(); // 初期表示時にも実行
});
</script>
@endpush
