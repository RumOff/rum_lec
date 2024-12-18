@extends('admin.layout')
@section('title', '設問リスト - ' . $survey->title)

@section('content')
<div class="main-panel">

    <div class="content">

        <p class="back-link"><a class="arrow-right left" href="{{ route('admin.companies.surveys.show', [$company, $survey]) }}">戻る</a></p>

        <div class="content_wrap">
            <h1 class="content_title">設問リスト - {{ $survey->title }}</h1>
        </div>

        @include('admin.messages')

            <div class="row">
                <div class="col-md-12">
                    <div class="card pa40">
                        <div class="table mt-3">
                            <table class="result-list gray">
                                <tbody>
                                    <!--タイトル-->
                                    <tr>
                                        @foreach (\App\Models\SurveyQuestion::CSV_HEADER as $header)
                                            <th>{{ $header }}</th>
                                        @endforeach
                                    </tr>

                                    <!--値-->
                                    @foreach($questions as $question)
                                        <tr>
                                            <td>{{ $question->sort }}</td>
                                            <td>{{ $question->major_category }}</td>
                                            <td>{{ $question->medium_category }}</td>
                                            <td>{{ $question->minor_category }}</td>
                                            <td>{{ $question->question_text }}</td>
                                            <td>{{ $question->answer_options }}</td>
                                        </tr>
                                    @endforeach

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        {{-- @endif --}}


    </div>
</div>
@endsection

@push('style')
<style>
    .width-70 {
        width: 70px;
    }
    .width-100 {
        width: 100px;
    }
    .width-150 {
        width: 100px;
    }
    .width-auto {
        width: auto;
    }
</style>
@endpush

@push('script')
<script>
</script>
@endpush
