<!DOCTYPE html>
<!-- saved from url=(0063)https://stellarluce.versus.jp/nomura-demo/respondent/login.html -->
<html lang="ja">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>全体診断結果 - {{ $survey->title }} | {{ config('app.name') }}</title>
    <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no" name="viewport">
    <!-- CSS Files -->
    <link rel="icon" href="/assets/favicon.svg" type="image/svg+xml">
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/style.css?{{ today()->format('YmdHis') }}" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .main-panel {
            float: none;
            width: 100%;
        }
        .main-panel>.content {
            padding: 0;
            width: 800px;
        }
    </style>
</head>

<body class="list">
    <header></header>

    <div class="wrapper">
        @include('admin.surveys.results-all-partial')
    </div>

    <script src="/assets/js/core/jquery.min.js"></script>
</body>
</html>
