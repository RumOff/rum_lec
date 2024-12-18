@extends("admin.layout")
@section("title", "管理者編集 - " . $admin->name)

@section("content")
    <div class="main-panel">
        <div class="content">
            <div class="content_wrap">
                <h1 class="content_title">管理者編集 - {{ $admin->name }}</h1>

                @if ($currentAdmin->is_superadmin)
                    <form action="{{ route("admin.admins.destroy", $admin) }}" method="POST"
                        onSubmit="return confirm('削除してよろしいですか？')">
                        @method("DELETE")
                        @csrf
                        <input id="admin-delete-button-{{ $admin->id }}" src="/assets/img/icon-del.svg" type="image"
                            alt="送信する" style="border: none">
                    </form>
                @endif
            </div>

            @include("admin.messages")

            <form action="{{ route("admin.admins.update", $admin) }}" method="POST">
                @method("PATCH")
                @csrf

                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <dl class="dlTable confirm">
                                <dt>管理者名</dt>
                                <dd><input class="field" name="name" type="text"
                                        value="{{ old("name") ?? $admin->name }}" placeholder="" required maxlength="20"
                                        {{ $currentAdmin->id !== $admin->id ? "readonly" : "" }}>
                                </dd>

                                <dt>メールアドレス</dt>
                                <dd><input class="field" name="email" type="email"
                                        value="{{ old("email") ?? $admin->email }}" placeholder="" required maxlength="100"
                                        {{ $currentAdmin->id !== $admin->id ? "readonly" : "" }}>
                                </dd>
                                @if ($currentAdmin->id === $admin->id)
                                    <dt>新しいパスワード<br><span class="tag required">変更時のみ入力</span></dt>
                                    <dd>
                                        <div class="password-wrapper">
                                            <input class="password__input" name="password" type="password">
                                            <span class="password__toggle"></span>
                                        </div>
                                    </dd>
                                @endif
                                <dt>Super管理者権限</dt>
                                <dd>
                                    <label>
                                        <input name="is_superadmin" type="checkbox" value="1"
                                            {{ $admin->is_superadmin ? "checked" : "" }} {{ !$currentAdmin->is_superadmin ? "disabled" : "" }}>
                                    </label>
                                </dd>
                            </dl>
                        </div>
                    </div>
                </div>

                <!--btn-area-->
                <div class="btn-area">
                    <p class="button defalt-btn"><a href="{{ route("admin.admins.index") }}">戻る</a></p>
                    <p class="button next-btn"><input class="submit" type="submit" value="変更"></p>
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
    <script>
        const passwordToggle = document.querySelector('.password__toggle')

        passwordToggle.addEventListener('click', (e) => {
            const input = e.target.previousElementSibling
            const type = input.getAttribute('type')
            input.setAttribute('type', type === 'password' ? 'text' : 'password')
            passwordToggle.classList.toggle('is-visible')
        })
    </script>
@endpush
