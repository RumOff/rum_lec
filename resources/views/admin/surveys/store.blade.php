@extends('admin.layout')
@section('title', '新規診断 - 登録完了')

@section('content')
<div class="main-panel">
    <div class="content">

        <div class="sticky">
            <div class="content_wrap">
                <h1 class="content_title">新規診断 - 登録完了</h1>
            </div>
        </div>

        @include('admin.messages')

        <!--list-->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <dl class="dlTable complete">
                        <dt><img src="/assets/img/icon-complete.svg" alt=""></dt>
                        <dd>{{ $userCount }} 人の受診者 の登録が完了しました。</dd>
                    </dl>

                </div>
            </div>
        </div>

        <!--btn-area-->
        <div class="btn-area">
            <p class="button next-btn"><a href="{{ route('admin.companies.show', $company) }}">企業詳細へ</a></p>
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
