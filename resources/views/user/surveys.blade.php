@extends('user.layout')
@section('title', '受診診断一覧')

@section('content')
<div class="main-panel">

    <div class="content">
        <div class="content_wrap">
            <h1 class="content_title">受診診断  一覧</h1>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">

                    <div class="card-body">
                        <table class="table">
                            <tbody class="list">
                                <tr class="list-title">
                                    <th>No.</th>
                                    <th>診断名</th>
                                    <th>締切日</th>
                                    <th>受診状況</th>
                                </tr>

                                @foreach($surveys as $survey)

                                    <tr class="details-link" data-href="{{ route('user.targets', $survey) }}">
                                        <td>{{ $survey->id }}</td>
                                        <td>{{ $survey->title }}</td>
                                        <td class="tdwidth">{{ $survey->expires_at->format('Y.m.d') }}</td>
                                        <td class="tdwidth">
                                            @if ($survey->survey_completed_count === $survey->survey_total_count)
                                                <p class="status incompletes">受診済</p>
                                            @else
                                                <p class="status">未受診 {{ $survey->survey_completed_count }}/{{ $survey->survey_total_count }}</p>
                                            @endif
                                        </td>
                                        <td class="arrow-right"></td>
                                    </tr>

                                @endforeach

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{ $surveys->links() }}
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
