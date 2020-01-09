<div>

    <div id="lastSevenDaysGeneralGraph" style="min-width: 500px; height: 400px; margin: 0 auto"></div>

</div>
<script>
    $(document).ready(function (e) {
        Highcharts.chart('lastSevenDaysGeneralGraph', {
            chart: {
                type: 'areaspline'
            },
            title: {
                text: false
            },
            legend: {
                layout: 'horizontal',
                align: 'center',
                verticalAlign: 'bottom',
                floating: false,
                borderWidth: 1,
                backgroundColor: (Highcharts.theme && Highcharts.theme.legendBackgroundColor) || '#FFFFFF'
            },
            xAxis: {
                categories: [
                    <?php
                    $date = '';
                    foreach ($sales_SevenDays as $day) {
                        $date .= '"' . $day['invoiceDate'] . '",';
                    }
                    echo rtrim($date, ',');


                    ?>
                ],
                plotBands: [{ // visualize the weekend
                    from: 4.5,
                    to: 6.5,
                    color: 'white'
                }]
            },
            yAxis: {
                title: {
                    text: ''
                }
            },
            tooltip: {
                shared: true,
                valueSuffix: ' '
            },
            credits: {
                enabled: false
            },
            plotOptions: {
                areaspline: {
                    fillOpacity: 0.5
                }
            },
            series: [{
                name: 'Last 7 days actual',
                color: '#6690e4', /*1abc9c*/
                data: [<?php if (isset($sales_SevenDays)) {
                    $i = 1;
                    foreach ($sales_SevenDays as $val) {
                        echo number_format($val['totalSales'], 2, '.', '');
                        if ($i < 7) {
                            echo ',';
                        }
                        $i++;
                    }

                }?>]
            }]
        });
    });

</script>