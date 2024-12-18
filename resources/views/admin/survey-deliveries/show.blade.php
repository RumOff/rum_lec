@extends('admin.layout')
@section('title', '配信 - 詳細')

@section('content')
<div class="main-panel">

    <div class="content">
        <div class="content_wrap">
            <h1 class="content_title">配信 - 詳細</h1>
        </div>

        @include('admin.messages')

        <form action="{{ route('admin.companies.surveys.survey-deliveries.destroy', [$company, $survey, $surveyDelivery]) }}" method="POST">
            @method('DELETE')
            @csrf

            <div class="row">
                <div class="col-md-12">
                    <div class="card">

                        <dl class="dlTable confirm">

                            <dt>企業名</dt>
                            <dd>{{ $company->name }}</dd>

                            <dt>診断名</dt>
                            <dd>{{ $survey->title }}</dd>

                            <dt>締切日（回答期日）</dt>
                            <dd>{{ $survey->expires_at->format('Y/m/d') }}</dd>

                            <dt class="mt-3">予約時間</dt>
                            <dd class="mt-3 radio-flex">
                                <input type="datetime-local" name="scheduled_sending_at" value="{{ $surveyDelivery->scheduled_sending_at->format('Y-m-d H:i') }}" disabled>
                            </dd>

                            <dt>送信タイトル</dt>
                            <dd><input type="text" name="subject" value="{{ $surveyDelivery->subject }}" class="field" disabled></dd>

                            <dt>本文</dt>
                            <dd>
                                <textarea name="body" cols="30" rows="25" disabled>{{ $surveyDelivery->body }}</textarea>
                                <p class="text-muted mt-2">※「送信タイトル / 本文」内にある置き換え文字は自動で置き換えされます</p>
                            </dd>

                        </dl>

                    </div>
                </div>
            </div>

            <!--btn-area-->
            <div class="btn-area">
                <p class="button defalt-btn"><a href="{{ route('admin.companies.surveys.show', [$company, $survey]) }}">戻る</a></p>
                @if (! $surveyDelivery->completed_sending_at)
                    <p class="button next-btn"><input type="submit" class="submit" value="配信停止"></p>
                @endif
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
</script>
@endpush
