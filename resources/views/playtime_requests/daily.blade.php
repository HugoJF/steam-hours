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
                        <td>Total</td>
                        </thead>
                        
                        <tbody>
                        
                        @forelse($days as $day=> $total)
                            <tr>
                                <td>{{ $day }}</td>
                                <td>{{ round($total / 60, 2) }} hours</td>
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