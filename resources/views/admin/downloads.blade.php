@extends('admin.layout')
@section('title', '診断結果ダウンロード')

@section('content')
<div class="main-panel">

    <div class="content">
        <div class="content_wrap">
            <h1 class="content_title">診断結果ダウンロード</h1>
        </div>

        @include('admin.messages')


        <div class="row">
            <div class="col-md-12">

                <form action="{{ route('admin.downloads.by-company') }}" method="POST">
                    @csrf
                    <div class="card mb-3">
                        <dl class="dlTable">
                            <dt>企業ごとの回答結果をダウンロード</dt>
                            <dd class="select flex">
                                <select class="select" name="company_id" required>
                                    <option hidden>選択してください</option>

                                    @foreach($companies as $company)
                                        <option value="{{ $company->id }}">{{ $company->name }}</option>
                                    @endforeach
                                </select>
                                <p class="button small_btn">
                                    <input type="submit" value="ダウンロード">
                                </p>
                            </dd>
                        </dl>
                    </div>
                </form>

                <form action="{{ route('admin.downloads.by-period') }}" method="POST">
                    @csrf
                    <div class="card mb-3">
                        <dl class="dlTable">
                            <dt>回答期間ごとの回答結果をダウンロード<span class="note">期間は最大120日になります</span></dt>
                            <dd class="flex">

                                <input type="date" name="starts_at" value="yyyy-mm-dd" required>
                                <p class="separete">―</p>
                                <input type="date" name="expires_at" value="yyyy-mm-dd" required>

                                <p class="button small_btn">
                                    <input type="submit" value="ダウンロード">
                                </p>
                            </dd>
                        </dl>
                    </div>
                </form>

                <form action="{{ route('admin.downloads.by-survey') }}" method="POST">
                    @csrf
                    <div class="card">
                        <dl class="dlTable">
                            <dt>診断ごとの回答結果をダウンロード</dt>
                            <dd class="select flex">
                                <select class="select" name="survey_id" required>
                                    <option hidden>選択してください</option>

                                    @foreach($surveys as $survey)
                                        <option value="{{ $survey->id }}">{{ $survey->company->name }} - {{ $survey->title }}</option>
                                    @endforeach
                                </select>
                                <p class="button small_btn">
                                    <input type="submit" value="ダウンロード">
                                </p>
                            </dd>
                        </dl>
                    </div>
                </form>

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
</script>
@endpush
