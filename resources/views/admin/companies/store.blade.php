@extends('admin.layout')
@section('title', '企業情報 - 登録完了')

@section('content')
<div class="main-panel">
    <div class="content">

        <div class="sticky">
            <div class="content_wrap">
                <h1 class="content_title">企業情報 - 登録完了</h1>
            </div>
        </div>

        @include('admin.messages')

        <!--list-->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <dl class="dlTable complete">
                        <dt><img src="/assets/img/icon-complete.svg" alt=""></dt>
                        <dd>登録が完了しました。</dd>
                    </dl>

                </div>
            </div>
        </div>

        <!--btn-area-->
        <div class="btn-area">
            <p class="button next-btn"><a href="{{ route('admin.companies.index') }}">一覧へ戻る</a></p>
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
