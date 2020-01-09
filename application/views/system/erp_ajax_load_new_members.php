<?php
$primaryLanguage = getPrimaryLanguage();
$this->lang->load('dashboard', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
?>
<style>
    .users-list > li img {
        max-width: 50% !important;
    }

    .users-list > li {
        width: 20% !important;
    }
</style>
<div class="box box-info">
    <div class="box-header with-border">
        <h4 class="box-title"><?php echo $this->lang->line('dashboard_new_members');?><!--New Members--></h4>
        <div class="box-tools pull-right">
            <!--<button type="button" class="btn btn-box-tool" data-widget="collapse"><i
                    class="fa fa-minus"></i>
            </button>-->
            <ul class="pagination pagination-sm inline">
                <?php
                $countofmem = count($newmembersRest) / 10;
                $pages = ceil($countofmem);
                $x = 1;
                while ($x <= $pages) {
                    if ($x == 1) {
                        ?>
                        <li onclick="load_new_members_list_view();return false;"><a href="#"><?php echo $x ?></a></li>
                        <?php

                    }else{
                        ?>
                        <li onclick="load_remaining_members_list_view(<?php echo $x-1 ?>);return false;"><a href="#"><?php echo $x ?></a></li>
                <?php
                    }
                    $x++;
                }
                ?>
            </ul>
        </div>
        <!-- /.box-tools -->
    </div>
    <!-- /.box-header -->
    <div class="box-body" style="display: block;width: 100%">
        <ul class="users-list clearfix" id="newmwmberview_<?php echo $userDashboardID ?>">

        </ul>
    </div>
    <div class="overlay" id="overlay11<?php echo $userDashboardID; ?>"><i class="fa fa-refresh fa-spin"></i></div>
    <!-- /.box-body -->
</div>


<script>
    load_new_members_list_view();

    function load_new_members_list_view() {
        var id = 0;
        $.ajax({
            type: 'POST',
            dataType: 'html',
            url: "<?php echo site_url('Finance_dashboard/load_new_members_list_view'); ?>",
            data: {autoId: id},
            cache: false,
            beforeSend: function () {
                //startLoad();
                $("#overlay1<?php echo $userDashboardID; ?>").show();
            },
            success: function (data) {
                //stopLoad();
                $("#overlay1<?php echo $userDashboardID; ?>").hide();
                $("#newmwmberview_<?php echo $userDashboardID ?>").html(data);
            },
            error: function () {
                $("#overlay1<?php echo $userDashboardID; ?>").hide();
                //stopLoad();
                //myAlert('e', 'Message: ' + "Error");
            }
        });
    }

    function load_remaining_members_list_view(pageid) {
        $.ajax({
            type: 'POST',
            dataType: 'html',
            url: "<?php echo site_url('Finance_dashboard/load_remaining_members_list_view'); ?>",
            data: {pageId: pageid},
            cache: false,
            beforeSend: function () {
                //startLoad();
                $("#overlay1<?php echo $userDashboardID; ?>").show();
            },
            success: function (data) {
                //stopLoad();
                $("#overlay1<?php echo $userDashboardID; ?>").hide();
                $("#newmwmberview_<?php echo $userDashboardID ?>").html(data);
            },
            error: function () {
                $("#overlay1<?php echo $userDashboardID; ?>").hide();
                //stopLoad();
                //myAlert('e', 'Message: ' + "Error");
            }
        });
    }

</script>
