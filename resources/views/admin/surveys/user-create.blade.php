@extends('admin.layout')
@section('title', 'ユーザー作成')

@section('content')
<div class="main-panel">

    <div class="content">
        <div class="content_wrap">
            <h1 class="content_title">ユーザー作成</h1>
        </div>

        @include('admin.messages')

        <form action="{{ route('admin.companies.surveys.user.store', [$company, $survey]) }}" method="POST">
            @method('POST')
            @csrf
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <dl class="dlTable confirm">
                            <dt>診断名</dt>
                            <dd><p>{{ $survey->title }}</p></dd>
                            <dt>氏名</dt>
                            <dd><input type="text" name="name" value="{{ old('name') }}" placeholder="氏名を記載してください" maxlength="50" class="field"></dd>
                            <dt>役割</dt>
                            <dd><input type="text" name="post" value="{{ old('post') }}" placeholder="役割を記載してください" maxlength="50" class="field"></dd>
                            <dt>チーム名</dt>
                            <dd><input type="text" name="team" value="{{ old('team') }}" placeholder="チーム名を記載してください" maxlength="50" class="field"></dd>
                            <dt>アドレス</dt>
                            <dd><input type="text" name="email" value="{{ old('email') }}" placeholder="アドレスを記載してください" required maxlength="50" class="field"></dd>
                        </dl>
                    </div>
                </div>
            </div>
            <!--btn-area-->
            <div class="btn-area">
                <p class="button defalt-btn"><a href="{{ route('admin.companies.surveys.show', [$company, $survey]) }}">キャンセル</a></p>
                <p class="button next-btn"><input type="submit" class="submit" value="作成"></p>
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
