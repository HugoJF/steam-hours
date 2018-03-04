@extends('layout.app')

@section('content')
    
    <!-- create container element for visualization -->
    <div id="viz"></div>

@endsection

@push('scripts')
    
    <!-- load D3js -->
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

    <script type="text/javascript">
        google.charts.load('current', {'packages':['treemap']});
        google.charts.setOnLoadCallback(drawChart);
        function drawChart() {
            var rawx = [
                ['Location', 'Parent', 'Market trade volume (size)', 'Market increase/decrease (color)'],
                ['Global',    null,                 0,                               0],
                ['America',   'Global',             0,                               0],
                ['Europe',    'Global',             0,                               0],
                ['Asia',      'Global',             0,                               0],
                ['Australia', 'Global',             0,                               0],
                ['Africa',    'Global',             0,                               0],
                ['Brazil',    'America',            11,                              10],
                ['USA',       'America',            52,                              31],
                ['Mexico',    'America',            24,                              12],
                ['Canada',    'America',            16,                              -23],
                ['France',    'Europe',             42,                              -11],
                ['Germany',   'Europe',             31,                              -2],
                ['Sweden',    'Europe',             22,                              -13],
                ['Italy',     'Europe',             17,                              4],
                ['UK',        'Europe',             21,                              -5],
                ['China',     'Asia',               36,                              4],
                ['Japan',     'Asia',               20,                              -12],
                ['India',     'Asia',               40,                              63],
                ['Laos',      'Asia',               4,                               34],
                ['Mongolia',  'Asia',               1,                               -5],
                ['Israel',    'Asia',               12,                              24],
                ['Iran',      'Asia',               18,                              13],
                ['Pakistan',  'Asia',               11,                              -52],
                ['Egypt',     'Africa',             21,                              0],
                ['S. Africa', 'Africa',             30,                              43],
                ['Sudan',     'Africa',             12,                              2],
                ['Congo',     'Africa',             10,                              12],
                ['Zaire',     'Africa',             8,                               10]
            ];
            
            var datax = google.visualization.arrayToDataTable(rawx);


            var jsonData = $.ajax({
                url: "{{ route('api.pergame') }}",
                dataType: "json",
                async: false
            }).responseText;
            
            // Create our data table out of JSON data loaded from server.
            var data = new google.visualization.arrayToDataTable(JSON.parse(jsonData));

            tree = new google.visualization.TreeMap(document.getElementById('viz'));

            tree.draw(data, {
                maxDepth: 2,
                minColor: '#e7711c',
                midColor: '#fff',
                maxColor: '#4374e0',
                headerHeight: 15,
                height: 700,
                fontColor: 'black',
                showScale: true
            });

        }
    </script>
@endpush