<div>

    <div id="lastSevenDaysGraph" style="min-width: 500px; height: 400px; margin: 0 auto"></div>
</div>
<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('pos_restaurent', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);
?>
<script>
    $(document).ready(function (e) {
        Highcharts.chart('lastSevenDaysGraph', {
            chart: {
                type: 'areaspline'
            },
            title: {
                text: '<?php echo $this->lang->line('posr_salesytd_average');?>'
            },/*Sales YTD Average*/
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
                    $begin = new DateTime("6 days ago");
                    $end = new DateTime(date('Y-m-d') - "1 d");
                    $interval = new DateInterval('P1D'); // 1 Day interval
                    $period = new DatePeriod($begin, $interval, $end); // 7 Days

                    $sale_data = array($this->lang->line('cal_sunday'));
                    $key = '';
                    foreach ($period as $day) {
                        //$key .= '\''.$day->format('l') . '\', ';
                        $key .= '\''.$this->lang->line( strtolower(  'cal_'.$day->format('l') ) ) . '\', ';
                    }
                    $clean = rtrim($key, ", ");
                    echo $clean;
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
                name: '<?php echo $this->lang->line('posr_last_7_days_actual');?>',/*Last 7 days actual*/
                color: '#1abc9c', /*1abc9c*/
                data: [<?php if (isset($sales_SevenDays)) {
                    $i = 1;
                    foreach ($sales_SevenDays as $key => $val) {
                        echo number_format($val['amount'], 2, '.', '');
                        if ($i < 7) {
                            echo ',';
                        }
                        $i++;
                    }

                }?>]
            }, {
                name: '<?php echo $this->lang->line('posr_average_for_the_year');?>',/*Average for the year*/
                color: '#297776', /*297776*/
                data: [<?php if (isset($sales_YTD)) {
                    $i = 1;

                    foreach ($sales_YTD as $key => $val) {
                        $dayCount = $sales_YTD_dayCount[$key];
                        echo $dayCount != 0 ? ($val / $dayCount) : 0.1;
                        //echo $val;
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