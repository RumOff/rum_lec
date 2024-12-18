@extends('admin.layout')
@section('title', '診断一覧 - ' . $user->name)

@section('content')
<div class="main-panel">

    <div class="content">
        <p class="back-link"><a class="arrow-right left" href="#" onclick="history.back(-1);return false;">戻る</a></p>

        <div class="content_wrap">
            <h1 class="content_title">診断一覧 - {{ $user->name }}</h1>
        </div>

        @include('admin.messages')

        <div class="row">
            <div class="col-md-12">
                <div class="card">

                    <div class="card-body">
                        @if ($surveyTargetUsers->isNotEmpty())
                            <table class="table">
                                <tbody class="list">
                                    <tr class="list-title">
                                        <th>企業名</th>
                                        <th>診断名</th>
                                        <th>チーム</th>
                                        <th>批評価者 / 評価者</th>
                                        <th></th>
                                    </tr>

                                    @foreach($surveyTargetUsers as $surveyTargetUser)
                                        <tr class="no-link">
                                            <td>
                                                {{ $surveyTargetUser->user->company->name }}
                                            </td>
                                            <td>
                                                {{ $surveyTargetUser->survey->title }}
                                            </td>
                                            <td>{{ $surveyTargetUser->team }}</td>
                                            <td>
                                                <strong>
                                                    {{ $surveyTargetUser->user->name }}（{{ $surveyTargetUser->post }}）
                                                    @if ($surveyTargetUser->surveyAnswer->completes_at)
                                                        <img src="/assets/img/icon-complete.svg" alt="" width="12px">
                                                    @endif
                                                </strong>
                                                &nbsp;/&nbsp;
                                                @foreach ($surveyTargetUser->raters as $surveyTargetUserAsRater)
                                                    @if (! $loop->first)
                                                    &nbsp;・&nbsp;
                                                    @endif
                                                    {{ $surveyTargetUserAsRater->user->name }}
                                                    @if ($surveyTargetUserAsRater->surveyAnswer->completes_at)
                                                        <img src="/assets/img/icon-complete.svg" alt="" width="12px">
                                                    @endif
                                                @endforeach
                                            </td>
                                            <td>
                                                <p class="button next-btn">
                                                    <a href="{{ route('admin.companies.surveys.results', [$surveyTargetUser->user->company, $surveyTargetUser->survey, $surveyTargetUser]) }}" target="_blank">診断結果</a>
                                                </p>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <table class="table">
                                <tbody class="list">
                                    <tr class="list-title">
                                        <th>企業名</th>
                                        <th>チーム</th>
                                        <th>被評価者名</th>
                                        <th>評価者</th>
                                    </tr>

                                    <tr class="details-link">
                                        <td></td>
                                        <td class="text-muted">診断データはありません</td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                </tbody>
                            </table>
                        @endif
                    </div>

                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@push('style')
<style>
</style>
@endpush

@push('script')
<script>
    //テーブルのtrにリンクを付ける
    jQuery(function($) {
        $('.details-link[data-href]').addClass('clicktable').click(function() {
            window.location = $(this).attr('data-href');
        }).find('a').hover(function() {
            $(this).parents('.details-link').unbind('click');
        }, function() {
            $(this).parents('.details-link').click(function() {
                window.location = $(this).attr('data-href');
            });
        });
    });

</script>
@endpush
