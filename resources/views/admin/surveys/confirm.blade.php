@extends("admin.layout")
@section("title", "新規診断 - 登録確認")

@section("content")
    <div class="main-panel">

        <div class="content">
            <div class="content_wrap">
                <h1 class="content_title">新規診断 - 登録確認</h1>
            </div>

            @include("admin.messages")

            <form action="{{ route("admin.companies.surveys.store", $company) }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">

                            <dl class="dlTable confirm">
                                <dt>診断名</dt>
                                <dd><input class="field" name="title" type="text" value="{{ $title }}" required
                                        disabled></dd>

                                <dt>サーベイ種別</dt>
                                <dd><input class="field" type="text"
                                        value="{{ $surveyType === "specified" ? "回答者・所属指定" : "オープンアンケート" }}" disabled></dd>

                                <dt>診断実施期間</dt>
                                <dd>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label for="starts_at">開始日:</label>
                                            <input id="starts_at" name="starts_at" type="date"
                                                value="{{ $startsAt }}" required disabled>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="expires_at">終了日:</label>
                                            <input id="expires_at" name="expires_at" type="date"
                                                value="{{ $expiresAt }}" required disabled>
                                        </div>
                                    </div>
                                </dd>

                                <dt>フォームURL</dt>
                                <dd><input class="field" name="form_url" type="url" value="{{ $formUrl }}"
                                        required disabled>
                                </dd>

                                <dt>パスワード</dt>
                                <dd>
                                    <input class="field" name="form_password" type="text" value="{{ $formPassword }}"
                                        disabled>
                                </dd>
                            </dl>

                        </div>
                        {{-- <div class="card pa40">
                            <p class="mb8 font-weight-bold">カスタム設問CSV</p>
                            <div class="table-scroll">
                                <table class="result-list gray">
                                    <!--タイトル-->
                                    <thead>
                                        <tr>
                                            @foreach (\App\Models\SurveyQuestion::CSV_HEADER as $header)
                                                <th>{{ $header }}</th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($questionCsv as $row)
                                            <tr>
                                                @foreach (\App\Models\SurveyQuestion::CSV_HEADER as $header)
                                                    <td>{{ $row[$header] ?? "" }}</td>
                                                @endforeach
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div> --}}
                        <div class="card pa40">
                            <p class="mb8 font-weight-bold">対象者CSV</p>
                            <div class="table-scroll-middle">
                                <table class="result-list gray">
                                    <tbody>
                                        <!--タイトル-->
                                        <tr>
                                            @if ($surveyType === "open")
                                                @foreach (\App\Models\User::OPEN_CSV_HEADER as $header)
                                                    <th>{{ $header }}</th>
                                                @endforeach
                                            @else
                                                @foreach (\App\Models\User::SPECIFIED_CSV_HEADER as $header)
                                                    <th>{{ $header }}</th>
                                                @endforeach
                                            @endif
                                        </tr>

                                        <!--値-->
                                        @foreach ($userCsv as $row)
                                            <tr>
                                                @if ($surveyType === "open")
                                                    @foreach (\App\Models\User::OPEN_CSV_HEADER as $header)
                                                        <td>{{ $row[$header] ?? "" }}</td>
                                                    @endforeach
                                                @else
                                                    @foreach (\App\Models\User::SPECIFIED_CSV_HEADER as $header)
                                                        <td>{{ $row[$header] ?? "" }}</td>
                                                    @endforeach
                                                @endif
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <input name="title" type="hidden" value="{{ $title }}">
                <input name="survey_type" type="hidden" value="{{ $surveyType }}">
                <input name="starts_at" type="hidden" value="{{ $startsAt }}">
                <input name="expires_at" type="hidden" value="{{ $expiresAt }}">
                <input name="send_invitation_at" type="hidden" value="{{ $sendInvitationAt }}">
                <input name="send_reminder_at" type="hidden" value="{{ $sendReminderAt }}">
                <input name="form_url" type="hidden" value="{{ $formUrl }}">
                <input name="form_password" type="hidden" value="{{ $formPassword }}">
                <input name="email_subject" type="hidden" value="{{ $emailSubject }}">
                <input name="email_body" type="hidden" value="{{ $emailBody }}">
                <input name="user_path" type="hidden" value="{{ $userPath }}">

                <!--btn-area-->
                <div class="btn-area">
                    <p class="button defalt-btn"><a
                            href="{{ route("admin.companies.surveys.create", $company) }}">キャンセル</a></p>
                    <p class="button next-btn"><input class="submit" type="submit" value="登録"></p>
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
