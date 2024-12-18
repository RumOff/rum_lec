<!DOCTYPE html>
<!-- saved from url=(0063)https://stellarluce.versus.jp/nomura-demo/respondent/login.html -->
<html lang="ja">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>回答者ログイン | {{ config('app.name') }}</title>
    <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no" name="viewport">
    <!-- CSS Files -->
    <link rel="icon" href="/assets/favicon.svg" type="image/svg+xml">
    <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/css/style.css" rel="stylesheet">
    <style>
    footer {
        text-align: center;
        margin: 60px 0 24px;
        color: #8080807d;
    }
    .personal-info-check {
        text-align: center;
    }
    .personal-info-check input {
        width: auto;
    }
    .personal-info-check label {
        cursor: pointer;
    }
    .personal-info-check a {
        text-decoration: underline;
    }
    </style>
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
                        <div class="card">
                            <h2 class="content_title">ログイン</h2>

                            <form method="POST" action="{{ route('user.login') }}">
                                @csrf
                                @include('user.messages')

                                <dl class="dlTable">
                                    <dt>メールアドレス</dt>
                                    <dd><input type="text" name="email" maxlength="255" required></dd>
                                    <dt>パスワード</dt>
                                    <dd>
                                        <div class="password-wrapper">
                                            <input type="password" class="password__input" id="password" name="password" maxlength="24" required>
                                            <button type="button" class="password__toggle"></button>
                                        </div>
                                    </dd>
                                </dl>

                                <div class="personal-info-check">
                                    <input type="checkbox" id="personal-info" required class="mr-2">
                                    <label for="personal-info"><a href="https://new-one.co.jp/privacypolicy_2/" target="_blank">個人情報の取り扱い</a>に同意する</label>
                                </div>

                                <div class="btn-area bottom">
                                    <p class="button next-btn"><button type="submit" class="submit">ログイン</button></p>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <footer>
        <p>Copyright©{{ today()->format('Y') }} NEWONE ,Inc All rights Reserved.</p>
    </footer>

    <script src="/assets/js/core/jquery.min.js"></script>
    <script>
        const passwordToggle = document.querySelector('.password__toggle')

        passwordToggle.addEventListener('click', (e) => {
            const input = e.target.previousElementSibling
            const type = input.getAttribute('type')
            input.setAttribute('type', type === 'password' ? 'text' : 'password')
            passwordToggle.classList.toggle('is-visible')
        })
    </script>
</body>
</html>
