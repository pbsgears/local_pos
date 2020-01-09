<?php
$compid = current_companyID();
$year = $this->db->query("select companyFinanceYearID,beginingDate,endingDate from srp_erp_companyfinanceyear WHERE companyID=$compid ")->result_array();
$curryear = $this->db->query("select companyFinanceYearID,beginingDate,endingDate from srp_erp_companyfinanceyear WHERE companyID=$compid and isCurrent=1")->row_array();

$primaryLanguage = getPrimaryLanguage();
$this->lang->load('dashboard', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);

?>
<div class="box box-info">
    <div class="box-header with-border">
        <h4 class="box-title"><?php echo $this->lang->line('dashboard_head_count');?><!--Head Count--></h4>
        <select class="pull-right" id="financeYear<?php echo $userDashboardID ?>" onchange="load_head_count_view<?php echo $userDashboardID ?>()">
           <!-- <option value="<?php /*echo $curryear['companyFinanceYearID'] */?>">Select Financial Year</option>-->
            <?php
            /*foreach ($year as $val) {
                if($val['companyFinanceYearID']==$curryear['companyFinanceYearID']){
                    */?><!--
                    <option value="<?php /*echo $val['companyFinanceYearID'] */?>" selected><?php /*echo $val['beginingDate'] */?>
                        / <?php /*echo $val['endingDate'] */?></option>
                    <?php
/*                }else{
                    */?>
                    <option value="<?php /*echo $val['companyFinanceYearID'] */?>"><?php /*echo $val['beginingDate'] */?>
                        / <?php /*echo $val['endingDate'] */?></option>
                    --><?php
/*                }
            }*/
            $d = date('Y-01-01');
            $date = strtotime($d.' -1 year');
            $dateFn1 = date('Y-m-d', $date);
            $dateFn2 = date('Y-12-31', $date);
            echo '<option value="currentYear"> '.date('Y-01-01').' - '. date('Y-12-31').'</option>';
            echo '<option value="lastYear">'.$dateFn1.' - '.$dateFn2.'</option>';
            ?>

        </select>
        <!-- /.box-tools -->
    </div>
    <!-- /.box-header -->
    <div class="box-body" style="display: block;width: 100%">
        <div id="headcount_<?php echo $userDashboardID ?>"></div>
    </div>
    <div class="overlay" id="overlay15<?php echo $userDashboardID; ?>"><i class="fa fa-refresh fa-spin"></i></div>
    <!-- /.box-body -->
</div>


<script>
    load_head_count_view<?php echo $userDashboardID ?>();




    function load_head_count_view<?php echo $userDashboardID ?>(){
        var financeyearid=$('#financeYear<?php echo $userDashboardID ?>').val();
        $.ajax({
            type: 'POST',
            dataType: 'html',
            url: "<?php echo site_url('Finance_dashboard/load_head_count_view'); ?>",
            data: {financeyearid: financeyearid,userDashboardID:<?php echo $userDashboardID ?>},
            cache: false,
            beforeSend: function () {
                startLoad();
            },
            success: function (data) {
                stopLoad();
                $("#headcount_<?php echo $userDashboardID ?>").html(data);
            },
            error: function () {
                stopLoad();
                //myAlert('e', 'Message: ' + "Error");
            }
        });
    }


</script>
