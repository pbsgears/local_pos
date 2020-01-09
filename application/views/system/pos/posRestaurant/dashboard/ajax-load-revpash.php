<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('pos_restaurent', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);
?>
<div class="row">
    <div class="col-sm-6">
        <div class="info-box">
            <span class="info-box-icon bg-green-gradient"><i class="fa fa-calendar-o"></i></span>
            <div class="info-box-content">
                <span class="info-box-text" style="text-align: center;font-size: 1.3em;font-weight: 600;"><?php echo $this->lang->line('posr_yesterday_revpash');?> REVPASH<!--YESTERDAY --></span>
                <span class="info-box-number" style="text-align: center;font-size: 1.9em;"><span id="rp_yesterday"><?php echo $yesterday?></span></span>
            </div>
        </div>
    </div>

    <div class="col-sm-6">
        <div class="info-box">
            <span class="info-box-icon bg-aqua-gradient"><i class="fa fa-calendar-o"></i></span>
            <div class="info-box-content">
                <span class="info-box-text"
                      style="text-align: center;font-size: 1.3em;font-weight: 600;"><?php echo $this->lang->line('posr_wtd_revpash');?> REVPASH<!--WTD --></span>
                <span class="info-box-number" style="text-align: center;font-size: 1.9em;"><span id="rp_wtd"><?php //echo $WTD?></span></span>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-6">
        <div class="info-box">
            <span class="info-box-icon bg-light-blue-gradient"><i class="fa fa-calendar-o"></i></span>
            <div class="info-box-content">
                <span class="info-box-text"
                      style="text-align: center;font-size: 1.3em;font-weight: 600;"><?php echo $this->lang->line('posr_wtw_revpash');?><!--MTD--> REVPASH</span>
                <span class="info-box-number" style="text-align: center;font-size: 1.9em;"><span id="rp_mtd"><?php //echo $MTD?></span></span>
            </div>
        </div>
    </div>
    <div class="col-sm-6">
        <div class="info-box">
            <span class="info-box-icon bg-teal-gradient"><i class="fa fa-calendar-o"></i></span>
            <div class="info-box-content">
                <span class="info-box-text"
                      style="text-align: center;font-size: 1.3em;font-weight: 600;"><?php echo $this->lang->line('posr_ytd_revpash');?><!--YTD--> REVPASH</span>
                <span class="info-box-number" style="text-align: center;font-size: 1.9em;"><span id="rp_ytd"><?php //echo $YTD?></span></span>
            </div>
        </div>
    </div>
</div>

<script>

    var options = {
        useEasing : true,
        useGrouping : true,
        separator : ',',
        decimal : '.',
        prefix : '',
        suffix : ''
    };
    var demo = new CountUp("rp_yesterday", 0, <?php echo $yesterday?>, 0, 2.5, options);
    var demo2 = new CountUp("rp_wtd", 0, <?php echo $WTD?>, 0, 2.5, options);
    var demo3 = new CountUp("rp_mtd", 0, <?php echo $MTD?>, 0, 2.5, options);
    var demo4 = new CountUp("rp_ytd", 0, <?php echo $YTD?>, 0, 2.5, options);
    demo.start();
    demo2.start();
    demo3.start();
    demo4.start();
</script>