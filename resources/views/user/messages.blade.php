@if ($errors->any())
<div class="messages alert alert-danger">
    <ul class="mb-0">
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

@if (session('success'))
<div class="messages alert alert-success">
    <ul class="mb-0">
        @foreach (session('success') as $message)
        <li> {{ $message }}</li>
        @endforeach
    </ul>
</div>
@endif

@push('style')
<style>
    .messages {
        margin: 20px auto;
    }
</style>
@endpush
