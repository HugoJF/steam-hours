@extends('layout.app')

@section('content')
    @include('partials.charts.area', [
        'api' => $api,
    ])
@endsection
