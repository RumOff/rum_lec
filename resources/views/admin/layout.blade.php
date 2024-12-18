<!DOCTYPE html>
<!-- saved from url=(0063)https://stellarluce.versus.jp/nomura-demo/respondent/login.html -->
<html lang="ja">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>@yield('title') | {{ config('app.name') }}</title>
    <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no" name="viewport">
    <!-- CSS Files -->
    <link rel="icon" href="/assets/favicon.svg" type="image/svg+xml">
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/style.css?{{ today()->format('YmdHis') }}" rel="stylesheet">

    @stack('style')
</head>

<body class="list">
    @include('admin.header')

    <div class="wrapper">
        @include('admin.sidebar')
        @yield('content')
    </div>

    <footer>
        <p>Copyright©{{ today()->format('Y') }} NEWONE ,Inc All rights Reserved.</p>
    </footer>

    <script src="/assets/js/core/jquery.min.js"></script>
    @stack('script')
</body>
</html>
