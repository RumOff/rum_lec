@extends('admin.layout')
@section('title', '管理者一覧')

@section('content')
<div class="main-panel">

    <div class="content">
        <div class="content_wrap">
            <h1 class="content_title">管理者一覧</h1>
            <div class="content_action">
                <p class="button small_btn"><a href="{{ route('admin.admins.create') }}"><span class="dli-plus"></span>管理者登録</a></p>
            </div>
        </div>

        @include('admin.messages')

        <div class="row">
            <div class="col-md-12">
                <div class="card">

                    <div class="card-body">
                        @if ($admins->isNotEmpty())
                            <table class="table">
                                <tbody class="list">
                                    <tr class="list-title">
                                        <th style="width: 70px">ID</th>
                                        <th>管理者名</th>
                                        <th>権限</th>
                                    </tr>

                                    @foreach($admins as $admin)
                                        <tr class="details-link" data-href="{{ route('admin.admins.edit', $admin) }}">
                                            <td>{{ $admin->id }}</td>
                                            <td>{{ $admin->name }}</td>
                                            <td><small>{{ $admin->is_superadmin ? '管理者' : ''  }}</small></td>
                                            <td class="arrow-right"></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <table class="table">
                                <tbody class="list">
                                    <tr class="list-title">
                                        <th style="width: 70px">ID</th>
                                        <th>管理者名</th>
                                    </tr>

                                    <tr class="details-link">
                                        <td></td>
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

        {{ $admins->links() }}
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
