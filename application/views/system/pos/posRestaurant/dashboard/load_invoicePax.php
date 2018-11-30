<?php /** ------------------------------  new Style --------------------------- */
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('pos_restaurent', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);
?>
<div class="row" style="margin-top:20px;">
    <div class="col-md-3 col-sm-6">
        <div class="white-box">
            <div class="r-icon-stats">
                <i class="ti-stats-up bg-danger"></i>
                <div class="bodystate">
                    <h4 id="pax_yesterday">370</h4>
                    <span class="text-muted"><?php // echo $this->lang->line('posr_yesterday_pax');?>Yesterday Bills</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="white-box">
            <div class="r-icon-stats">
                <i class="ti-wallet bg-info"></i>
                <div class="bodystate">
                    <h4 id="pax_wtd">342</h4>
                    <span class="text-muted"><?php // echo $this->lang->line('posr_wtd_Pax');?>WTD Bills</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="white-box">
            <div class="r-icon-stats">
                <i class="fa fa-area-chart bg-warning"></i>
                <div class="bodystate">
                    <h4 id="pax_mtd">13</h4>
                    <span class="text-muted"><?php // echo $this->lang->line('posr_wtd_Pax');?>MTD Bills</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="white-box">
            <div class="r-icon-stats">
                <i class="fa fa-bar-chart bg-inverse"></i>
                <div class="bodystate">
                    <h4 id="pax_ytd">34650</h4>
                    <span class="text-muted"><?php // echo $this->lang->line('posr_ytd_pax');?>YTD Bills</span>
                </div>
            </div>
        </div>
    </div>
</div>
<script>

    var paxList = {
        useEasing : true,
        useGrouping : true,
        separator : ',',
        decimal : '.',
        prefix : '',
        suffix : ''
    };
    var pax = new CountUp("pax_yesterday", 0, <?php echo $yesterday?>, 0, 2.5, paxList);
    var pax2 = new CountUp("pax_wtd", 0, <?php echo $WTD?>, 0, 2.5, paxList);
    var pax3 = new CountUp("pax_mtd", 0, <?php echo $MTD?>, 0, 2.5, paxList);
    var pax4 = new CountUp("pax_ytd", 0, <?php echo $YTD?>, 0, 2.5, paxList);
    pax.start();
    pax2.start();
    pax3.start();
    pax4.start();
</script>