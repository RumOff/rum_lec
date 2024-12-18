@extends('user.layout')
@section('title', '診断結果 - ' . $survey->title)

@push('style')
<style>
    .main-panel>.content {
        padding: 0;
        width: 800px;
    }
    @media print {
        .sidebar {
            display: none;
        }

        footer {
            margin: 0;
        }
    }
</style>
@endpush

@section('content')
    @include('user.results-partial')
@endsection
