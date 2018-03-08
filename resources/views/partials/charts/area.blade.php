<!-- create container element for visualization -->
<div id="viz"></div>

@push('scripts')
    <script type="text/javascript">
        google.charts.load('current', {'packages':['corechart']});
        google.charts.setOnLoadCallback(drawChart);

        function drawChart() {
            var jsonData = $.ajax({
                url: "{{ $api }}",
                dataType: "json",
                async: false
            }).responseText;

            // Create our data table out of JSON data loaded from server.
            var data = new google.visualization.arrayToDataTable(JSON.parse(jsonData));

            var options = {
                title: 'Daily gametimes',
                vAxis: {
                    title: 'Time'
                },
                height: 300,
                isStacked: true
            };

            var chart = new google.visualization.SteppedAreaChart(document.getElementById('viz'));

            chart.draw(data, options);
        }
    </script>
@endpush