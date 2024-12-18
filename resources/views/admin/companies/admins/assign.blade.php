@extends("admin.layout")
@section("title", "新規担当者割り当て")

@section("content")
    <div class="main-panel">
        <div class="content">
            <p class="back-link"><a class="arrow-right left" href="{{ route("admin.companies.show", $company) }}">戻る</a></p>
            <div class="content_wrap">
                <h1 class="content_title">新規担当者割り当て</h1>
            </div>

            @include("admin.messages")

            <div class="row">
                <div class="col-md-12">
                    <form action="{{ route("admin.companies.admins.store", $company->id) }}" method="POST">
                        @csrf
                        <div class="content_wrap">
                            <small>現在の担当者</small>
                        </div>
                        <div class="table_wrap">
                            <table class="table">
                                <tbody class="list">
                                    <tr class="list-title">
                                        <th style="width: 70px">ID</th>
                                        <th>名前</th>
                                        <th>メールアドレス</th>
                                        <th style="text-align: center">選択</th>
                                    </tr>
                                    @foreach ($assignedAdmins as $admin)
                                        <tr>
                                            <td>{{ $admin->id }}</td>
                                            <td>{{ $admin->name }}</td>
                                            <td>{{ $admin->email }}</td>
                                            <td>
                                                <input name="admin_ids[]" type="checkbox" value="{{ $admin->id }}"
                                                    checked>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="content_wrap">
                            <small>その他の管理者</small>
                        </div>
                        <div class="table_wrap long">
                            <table class="table">
                                <tbody class="list">
                                    <tr class="list-title">
                                        <th style="width: 70px">ID</th>
                                        <th>名前</th>
                                        <th>メールアドレス</th>
                                        <th style="text-align: center">選択</th>
                                    </tr>
                                    @foreach ($otherAdmins as $admin)
                                        @if (!$admin->is_superadmin)
                                            <tr>
                                                <td>{{ $admin->id }}</td>
                                                <td>{{ $admin->name }}</td>
                                                <td>{{ $admin->email }}</td>
                                                <td>
                                                    <input name="admin_ids[]" type="checkbox" value="{{ $admin->id }}">
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="btn-area">
                            <p class="button next-btn"><input class="submit" type="submit" value="更新"></p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push("style")
    <style>
        /* ここにカスタムスタイルを追加可能 */
    </style>
@endpush
