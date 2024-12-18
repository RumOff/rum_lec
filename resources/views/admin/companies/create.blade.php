@extends('admin.layout')
@section('title', '企業情報 - 登録')

@section('content')
<div class="main-panel">

    <div class="content">
        <div class="content_wrap">
            <h1 class="content_title">企業情報 - 登録</h1>
        </div>

        @include('admin.messages')

        <form action="{{ route('admin.companies.store') }}" method="POST">
            @csrf

            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <dl class="dlTable confirm">
                            <dt>企業名</dt>
                            <dd><input type="text" class="field" name="name" value="{{ old('name') }}" placeholder="〇〇〇株式会社" required maxlength="50"></dd>

                            <dt>業種</dt>
                            <dd>
                                <select name="industry" class="field" required>
                                    <option value="">選択してください</option>
                                    @foreach(App\Models\Company::$industries as $key => $value)
                                        <option value="{{ $key }}" {{ old('industry') == $key ? 'selected' : '' }}>{{ $value }}</option>
                                    @endforeach
                                </select>
                            </dd>

                            <dt>契約開始日</dt>
                            <dd><input type="date" class="field" name="contract_start_date" value="{{ old('contract_start_date') }}" required></dd>

                            <dt>契約終了日</dt>
                            <dd><input type="date" class="field" name="contract_end_date" value="{{ old('contract_end_date') }}" required></dd>
                        </dl>
                    </div>
                </div>
            </div>

            <!--btn-area-->
            <div class="btn-area">
                <p class="button defalt-btn"><a href="{{ route('admin.companies.index') }}">キャンセル</a></p>
                <p class="button next-btn"><input type="submit" class="submit" value="登録"></p>
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
