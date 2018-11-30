
<div id="fastMovingItemChartContainer" style="min-width: 1090px; height: 400px; margin: 0 auto"></div>
<?php

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('pos_restaurent', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);
?>


<script>
    $(document).ready(function (e) {
        Highcharts.chart('fastMovingItemChartContainer', {
            chart: {
                type: 'column'
            },
            title: {
                text: '<?php echo $this->lang->line('posr_fast_moving_item_by_value');?> - <?php echo $this->lang->line('posr_top');?> <?php $count_result = count($productMix); echo $count_result; ?> (<?php echo $this->lang->line('posr_ytd_revpash');?>)'/*Fast moving item by value*//*Top*//*YTD*/
            },
            xAxis: {
                type: 'category',
                labels: {
                    rotation: -45,
                    style: {
                        fontSize: '11px',
                        fontFamily: 'Verdana, sans-serif',
                        color: '#000000'
                    }
                }
            },

            legend: {
                enabled: false
            },
            tooltip: {
                pointFormat: 'Item Sold {point.y:.0f}'
            },
            series: [{
                name: 'Population',
                color: '#FFC107',
                data: [
                    <?php
                    $grandTotal = 0;
                    if (!empty($productMix)) {
                        $i = 1;
                        foreach ($productMix as  $val) {


                            echo "['" . $val['menuMasterDescription'] . "', " . $val['qty'] . "]";
                            if ($count_result != $i) {
                                echo ',';
                            }
                            $i++;
                        }
                    }
                    ?>


                ],
                dataLabels: {
                    enabled: true,
                    rotation: 0, /*-90*/
                    color: '#000000',
                    align: 'right',
                    format: '{point.y:.0f}', // one decimal
                    y: 3, // 10 pixels down from the top
                    style: {
                        fontSize: '14px',
                        fontFamily: 'Arial'
                    }
                }
            }]
        });
    });

    /** $(document).ready(function (e) {
        Highcharts.chart('fastMovingItemChartContainer', {
            chart: {
                type: 'column'
            },
            title: {
                text: 'Fast moving item by value - Top <?php $count_result = count($result); echo $count_result; ?> (YTD)'
            },
            xAxis: {
                type: 'category',
                labels: {
                    rotation: -45,
                    style: {
                        fontSize: '11px',
                        fontFamily: 'Verdana, sans-serif',
                        color: '#000000 '
                    }
                }
            },

            legend: {
                enabled: false
            },
            tooltip: {
                pointFormat: 'Item Sold {point.y:.0f}'
            },
            series: [{
                name: 'Population',
                color: '#FFC107 ',
                data: [
                    <?php
                    if (!empty($result)) {
                        $i = 1;
                        foreach ($result as $item) {

                            echo "['" . $item['menuMasterDescription'] . "', " . $item['sumQty'] . "]";
                            if ($count_result != $i) {
                                echo ',';
                            }
                            $i++;
                        }
                    }
                    ?>


                ],
                dataLabels: {
                    enabled: true,
                    rotation: 0, /!*-90*!/
                    color: '#000000 ',
                    align: 'right',
                    format: '{point.y:.0f}', // one decimal
                    y: 3, // 10 pixels down from the top
                    style: {
                        fontSize: '14px',
                        fontFamily: 'Arial'
                    }
                }
            }]
        });
    });*/

</script>