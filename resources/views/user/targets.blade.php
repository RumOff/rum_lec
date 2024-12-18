@extends('user.layout')
@section('title', "{$survey->title}　受診一覧")

@section('content')
<div class="main-panel">

    <div class="content">

        <p class="back-link"><a class="arrow-right left" href="{{ route('user.surveys') }}">戻る</a></p>

        <div class="content_wrap">
            <h1 class="content_title">{{ $survey->title }}　受診一覧</h1>
        </div>

        @include('user.messages')

        <div class="row">
            <div class="col-md-12">
                <div class="card">

                    <div class="card-body">
                        <table class="table">
                            <tbody class="list">
                                <tr class="list-title">
                                    <th>No.</th>
                                    <th>チーム名・部署名</th>
                                    <th>被評価者</th>
                                    <th>受診状況</th>
                                </tr>

                                @foreach($targets as $target)

                                    {{-- 受診済み --}}
                                    @if ($target->surveyAnswer->completes_at)

                                        {{-- 結果閲覧可能 --}}
                                        @if ($survey->open_results)
                                            <tr class="details-link" data-href="{{ route('user.results', $target->surveyAnswer) }}">
                                                <td>{{ $loop->index + 1 }}</td>
                                                <td>{{ $target->team }}</td>
                                                <td>
                                                    @if ($target->user_id === $target->target_user_id)
                                                        <span class="text-muted">本人</span>
                                                    @else
                                                        {{ $target->targetUser->name }}
                                                    @endif
                                                </td>
                                                    <td class="tdwidth">
                                                        <p class="status incompletes">受診済</p>
                                                    </td>
                                                    <td>
                                                        <p class="button next-btn"><a href="{{ route('user.results', $target->surveyAnswer) }}">診断結果</a></p>
                                                    </td>
                                                </td>
                                            </tr>

                                        {{-- 結果閲覧不可 --}}
                                        @else
                                            <tr class="details-link no-link">
                                                <td>{{ $loop->index + 1 }}</td>
                                                <td>{{ $target->team }}</td>
                                                <td>
                                                    @if ($target->user_id === $target->target_user_id)
                                                        <span class="text-muted">本人</span>
                                                    @else
                                                        {{ $target->targetUser->name }}
                                                    @endif
                                                </td>
                                                    <td class="tdwidth">
                                                        <p class="status incompletes">受診済</p>
                                                    </td>
                                                    <td>
                                                        <p class="button next-btn"><a style="opacity: 0;cursor: auto;">診断結果</a></p>
                                                    </td>
                                                </td>
                                            </tr>
                                        @endif

                                    {{-- 未受診 OR 一時中断 --}}
                                    @else
                                        <tr class="details-link" data-href="{{ route('user.questions', $target->surveyAnswer) }}">
                                            <td>{{ $loop->index + 1 }}</td>
                                            <td>{{ $target->team }}</td>
                                            <td>
                                                @if ($target->user_id === $target->target_user_id)
                                                    <span class="text-muted">本人</span>
                                                @else
                                                    {{ $target->targetUser->name }}
                                                @endif
                                            </td>
                                            <td class="tdwidth">
                                                @if ($target->surveyAnswer->starts_at)
                                                    <p class="status inactive">一時中断</p>
                                                @else
                                                    <p class="status">未受診</p>
                                                @endif
                                            </td>
                                            <td class="arrow-right"></td>
                                        </tr>
                                    @endif

                                @endforeach

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{ $targets->links() }}
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
    jQuery( function($) {
        $('.details-link[data-href]').addClass('clicktable').click( function() {
            window.location = $(this).attr('data-href');
        }).find('a').hover( function() {
            $(this).parents('.details-link').unbind('click');
        }, function() {
            $(this).parents('.details-link').click( function() {
                window.location = $(this).attr('data-href');
            });
        });
    });

    </script>
@endpush

