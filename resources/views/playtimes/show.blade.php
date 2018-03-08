@extends('layout.app')

@section('content')
    
    <!-- Page Content -->
    
    <div class="content-section-b">
        
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <h1>Playtime Requests</h1>
                    
                    @include('partials.charts.area', [
                        'api' => route('api.charts.area'),
                    ])
                    
                    <table class="table">
                        <thead>
                        <td>Logo</td>
                        <td>Name</td>
                        <td>Delta</td>
                        </thead>
                        
                        <tbody>
                        
                        @foreach($playtimes as $playtime)
                            <tr>
                                <td><img src="{{ \App\SteamAPI::mapImageUrl($playtime->gameInfo->appid, $playtime->gameInfo->logo) }}"></td>
                                <td>{{ $playtime->gameInfo->name }}</td>
                                <td>{{ round ($playtime->total / 60, 1) }} hours</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        
        </div>
        <!-- /.container -->
    
    </div>
    <!-- /.content-section-a -->
@endsection