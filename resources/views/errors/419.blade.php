<!DOCTYPE html>
<!-- saved from url=(0063)https://stellarluce.versus.jp/nomura-demo/respondent/login.html -->
<html lang="ja">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>419 セキュリティーエラー | {{ config('app.name') }}</title>
    <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no" name="viewport">
    <!-- CSS Files -->
    <link rel="icon" href="/assets/favicon.svg" type="image/svg+xml">
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/style.css" rel="stylesheet">

</head>

<body class="login">
    <div class="wrapper">

        <div class="main-panel onecolmn">

            <div class="content">
                <!--title-->
                <h1 class="content_title"><img src="/assets/img/logo-small-oseru.svg" alt=""></h1>
                <!--list-->
                <div class="row">
                    <div class="col-md-12">
                        <div class="card" style="margin-top: 40px;">
                            <h2 class="content_title" style="margin-top: 40px">セキュリティーエラーが起きました</h2>
                            <p>重複送信や同一ページの長時間表示があったため、セキュリティーの観点からエラーになりました。</p>
                        </div>
                    </div>
                </div>

                <div class="btn-area">
                    @if (request()->is('admin*'))
                        <p class="button next-btn"><a href="{{ route('admin.companies.index') }}">TOPへ</a></p>
                    @else
                        <p class="button next-btn"><a href="/">TOPへ</a></p>
                    @endif
                </div>

            </div>
        </div>
    </div>

    <footer>
        <p>Copyright©{{ today()->format('Y') }} NEWONE ,Inc All rights Reserved.</p>
    </footer>
</body>
</html>
