@extends('layout.app')

@section('content')
    <h2>Sankey Diagram for games played in the last 7 days</h2>
    <br><br>
    <!-- create container element for visualization -->
    <div id="viz"></div>

@endsection

@push('scripts')
    
    <!-- load D3js -->
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
    
    <script type="text/javascript">
        google.charts.load('current', {'packages': ['sankey']});
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {
            var data = new google.visualization.DataTable();
            data.addColumn('string', 'From');
            data.addColumn('string', 'To');
            data.addColumn('number', 'Hours');
            data.addRows([
                ['04-03-2018', 'CSGO', 5],
                ['05-03-2018', 'They Are Billions', 7],
                ['06-03-2018', 'They Are Billions', 6],
                ['07-03-2018', 'CSGO', 2],
                ['08-03-2018', 'CSGO', 9],
                ['08-03-2018', 'They Are Billions', 9],
                ['09-03-2018', 'Golf It', 4],
                ['09-03-2018', 'CSGO', 4],
                ['09-03-2018', 'They Are Billions', 4],
            ]);

            // Sets chart options.
            var options = {
                width: 1200,
                height: 700,
                sankey: {
                    node: {
                        nodePadding: 20,
                        width: 15,
                        label: {
                            fontSize: 22
                        }
                    },
                }
            };

            // Instantiates and draws our chart, passing in some options.
            var chart = new google.visualization.Sankey(document.getElementById('viz'));
            chart.draw(data, options);
        }
    </script>
@endpush