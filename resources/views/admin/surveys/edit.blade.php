@extends('admin.layout')
@section('title', '診断編集 - ' . $survey->title)

@section('content')
<div class="main-panel">

    <div class="content">
        <div class="content_wrap">
            <h1 class="content_title">診断編集 - {{ $survey->title }}</h1>

            <form action="{{ route('admin.companies.surveys.destroy', [$company, $survey]) }}" method="POST" onSubmit="return confirm('削除してよろしいですか？')">
                @method('DELETE')
                @csrf
                <input type="image" src="/assets/img/icon-del.svg" alt="送信する" style="border: none"  id="survey-delete-button-{{ $survey->id }}">
            </form>
        </div>

        @include('admin.messages')

        <form action="{{ route('admin.companies.surveys.update', [$company, $survey]) }}" method="POST">
            @method('PATCH')
            @csrf

            <div class="row">
                <div class="col-md-12">
                    <div class="card">

                        <dl class="dlTable confirm">

                            <dt>診断名</dt>
                            <dd><input type="text" name="title" value="{{ old('title') ?? $survey->title }}" placeholder="診断名を記載してください" required maxlength="50" class="field"></dd>

                            <dt>診断実施期間</dt>
                            <dd>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="starts_at">開始日:</label>
                                        <input type="date" name="starts_at" value="{{ old('starts_at') ?? $survey->starts_at->format('Y-m-d') }}" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="expires_at">終了日:</label>
                                        <input type="date" name="expires_at" value="{{ old('expires_at') ?? $survey->expires_at->format('Y-m-d') }}" required>
                                    </div>
                                </div>
                            </dd>

                            {{-- <dt>
                                診断結果の閲覧

                                <div class="tooltip3">
                                    <p><img class="ml8" src="/assets/img/icon-hatena.svg" alt=""></p>
                                    <div class="description3">
                                        <p class="font-weight-normal">「ON」にすると受診者が自身の診断結果を閲覧できるようになります</p>
                                    </div>
                                </div>
                                <br>
                                <small>締切後に可能</small>
                            </dt> --}}
                            {{-- <dd>
                                <div class="ml40 switchArea">
                                    <input type="checkbox" id="open_results" name="open_results" value="1" @checked(old('open_results', $survey->open_results))>
                                    <label for="open_results"><span></span></label>
                                    <div id="swImg"></div>
                                </div>
                            </dd> --}}


                        </dl>

                    </div>
                </div>
            </div>

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
