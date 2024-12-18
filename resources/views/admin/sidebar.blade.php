<div class="sidebar-overlay"></div>

<div class="sidebar">
    <div class="logo">

        <div class="simple-text logo-normal">
            <img src="/assets/img/logo-small-oseru.svg" alt="">
        </div>

    </div>
    <div class="sidebar-wrapper">
        <ul class="nav">
            <li class="name">
                <p class="small">株式会社NEWONE</p>
                <p class="large">{{ $user->name }}</p>
            </li>
            <li @class(["active" => request()->route()->named("admin.companies*")])>
                <a href="{{ route("admin.companies.index") }}">
                    <img src="/assets/img/icon-menu01.svg" alt="">
                    <p>企業情報一覧</p>
                </a>
            </li>
            <li @class(["active" => request()->route()->named("admin.downloads*")])>
                <a href="{{ route("admin.downloads.index") }}">
                    <img src="/assets/img/icon-menu02.svg" alt="">
                    <p>診断結果DL</p>
                </a>
            </li>
            {{-- <li @class(["active" => request()->route()->named("admin.users.searchs")])>
                <a href="{{ route("admin.users.searchs") }}">
                    <img src="/assets/img/icon-menu03.svg" alt="">
                    <p>診断検索</p>
                </a>
            </li> --}}
            @if (isset($currentAdmin) && $currentAdmin->is_superadmin)
                <li @class(["active" => request()->route()->named("admin.admins*")])>
                    <a href="{{ route("admin.admins.index") }}">
                        <img src="/assets/img/icon-menu04.svg" alt="">
                        <p>管理者一覧</p>
                    </a>
                </li>
            @elseif(isset($currentAdmin) && !$currentAdmin->is_superadmin)
                <li @class(["active" => request()->route()->named("admin.admins*")])>
                    <a href="{{ route("admin.admins.edit", $currentAdmin) }}">
                        <img src="/assets/img/icon-menu04.svg" alt="">
                        <p>管理者情報</p>
                    </a>
                </li>
            @endif

            <li class="logout">
                <a href="{{ route("admin.logout") }}">
                    <p>ログアウト</p>
                    <img src="/assets/img/icon-logout.svg" alt="">
                </a>
            </li>
        </ul>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const menuButton = document.querySelector('.mobile-menu-button');
        const sidebar = document.querySelector('.sidebar');
        const overlay = document.querySelector('.sidebar-overlay');

        menuButton.addEventListener('click', function() {
            menuButton.classList.toggle('active');
            sidebar.classList.toggle('active');
            overlay.classList.toggle('active');
        });

        overlay.addEventListener('click', function() {
            menuButton.classList.remove('active');
            sidebar.classList.remove('active');
            overlay.classList.remove('active');
        });
    });
</script>
