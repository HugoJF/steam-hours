@extends('layout.app')

@section('content')
    
    <!-- Page Content -->
    
    <div class="content-section-b">
        
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    @if(isset($title))
                        <h1>{{ $title }}</h1>
                    @else
                        <h1>Playtime Requests</h1>
                    @endif
                    <table class="table">
                        <thead>
                        <td>Date</td>
                        <td>Deltas</td>
                        <td>Count</td>
                        <td>Filled</td>
                        <td>Cached</td>
                        <td>Date</td>
                        <td>Link</td>
                        </thead>
                        
                        <tbody>
                        
                        @forelse($playtimeRequests as $request)
                            <tr>
                                <td>{{ $request->created_at }}</td>
                                <td>{{ round($request->playtimeDeltas->sum('delta') / 60, 1) }} hours</td>
                                <td>{{ $request->playtimeDeltas->count() }} games</td>
                                <td>{{ $request->filled ? 'True' : 'False' }}</td>
                                <td>{{ $request->cached ? 'True' : 'False' }}</td>
                                <td>{{ $request->created_at->diffForHumans() }}</td>
                                <td><a href="{{ route('playtime_requests.show', $request) }}">View</a></td>
                            </tr>
                        @empty
                            <h1>No request or not logged in</h1>
                        @endforelse
                        </tbody>
                    </table>
                    <div class="text-center">
                        {{ $playtimeRequests->links() }}
                    </div>
                </div>
            </div>
        
        </div>
        <!-- /.container -->
    
    </div>
    <!-- /.content-section-a -->
@endsection