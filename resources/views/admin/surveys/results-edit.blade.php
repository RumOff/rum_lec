@extends('admin.layout')
@section('title', "診断編集 - {$surveyAnswer->surveyTargetUser->user->name}（{$surveyAnswer->surveyTargetUser->team}）")

@section('content')
<div class="main-panel">

    <div class="content">
        <div class="content_wrap">
            <h1 class="content_title">診断編集 - {{ $surveyAnswer->surveyTargetUser->user->name }}（{{ $surveyAnswer->surveyTargetUser->team }}）</h1>
        </div>

        @include('admin.messages')

        <form action="{{ route('admin.companies.surveys.results.update', [$company, $survey, $surveyAnswer->surveyTargetUser]) }}" method="POST">
            @method('PATCH')
            @csrf

            @if ($textQuestions->isNotEmpty())
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">

                            @foreach($textQuestions as $q)

                                <dl class="dlTable confirm">
                                    <dt style="width: 100%;" class="mb-4">Q {{ Str::replace('$user_name', "{$surveyAnswer->surveyTargetUser->targetUser->name}さん", $q->question) }}</dt>
                                    <dt>{{ $surveyAnswer->surveyTargetUser->targetUser->name }}</dt>
                                    <dd class="mb-2">
                                        <input type="hidden" name="{{ $surveyAnswerDetails['被評価者']->first()->get($q->id)->first()->survey_answer_detail_id }}[id]" value="{{ $surveyAnswerDetails['被評価者']->first()->get($q->id)->first()->survey_answer_detail_id }}">
                                        <textarea name="{{ $surveyAnswerDetails['被評価者']->first()->get($q->id)->first()->survey_answer_detail_id }}[text]" rows="3">{!! nl2br($surveyAnswerDetails['被評価者']->first()->get($q->id)->first()->text) !!}</textarea>
                                    </dd>

                                    @foreach($surveyAnswerDetails['評価者'] as $post => $otherAnswer)
                                        @foreach($otherAnswer->get($q->id) as $answer)
                                            <dt>{{ $post }}</dt>
                                            <dd class="mb-2">
                                                <input type="hidden" name="{{ $answer->survey_answer_detail_id }}[id]" value="{{ $answer->survey_answer_detail_id }}">
                                                <textarea name="{{ $answer->survey_answer_detail_id }}[text]" rows="3">{!! nl2br($answer->text) !!}</textarea>
                                            </dd>
                                        @endforeach
                                    @endforeach
                                </dl>

                            @endforeach

                        </div>
                    </div>
                </div>

                <!--btn-area-->
                <div class="btn-area">
                    <p class="button defalt-btn"><a href="{{ route('admin.companies.surveys.show', [$company, $survey]) }}">キャンセル</a></p>
                    <p class="button next-btn"><input type="submit" class="submit" value="変更"></p>
                </div>
            @else
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="p-5">
                                編集可能な設問はありません。
                                <br>編集ができるのは定性（文章）だけです。
                            </div>
                        </div>
                    </div>
                </div>
            @endif

        </form>

    </div>
</div>
@endsection

@push('style')
<style>
    .q-title {
        padding: 12px;
    }
</style>
@endpush

@push('script')
<script>
</script>
@endpush
