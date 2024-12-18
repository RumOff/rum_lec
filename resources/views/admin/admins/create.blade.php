@extends('admin.layout')
@section('title', '管理者 - 登録')

@section('content')
<div class="main-panel">

    <div class="content">
        <div class="content_wrap">
            <h1 class="content_title">管理者 - 登録</h1>
        </div>

        @include('admin.messages')

        <form action="{{ route('admin.admins.store') }}" method="POST">
            @csrf

            <div class="row">
                <div class="col-md-12">
                    <div class="card">

                        <dl class="dlTable confirm">

                            <dt>管理者名</dt>
                            <dd><input type="text" name="name" value="{{ old('name') ?? '' }}" placeholder="" required maxlength="20" class="field"></dd>

                            <dt>メールアドレス</dt>
                            <dd><input type="email" name="email" value="{{ old('email') ?? '' }}" placeholder="" required maxlength="100" class="field"></dd>

                            <dt>Super管理者権限</dt>
                            <dd>
                                <label>
                                    <input type="checkbox" name="is_superadmin" value="1" {{ old('is_superadmin') ? 'checked' : '' }}>
                                </label>
                            </dd>

                        </dl>

                    </div>
                </div>
            </div>

            <!--btn-area-->
            <div class="btn-area">
                <p class="button defalt-btn"><a href="{{ route('admin.admins.index') }}">キャンセル</a></p>
                <p class="button next-btn"><input type="submit" class="submit" value="登録"></p>
            </div>
        </form>

    </div>
</div>
@endsection

@push('style')
<style>
</style>
@endpush

@push('script')
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
