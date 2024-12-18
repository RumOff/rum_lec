@extends('user.layout')
@section('title', '受診完了')

@section('content')
<div class="main-panel">
    <div class="content">

        <div class="sticky">
            <div class="content_wrap">
                <h1 class="content_title">受診完了</h1>
                <p class="date">受診日：{{ $surveyAnswer->completes_at->format('Y/m/d') }}</p>
            </div>
            <div class="content_action save_wrap">

                <p class="patient_name font-weight-bold">
                    {{ $target->team }}　{{ $target->targetUser->name }}
                    @if ($target->user_id === $target->target_user_id)
                    （本人）
                    @endif
                </p>

            </div>

        </div>

        <!--list-->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <dl class="dlTable complete">
                        <dt><img src="/assets/img/icon-complete.svg" alt=""></dt>
                        <dd>すべての回答が完了しました！<br>お疲れ様でした！</dd>
                    </dl>

                </div>
            </div>
        </div>

        <!--btn-area-->
        <div class="btn-area">
            <p class="button next-btn"><a href="{{ route('user.targets', $survey) }}">受診一覧へ</a></p>
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
</script>
@endpush
