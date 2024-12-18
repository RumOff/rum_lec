@extends('admin.layout')
@section('title', '配信 - 予約')

@section('content')
<div class="main-panel">

    <div class="content">
        <div class="content_wrap">
            <h1 class="content_title">配信 - 予約</h1>
        </div>

        @include('admin.messages')

        <form action="{{ route('admin.companies.surveys.survey-deliveries.store', [$company, $survey]) }}" method="POST">
            @csrf

            <div class="row">
                <div class="col-md-12">
                    <div class="card">

                        <dl class="dlTable confirm">

                            <dt>企業名</dt>
                            <dd>{{ $company->name }}</dd>

                            <dt>診断名</dt>
                            <dd>{{ $survey->title }}</dd>

                            <dt>診断期間</dt>
                            <dd>{{ $survey->starts_at->format('Y/m/d') }} - {{ $survey->expires_at->format('Y/m/d') }}</dd>

                            <dt class="mt-3">リマインド送信</dt>
                            <dd class="mt-3">
                                <input type="checkbox" id="isRemind" name="is_remind">
                            </dd>

                            <dt class="mt-3">予約時間</dt>
                            <dd class="mt-3 radio-flex">
                                <input type="datetime-local" name="scheduled_sending_at" value="" required>
                            </dd>

                            <dt>送信タイトル</dt>
                            <dd><input type="text" name="subject" value="{{ old('subject') ?? $emailTemplate['subject'] }}" placeholder="{{ $emailTemplate['subject'] }}" required maxlength="50" class="field"></dd>

                            <dt>本文</dt>
                            <dd>
                                <textarea name="body" cols="30" rows="25" placeholder="{{ $emailTemplate['body'] }}" required>{{ old('body') ?? $emailTemplate['body'] }}</textarea>
                                <p class="text-muted mt-2">※「送信タイトル / 本文」内にある置き換え文字は自動で置き換えされます</p>
                            </dd>

                        </dl>

                    </div>
                </div>
            </div>

            <!--btn-area-->
            <div class="btn-area">
                <p class="button defalt-btn"><a href="{{ route('admin.companies.surveys.show', [$company, $survey]) }}">キャンセル</a></p>
                <p class="button next-btn"><input type="submit" class="submit" value="作成"></p>
            </div>
        </form>

    </div>
</div>
@endsection

@push('style')
<style>
</style>
@endpush

@push('script')
<script>
    document.getElementById('isRemind').addEventListener('change', function () {
        if (this.checked) {
            // リマインド用のテンプレートを取得する
            fetchRemindTemplate();
        } else {
            // 通常のテンプレートに戻す
            fetchDefaultTemplate();
        }
    });

    function fetchRemindTemplate() {
        fetch("{{ route('admin.survey-deliveries.get_remind_template') }}")
            .then(response => response.json())
            .then(data => {
                // テンプレートの内容をフォームに反映
                document.querySelector('input[name="subject"]').value = data.subject;
                document.querySelector('textarea[name="body"]').value = data.body;
            })
            .catch(error => console.error('Error fetching remind template:', error));
    }

    function fetchDefaultTemplate() {
        fetch("{{ route('admin.survey-deliveries.get_default_template') }}")
            .then(response => response.json())
            .then(data => {
                // テンプレートの内容をフォームに反映
                document.querySelector('input[name="subject"]').value = data.subject;
                document.querySelector('textarea[name="body"]').value = data.body;
            })
            .catch(error => console.error('Error fetching default template:', error));
    }
</script>
@endpush
