@extends('admin.layout')
@section('title', 'ユーザー編集')

@section('content')
<div class="main-panel">

    <div class="content">
        <div class="content_wrap">
            <h1 class="content_title">ユーザー編集</h1>
            <form action="{{ route('admin.companies.surveys.user.destroy', [$company, $survey, $surveyTargetUser]) }}" method="POST" onSubmit="return confirm('削除してよろしいですか？')">
                @method('DELETE')
                @csrf
                <input type="image" src="/assets/img/icon-del.svg" alt="送信する" style="border: none"  id="survey-delete-button-{{ $survey->id }}">
            </form>
        </div>

        @include('admin.messages')

        <form action="{{ route('admin.companies.surveys.user.update', [$company, $survey, $surveyTargetUser]) }}" method="POST">
            @method('PATCH')
            @csrf
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <dl class="dlTable confirm">
                            <dt>診断名</dt>
                            <dd><p>{{ $survey->title }}</p></dd>
                            <dt>氏名</dt>
                            <dd><input type="text" name="name" value="{{ old('name') ?? $surveyTargetUser->user->name }}" placeholder="氏名を記載してください" maxlength="50" class="field"></dd>
                            <dt>役割</dt>
                            <dd><input type="text" name="post" value="{{ old('post') ?? $surveyTargetUser->post }}" placeholder="役割を記載してください" maxlength="50" class="field"></dd>
                            <dt>チーム名</dt>
                            <dd><input type="text" name="team" value="{{ old('team') ?? $surveyTargetUser->team }}" placeholder="チーム名を記載してください" maxlength="50" class="field"></dd>
                            <dt>アドレス</dt>
                            <dd><input type="text" name="email" value="{{ old('email') ?? $surveyTargetUser->user->email }}" placeholder="アドレスを記載してください" required maxlength="50" class="field"></dd>
                        </dl>
                    </div>
                </div>
            </div>
            <input type="text" name="userId" value={{$surveyTargetUser->user->id}} hidden>
            <!--btn-area-->
            <div class="btn-area">
                <p class="button defalt-btn"><a href="{{ route('admin.companies.surveys.show', [$company, $survey]) }}">キャンセル</a></p>
                <p class="button next-btn"><input type="submit" class="submit" value="変更"></p>
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
