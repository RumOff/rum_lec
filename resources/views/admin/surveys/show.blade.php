@extends("admin.layout")
@section("title", "診断詳細 - " . $survey->title)

@section("content")
    <div class="main-panel">
        <div class="content">

            <p class="back-link"><a class="arrow-right left" href="{{ route("admin.companies.show", $company) }}">戻る</a></p>

            <div class="content_wrap">
                <h1 class="content_title">診断詳細</h1>
            </div>

            @include("admin.messages")

            <div class="row mb40">
                <div class="col-md-12">
                    <div class="card pa24">

                        <div class="content_wrap">
                            <h1 class="content_title">{{ $survey->title }}</h1>
                            <div class="save_wrap">
                                <a href="{{ route("admin.companies.surveys.edit", [$company, $survey]) }}">
                                    <img class="ml8" src="/assets/img/icon-edit.svg" alt="">
                                </a>
                            </div>
                        </div>

                        <div class="mt-2">
                            <table class="table">
                                <tbody class="list">
                                    <tr class="no-link">
                                        <td>
                                            <span class="delivery font-weight-bold">実施期間</span>
                                            {{ $survey->starts_at->format("Y.m.d") }} -
                                            {{ $survey->expires_at->format("Y.m.d") }}
                                        </td>
                                        <td>
                                            {{-- <span class="delivery font-weight-bold">結果閲覧</span>
                                            @if ($survey->open_results)
                                                <span class="status incompletes open_results">公開中</span>
                                            @else
                                                <span class="status open_results">非公開</span>
                                            @endif --}}
                                        </td>
                                        <td>
                                            <span class="delivery font-weight-bold">回答進捗</span>
                                            {{ $survey->survey_completed_count }} / {{ $survey->survey_total_count }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>

            <div class="row mb40">
                <div class="col-md-12">
                    <div class="card pa24">

                        <div class="content_wrap">
                            <h1 class="content_title">設問リスト</h1>
                        </div>

                        <div class="mt-2">
                            <table class="table">
                                <tbody class="list">
                                    @if (!$countSurveyQuestion)
                                        <tr class="no-link">
                                            <th>設問データがありません</th>
                                        </tr>
                                    @else
                                        <tr class="details-link"
                                            data-href="{{ route("admin.companies.surveys.survey-questions.edit", [$company, $survey]) }}">
                                            <th>
                                                登録設問数: {{ $countSurveyQuestion }} 問
                                            </th>
                                            <th class="arrow-right"></th>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
            <!--list-->
            <div class="row mb40">
                <div class="col-md-12">
                    <div class="card pa24">

                        <div class="content_wrap">
                            <h1 class="content_title">配信リスト</h1>
                            <div class="save_wrap">
                                <p class="button small_btn">
                                    <a
                                        href="{{ route("admin.companies.surveys.survey-deliveries.create", [$company, $survey]) }}">
                                        <span class="dli-plus"></span>配信予約
                                    </a>
                                </p>
                            </div>
                        </div>

                        <div class="table-scroll-small mt-2">
                            <table class="table">
                                <tbody class="list">
                                    <tr class="list-title">
                                        <th>配信対象</th>
                                        <th>配信タイトル</th>
                                        <th>配信時間</th>
                                        <th>配信状態</th>
                                    </tr>

                                    @if ($surveyDeliveries->isEmpty())
                                        <tr class="no-link">
                                            <td>データがありません</td>
                                        </tr>
                                    @else
                                        @foreach ($surveyDeliveries as $surveyDelivery)
                                            <!--値-->
                                            <tr class="details-link"
                                                data-href="{{ route("admin.companies.surveys.survey-deliveries.show", [$company, $survey, $surveyDelivery]) }}">
                                                @switch($surveyDelivery->targetable_type)
                                                    @case(App\Models\Survey::class)
                                                        <td>未受診者</td>
                                                    @break

                                                    @case(App\Models\User::class)
                                                        <td>{{ $surveyDelivery->targetable->name }}</td>
                                                    @break

                                                    @default
                                                @endswitch
                                                <td class="">
                                                    {{ $surveyDelivery->subject }}
                                                </td>
                                                <td class="">
                                                    {{ $surveyDelivery->scheduled_sending_at->format("Y.m.d H:i") }}
                                                </td>
                                                <td class="del-area arrow-right">
                                                    @if ($surveyDelivery->completed_sending_at)
                                                        <p class="status incompletes">完了</p>
                                                    @else
                                                        <p class="status inactive">未配信</p>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>

            <div class="row mb40">
                <div class="col-md-12">
                    <div class="card pa24">

                        <div class="content_wrap">
                            <h1 class="content_title">診断リスト</h1>
                            <div class="content_action">
                                <form class="search-form" action="#">
                                    <form class="search-form"
                                        action="{{ route("admin.companies.surveys.show", [$company, $survey]) }}"
                                        method="GET">
                                        <label><input name="q" type="text" value="{{ request()->input("q") }}"
                                                placeholder="氏名検索"></label>
                                        <button type="submit" aria-label="検索"></button>
                                    </form>
                            </div>
                            <p class="button small_btn">
                                <a
                                href="{{ route("admin.companies.surveys.user.create", [$company, $survey]) }}">ユーザー追加</a>
                            </p>
                        </div>

                        <div class="table-scroll mt-2">
                            <table class="table">
                                <tbody class="list">
                                    <!--タイトル-->
                                    <tr class="list-title">
                                        <th style="width: 30%;">氏名</th>
                                        <th style="width: 15%;">チーム</th>
                                        <th style="width: 30%;">アドレス</th>
                                        <th style="width: 15%;">編集</th>
                                        <th class="pc">結果</th>
                                    </tr>

                                    @if ($surveyTargetUsers->isEmpty())
                                        <tr class="no-link">
                                            <td>データがありません</td>
                                        </tr>
                                    @else
                                        <tr class="no-link">
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                        </tr>

                                        @foreach ($surveyTargetUsers as $surveyTargetUser)
                                            <!--値-->
                                            <tr class="no-link">
                                                <td>
                                                    <strong>
                                                        <span class="table-user-name">{{ $surveyTargetUser->user->name }}</span><br class="pc"/><small>
                                                            {{ $surveyTargetUser->post }}</small>
                                                        @if ($surveyTargetUser->surveyAnswer->completes_at)
                                                            <img src="/assets/img/icon-complete.svg" alt=""
                                                                width="12px">
                                                        @endif
                                                    </strong>
                                                </td>
                                                <td>{{ $surveyTargetUser->team }}</td>
                                                <td class="email-cell">{{ $surveyTargetUser->user->email }}</td>
                                                <td>
                                                    <a href="{{ route("admin.companies.surveys.user.edit", [$company, $survey, $surveyTargetUser]) }}">
                                                        <img class="ml8" src="/assets/img/icon-edit.svg" alt="">
                                                    </a>
                                                </td>
                                                <td>
                                                    @if ($surveyTargetUser->surveyAnswer->completes_at)
                                                        <p class="button next-btn">
                                                            <a
                                                                href="{{ route("admin.companies.surveys.results", [$company, $survey, $surveyTargetUser]) }}">結果へ</a>
                                                        </p>
                                                    @else
                                                        <p title="診断未完了">未完了</p>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endif

                                </tbody>
                            </table>
                        </div>

                    </div>

                    {{ $surveyTargetUsers->links() }}

                </div>
            </div>

        </div>
    </div>
@endsection

@push("style")
    <style>
    </style>
@endpush

@push("script")
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
