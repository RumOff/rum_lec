<!DOCTYPE html>
<!-- saved from url=(0063)https://stellarluce.versus.jp/nomura-demo/respondent/login.html -->
<html lang="ja">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>診断結果 - {{ $survey->title }} | {{ config("app.name") }}</title>
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no">
    <!-- CSS Files -->
    <link type="image/svg+xml" href="/assets/favicon.svg" rel="icon">
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/style.css?{{ today()->format("YmdHis") }}" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .main-panel {
            float: none;
            width: 100%;
        }

        @media screen and (min-width:767px) {
            .main-panel>.content {
                padding: 0;
                width: 800px;
            }
        }
    </style>
</head>

<body class="list">
    <header></header>

    <div class="wrapper wrapper-result">
        @include("user.results-partial")
    </div>

    <script src="/assets/js/core/jquery.min.js"></script>
</body>

</html>
