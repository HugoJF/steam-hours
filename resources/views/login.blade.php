@extends('layout.app')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <p class="text-center"><a href="{{ route('redirectToSteam') }}"><img src="{{ asset('images/steam.png') }}"></a></p>
        </div>
    </div>
@endsection