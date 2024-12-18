@extends("admin.layout")
@section("title", "企業情報 - 編集")

@section("content")
    <div class="main-panel">

        <div class="content">
            <p class="back-link"><a class="arrow-right left" href="{{ route("admin.companies.show", [$company]) }}">戻る</a></p>

            <div class="content_wrap">
                <h1 class="content_title">企業情報 - 編集</h1>
                <form action="{{ route("admin.companies.destroy", $company) }}" method="POST"
                    onSubmit="return confirm('削除してよろしいですか？')">
                    @method("DELETE")
                    @csrf
                    @if ($currentAdmin->is_superadmin)
                        <input id="company-delete-button-{{ $company->id }}" src="/assets/img/icon-del.svg" type="image"
                            alt="送信する" style="border: none">
                    @endif
                </form>
            </div>

            @include("admin.messages")

            <form action="{{ route("admin.companies.update", $company) }}" method="POST">
                @method("PATCH")
                @csrf

                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <dl class="dlTable confirm">
                                <dt>企業名</dt>
                                <dd><input class="field" name="name" type="text"
                                        value="{{ old("name", $company->name) }}" placeholder="〇〇〇株式会社" required
                                        maxlength="50"></dd>

                                <dt>業種</dt>
                                <dd>
                                    <select class="field" name="industry" required>
                                        <option value="">選択してください</option>
                                        @foreach (App\Models\Company::$industries as $key => $value)
                                            <option value="{{ $key }}"
                                                {{ old("industry", $company->industry) == $key ? "selected" : "" }}>
                                                {{ $value }}</option>
                                        @endforeach
                                    </select>
                                </dd>

                                <dt>契約開始日</dt>
                                <dd><input class="field" name="contract_start_date" type="date"
                                        value="{{ old("contract_start_date", $company->contract_start_date->format("Y-m-d")) }}"
                                        required></dd>

                                <dt>契約終了日</dt>
                                <dd><input class="field" name="contract_end_date" type="date"
                                        value="{{ old("contract_end_date", $company->contract_end_date->format("Y-m-d")) }}"
                                        required></dd>
                            </dl>
                        </div>
                    </div>
                </div>

                <!--btn-area-->
                <div class="btn-area">
                    <p class="button defalt-btn"><a href="{{ route("admin.companies.index") }}">キャンセル</a></p>
                    <p class="button next-btn">
                        <input class="submit" type="submit" value="更新">
                    </p>
                </div>
            </form>
        </div>
    </div>
@endsection

@push("style")
    <style>
    </style>
@endpush

@push("script")
    <script></script>
@endpush
