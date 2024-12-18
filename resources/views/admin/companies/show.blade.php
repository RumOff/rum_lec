@extends("admin.layout")
@section("title", "企業情報 ： 詳細")

@section("content")
    <div class="main-panel">
        <div class="content">
            <p class="back-link"><a class="arrow-right left" href="{{ route("admin.companies.index") }}">戻る</a></p>
            <div class="content_wrap">
                <h1 class="content_title">企業情報 ： 詳細</h1>
            </div>
            <div class="content_action edit_wrap">
                <div class="company_info">
                    <h4 class="patient_name font-weight-bold">{{ $company->name }}</h4>
                    <dl class="company_details">
                        <dt>業種:</dt>
                        <dd>{{ App\Models\Company::$industries[$company->industry] ?? "未設定" }}</dd>

                        <dt>契約期間:</dt>
                        <dd>
                            @if ($company->contract_start_date && $company->contract_end_date)
                                {{ $company->contract_start_date->format("Y年m月d日") }} 〜
                                {{ $company->contract_end_date->format("Y年m月d日") }}
                            @else
                                未設定
                            @endif
                        </dd>
                    </dl>
                </div>
                <a href="{{ route("admin.companies.edit", $company) }}"><img class="ml8" src="/assets/img/icon-edit.svg"
                        alt=""></a>
            </div>
            @include("admin.messages")

            <!-- 担当者一覧エリア -->
            @if ($currentAdmin->is_superadmin)
                <div class="save_wrap mt-4">
                    <label class="keep save" id="js-draft-save" for="save">
                        <a href="{{ route("admin.companies.admins.assign", $company) }}">担当者管理</a>
                    </label>
                </div>
            @endif
            <div class="table_wrap">
                <div class="company_info">
                    @if ($companyAdmins->isNotEmpty())
                        <table class="table">
                            <tbody class="list">
                                <tr class="list-title">
                                    <th>名前</th>
                                    <th>メールアドレス</th>
                                </tr>
                                @foreach ($companyAdmins as $companyAdmin)
                                    <tr  class="no-link">
                                        <td>{{ $companyAdmin->name }}</td>
                                        <td>{{ $companyAdmin->email }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="text-muted">担当者がまだ登録されていません。</p>
                    @endif

                </div>
            </div>
            <div class="save_wrap">
                <label class="keep save" id="js-draft-save" for="save"><a
                        href="{{ route("admin.companies.surveys.create", $company) }}">新規診断登録</a></label>
            </div>
            <!--list-->
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            @if ($surveys->isNotEmpty())
                                <table class="table">
                                    <tbody class="list">
                                        <tr class="list-title">
                                            <th style="width: 70px">ID</th>
                                            <th>診断名</th>
                                            <th>実施期間</th>
                                        </tr>

                                        @foreach ($surveys as $survey)
                                            <tr class="details-link"
                                                data-href="{{ route("admin.companies.surveys.show", [$company, $survey]) }}">
                                                <td>{{ $survey->id }}</td>
                                                <td>{{ $survey->title }}</td>
                                                <td>{{ $survey->starts_at->format("Y.m.d") }} -
                                                    {{ $survey->expires_at->format("Y.m.d") }}</td>
                                                <td class="arrow-right pc"></td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <table class="table">
                                    <tbody class="list">
                                        <tr class="list-title">
                                            <th style="width: 70px">ID</th>
                                            <th>診断名</th>
                                            <th>締切日</th>
                                        </tr>

                                        <tr class="details-link">
                                            <td></td>
                                            <td class="text-muted">データはありません</td>
                                            <td></td>
                                            <td></td>
                                        </tr>
                                    </tbody>
                                </table>
                            @endif
                        </div>
                    </div>

                </div>
            </div>
            {{ $surveys->links() }}

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
