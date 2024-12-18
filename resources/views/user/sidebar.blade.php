<div class="sidebar">
    <div class="logo">

        <div class="simple-text logo-normal">
            <img src="/assets/img/logo-small-oseru.svg" alt="">
        </div>

    </div>
    <div class="sidebar-wrapper">
        <ul class="nav">
            <li class="name">
                <p class="small">{{ $user->company->name }}</p>
                <p class="large">{{ $user->name }}</p>
            </li>
            <li class="active">
                <a disabled="disabled">
                    <img src="/assets/img/icon-menu01.svg" alt="">
                    <p>受診診断</p>
                </a>
            </li>
            <li class="logout">
                <a href="{{ route('user.logout') }}">
                    <p>ログアウト</p>
                    <img src="/assets/img/icon-logout.svg" alt="">
                </a>
            </li>
        </ul>
    </div>
</div>
