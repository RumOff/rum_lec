@extends('user.layout')
@section('title', '診断')

@section('content')
<div class="main-panel">
    <div class="content">

        <form action="{{ route('user.completes', $surveyAnswer) }}" method="POST" id="survey-answers">
            @csrf
            <div class="sticky">
                <div class="content_wrap">
                    <h1 class="content_title">診断開始</h1>
                    <p class="date">受診日：{{ now()->format('Y/m/d') }}</p>
                </div>
                <div class="content_action save_wrap">

                    <p class="patient_name font-weight-bold">
                        {{ $target->team }}　{{ $target->targetUser->name }}
                        @if ($target->user_id === $target->target_user_id)
                        （本人）
                        @endif
                    </p>

                    <div class="save_wrap">
                        <label class="keep save" for="save" id="js-draft-save">保存して中断</label>
                    </div>
                </div>

            </div>

            <!--list-->
            <div class="row">
                <div class="col-md-12">

                    @foreach ($surveyAnswerDetails as $detail)
                        <div class="consultation-wrap">

                            <div class="consultation-wrap_title">
                                @if ($target->user_id === $target->target_user_id)
                                    Q{{ $loop->index + 1 }}.{{ Str::replace('$user_name', 'ご自身', $detail->surveyQuestion->question) }}
                                @else
                                    Q{{ $loop->index + 1 }}.{{ Str::replace('$user_name', "{$target->targetUser->name}さん", $detail->surveyQuestion->question) }}
                                @endif
                            </div>

                            @if ($detail->surveyQuestion->type === 'text')
                                <div>
                                    <textarea name="{{ $detail->id }}" id="{{ $detail->id }}" cols="30" rows="10" placeholder="ここに回答を記載してください" required>{{ $detail->text }}</textarea>
                                </div>
                            @else
                                <div class="consultation-wrap_img">
                                    <ul class="radio-area-survey">
                                        @foreach ($scores as $choices)
                                        <li class="radio-wrap">
                                            <input
                                                class="visually-hidden"
                                                type="radio"
                                                name="{{ $detail->id }}"
                                                id="{{ $detail->id }}_{{ $choices }}"
                                                value="{{ $choices }}"
                                                @required(true)
                                                @checked($detail->score === $choices)
                                                >
                                            <label for="{{ $detail->id }}_{{ $choices }}"></label>
                                        </li>
                                        @endforeach
                                    </ul>
                                </div>
                                <div class="consultation-wrap_text">
                                    <span>全くそう思わない（1）</span><span>とてもそう思う（5）</span>
                                </div>
                            @endif

                        </div>
                    @endforeach

                </div>
            </div>

            <!--btn-area-->
            <div class="btn-area">
                <div class="button next-btn">
                    <input type="submit" class="submit" value="診断を完了する">
                </div>
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
    $(function() {
        $('#js-draft-save').on('click', function() {
            var fm = $('#survey-answers');
            fm.append($('<input />', {
                type: 'hidden',
                name: 'draft',
                value: true
            }));
            fm.appendTo(document.body);
            fm.submit();
            fm.remove();
        });
    })
</script>
@endpush
