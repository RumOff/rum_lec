@extends('admin.layout')
@section('title', '診断検索')

@section('content')
<div class="main-panel">

    <div class="content">
        <div class="content_wrap">
            <h1 class="content_title">診断検索</h1>
        </div>

        @include('admin.messages')

        <form action="{{ route('admin.users.searchs') }}" method="GET">
            @csrf

            <div class="row">
                <div class="col-md-12">
                    <div class="card">

                        <dl class="dlTable confirm">

                            <dt>企業名</dt>
                            <dd class="select">
                                <select class="content_title-date" name="company_id">
                                    <option></option>
                                        @foreach($companies as $company)
                                            <option value="{{ $company->id }}" @selected((int)(request()->input('company_id')) === $company->id)>{{ $company->name }}</option>
                                        @endforeach
                                </select>
                            </dd>

                            <dt>
                                被評価者の<br>
                                氏名 / メールアドレス
                            </dt>
                            <dd><input type="text" name="_q" value="{{ request()->input('_q') ?? '' }}" placeholder="氏名 / メールアドレス" maxlength="100" class="field"></dd>

                        </dl>

                    </div>
                </div>
            </div>

            <!--btn-area-->
            <div class="btn-area">
                <p class="button next-btn"><input type="submit" class="submit" value="検索"></p>
            </div>
        </form>

        <div class="row">
            <div class="col-md-12">
                <div class="card">

                    <div class="card-body">
                        @if ($users->isNotEmpty())
                            <table class="table">
                                <tbody class="list">
                                    <tr class="list-title">
                                        <th style="max-width: 300px">企業名</th>
                                        <th>被評価者名</th>
                                        <th></th>
                                    </tr>

                                    @foreach($users as $user)
                                        <tr class="details-link" data-href="{{ route('admin.users.surveys', $user) }}">
                                            <td>{{ $user->company->name }}</td>
                                            <td>{{ $user->name }}</td>
                                            <td class="arrow-right"></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <table class="table">
                                <tbody class="list">
                                    <tr class="list-title">
                                        <th style="max-width: 300px">企業名</th>
                                        <th>被評価者名</th>
                                    </tr>

                                    <tr class="details-link">
                                        <td class="text-muted">データはありません</td>
                                        <td></td>
                                    </tr>
                                </tbody>
                            </table>
                        @endif
                    </div>

                </div>
            </div>
        </div>

        {{ $users->links() }}
    </div>
</div>
@endsection

@push('style')
<style>
</style>
@endpush

@push('script')
<script>
    //テーブルのtrにリンクを付ける
    jQuery(function($) {
        $('.details-link[data-href]').addClass('clicktable').click(function() {
            window.location = $(this).attr('data-href');
        }).find('a').hover(function() {
            $(this).parents('.details-link').unbind('click');
        }, function() {
            $(this).parents('.details-link').click(function() {
                window.location = $(this).attr('data-href');
            });
        });
    });

</script>
@endpush
