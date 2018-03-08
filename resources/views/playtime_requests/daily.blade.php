@extends('layout.app')

@section('content')
    
    <!-- Page Content -->
    
    <div class="content-section-b">
        
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <h1>Playtime Requests</h1>
                    <table class="table">
                        <thead>
                            <td>Date</td>
                            <td>Request Count</td>
                            <td>Total</td>
                            <td>Requests</td>
                            <td>View</td>
                        </thead>
                        
                        <tbody>
                        
                        @forelse($days as $day => $info)
                            <tr>
                                <td>{{ $day }}</td>
                                <td>{{ $info['count'] }}</td>
                                <td>{{ round($info['total'] / 60, 1) }} hours</td>
                                <td>
                                    <a href="{{ route('playtime_requests.index', ['date' => $day]) }}">Requests</a>
                                </td>
                                <td>
                                    <a href="{{ route('playtimes.show', ['date' => $day]) }}">View</a>
                                </td>
                            </tr>
                        @empty
                            <h1>No request or not logged in</h1>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        
        </div>
        <!-- /.container -->
    
    </div>
    <!-- /.content-section-a -->
@endsection