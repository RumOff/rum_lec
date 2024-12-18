@extends("admin.layout")
@section("title", "企業情報一覧")

@section("content")
    <div class="main-panel">

        <div class="content">
            <div class="content_wrap">
                <h1 class="content_title">企業情報一覧</h1>
                <div class="content_action">
                    <form class="search-form" action="{{ route("admin.companies.index") }}" method="GET">
                        <label><input name="q" type="text" value="{{ request()->input("q") }}"
                                placeholder="企業名検索"></label>
                        <button type="submit" aria-label="検索"></button>
                    </form>
                    @if ($currentAdmin->is_superadmin)
                        <p class="button small_btn"><a href="{{ route("admin.companies.create") }}"><span
                                    class="dli-plus"></span>新規企業登録</a></p>
                    @endif
                </div>
            </div>

            @include("admin.messages")

            <div class="row">
                <div class="col-md-12">
                    <div class="card">

                        <div class="card-body">
                            @if ($companies->isNotEmpty())
                                <table class="table">
                                    <tbody class="list">
                                        <tr class="list-title">
                                            <th style="width: 70px">ID</th>
                                            <th>企業名</th>
                                        </tr>

                                        @foreach ($companies as $company)
                                            <tr class="details-link"
                                                data-href="{{ route("admin.companies.show", $company) }}">
                                                <td>{{ $company->id }}</td>
                                                <td>{{ $company->name }}</td>
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
                                            <th>企業名</th>
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

            {{ $companies->links() }}
        </div>
    </div>
@endsection

@push("style")
    <style>
    </style>
@endpush

@push("script")
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
