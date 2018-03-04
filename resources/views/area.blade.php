@extends('layout.app')

@section('content')
    
    <!-- create container element for visualization -->
    <div id="viz"></div>

@endsection

@push('scripts')
    
    <!-- load D3js -->
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

    <script type="text/javascript">
        google.charts.load('current', {'packages':['corechart']});
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {
            // var data = google.visualization.arrayToDataTable([
            //     ['Director (Year)',  'Rotten Tomatoes', 'IMDB'],
            //     ['Alfred Hitchcock (1935)', 8.4,         7.9],
            //     ['Ralph Thomas (1959)',     6.9,         6.5],
            //     ['Don Sharp (1978)',        6.5,         6.4],
            //     ['James Hawes (2008)',      4.4,         6.2]
            // ]);


            var jsonData = $.ajax({
                url: "{{ route('api.perday') }}",
                dataType: "json",
                async: false
            }).responseText;

            // Create our data table out of JSON data loaded from server.
            var data = new google.visualization.arrayToDataTable(JSON.parse(jsonData));

            var options = {
                title: 'Daily gametimes',
                vAxis: {title: 'Time'},
                height: 700,
                isStacked: true
            };

            var chart = new google.visualization.SteppedAreaChart(document.getElementById('viz'));

            chart.draw(data, options);
        }
    </script>
@endpush