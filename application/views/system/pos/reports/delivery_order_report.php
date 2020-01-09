<?php
echo head_page('<i class="fa fa-truck"></i>  Delivery Orders', false);
$locations = load_pos_location_drop();


$primaryLanguage = getPrimaryLanguage();
$this->lang->load('pos_config_restaurant', $primaryLanguage);
$this->lang->load('common', $primaryLanguage);
$this->lang->load('calendar', $primaryLanguage);

?>
<link rel="stylesheet" type="text/css" href="<?php echo base_url('plugins/pos/pos.css') ?>">
<link href='<?php echo base_url('plugins/fullcalender/lib/cupertino/jquery-ui.min.css'); ?>' rel='stylesheet'/>
<link href='<?php echo base_url('plugins/fullcalender/fullcalendar.min.css'); ?>' rel='stylesheet'/>
<link href='<?php echo base_url('plugins/fullcalender/fullcalendar.print.min.css'); ?>' rel='stylesheet' media='print'/>
<script type="text/javascript" src="<?php echo base_url('plugins/fullcalender/fullcalendar.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo base_url('plugins/printJS/jQuery.print.js'); ?>"></script>
<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <button class="btn btn-sm btn-glass btn-glass-purple pull-right" type="button"
                onclick="$.print('#crm_calendar')">
            <i class="fa fa-print"></i> Print
        </button>
    </div>
</div>
<section class="content" id="ajax_body_container">
    <div id="dashboard_content">
        <div class="row">
            <div class="col-md-12">
                <div id='crm_calendar'></div>
            </div>
        </div>
</section>

<div aria-hidden="true" role="dialog" tabindex="2" id="rpos_print_template" class="modal" data-keyboard="true"
     data-backdrop="static">
    <div class="modal-dialog modal-responsive-bill">
        <div class="modal-content">

            <div class="modal-header posModalHeader">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i
                        class="fa fa-close text-red"></i></button>
                <h4 class="modal-title"><?php echo $this->lang->line('common_print'); ?><!--Print--> </h4>
            </div>
            <div class="modal-body modal-responsive-bill" id="pos_modalBody_posPrint_template">

            </div>

            <div class="modal-footer" style="margin-top: 0px;">
                <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"
                        style="width:100%; display:block; font-size:12px; text-decoration: none; text-align:center; color:#FFF; background-color:#005b8a; border:0px solid #007FFF; padding: 10px 1px; margin: 5px auto 10px auto; font-weight:bold;">
                    <i class="fa fa-angle-double-left" aria-hidden="true"></i>
                    Close
                </button>
            </div>
        </div>
    </div>
</div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>
<script type="text/javascript">
    $(document).ready(function () {


        $('#crm_calendar').fullCalendar({
            header: {
                left: 'prev, next today',
                center: 'title',
                right: 'month, agendaWeek, agendaDay, listWeek'
            },
            defaultDate: new Date(),
            navLinks: true, // can click day/week names to navigate views
            editable: true,
            eventLimit: true, // allow "more" link when too many events
            events: {
                url: '<?php echo site_url('Pos_delivery/allCalenderEvents'); ?>',
                data: function () {
                    return {
                        category: null,
                        status: null
                    };
                },
                type: "POST",
                cache: false
            },
            dayClick: function (date) {
                /*                swal({
                 title: "Are you sure?",
                 text: "You want to create a task!",
                 type: "warning",
                 showCancelButton: true,
                 confirmButtonColor: "#00A65A",
                 confirmButtonText: "Create Task"
                 },
                 function () {
                 fetchPage('system/crm/create_new_task', '', 'Create Task', 2, date.format());
                 });*/
            },
            eventRender: function (event, element) {
                element.find(".fc-content").click(function () {
                    viewEvent(event.invoiceID);
                });
            }

        });
    });

    function viewEvent(invoiceID) {
        if (invoiceID > 0) {
            $.ajax({
                type: 'POST',
                dataType: 'html',
                url: "<?php echo site_url('Pos_restaurant/loadPrintTemplate'); ?>",
                data: {invoiceID: invoiceID},
                cache: false,
                beforeSend: function () {
                    startLoad();
                },
                success: function (data) {
                    stopLoad();
                    $('#rpos_print_template').modal('show');
                    $("#pos_modalBody_posPrint_template").html(data);
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    stopLoad();
                }
            });
        } else {
            myAlert('e', 'Load the invoice and click again.')
        }
    }


</script>