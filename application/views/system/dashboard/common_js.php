<script>
    var position<?php echo $userDashboardID ?> = [];
    $(document).ready(function () {
        getAssignedWidget<?php echo $userDashboardID ?>();
    });

    function filter<?php echo $userDashboardID ?>() {
        $.each(position<?php echo $userDashboardID ?>, function (index, item) {
            window[item.functionName+<?php echo $userDashboardID ?>](item.position);
        });
    }

    function getAssignedWidget<?php echo $userDashboardID ?>() {
        $.ajax({
            async: true,
            type: 'POST',
            dataType: 'json',
            data: {userDashboardID:<?php echo $userDashboardID ?>},
            url: "<?php echo site_url('Finance_dashboard/fetch_assigned_dashboard_widget'); ?>",
            beforeSend: function () {
                //startLoad();
            },
            success: function (data) {
                //stopLoad();
                $.each(data.dashboardWidget, function (index, item) {
                    //alert(item.functionName+"('"+ item.position +"')");
                    window[item.functionName+<?php echo $userDashboardID ?>](item.position+<?php echo $userDashboardID ?>);
                });
            }, error: function () {

            }
        })
    }


    function load_overall_performance<?php echo $userDashboardID ?>(id) {
        var target = "load_overall_performance";
        var result = $.grep(position<?php echo $userDashboardID ?>, function (e) {
            return e.functionName == target;
        });

        if (result.length == 0) {
            position<?php echo $userDashboardID ?>.push({functionName: "load_overall_performance", position: id});
        }

        $.ajax({
            async: true,
            type: 'POST',
            dataType: 'html',
            data: {'period': $('#period<?php echo $userDashboardID; ?>').val(),userDashboardID:<?php echo $userDashboardID; ?>},
            url: "<?php echo site_url('Finance_dashboard/load_overall_performance'); ?>",
            beforeSend: function () {
                $("#overlay1<?php echo $userDashboardID; ?>").show();
            },
            success: function (data) {
                $("#" + id).html(data);
                $("#overlay1<?php echo $userDashboardID; ?>").hide();
            }, error: function () {

            }
        });
    }

    function load_revenue_detail_analysis<?php echo $userDashboardID ?>(id) {
        var target = "load_revenue_detail_analysis";
        var result = $.grep(position<?php echo $userDashboardID ?>, function (e) {
            return e.functionName == target;
        });

        if (result.length == 0) {
            position<?php echo $userDashboardID ?>.push({functionName: "load_revenue_detail_analysis", position: id});
        }
        $.ajax({
            async: true,
            type: 'POST',
            dataType: 'html',
            data: {'period': $('#period'+<?php echo $userDashboardID; ?>).val(),userDashboardID:<?php echo $userDashboardID; ?>},
            url: "<?php echo site_url('Finance_dashboard/load_revenue_detail_analysis'); ?>",
            beforeSend: function () {
                $("#overlay2<?php echo $userDashboardID; ?>").show();
            },
            success: function (data) {
                $("#" + id).html(data);
                $("#overlay2<?php echo $userDashboardID; ?>").hide();
            }, error: function () {

            }
        });
    }

    function load_performance_summary<?php echo $userDashboardID ?>(id) {
        var target = "load_performance_summary";
        var result = $.grep(position<?php echo $userDashboardID ?>, function (e) {
            return e.functionName == target;
        });

        if (result.length == 0) {
            position<?php echo $userDashboardID ?>.push({functionName: "load_performance_summary", position: id});
        }
        $.ajax({
            async: true,
            type: 'POST',
            dataType: 'html',
            data: {'period': $('#period'+<?php echo $userDashboardID; ?>).val(),userDashboardID:<?php echo $userDashboardID; ?>},
            url: "<?php echo site_url('Finance_dashboard/load_performance_summary'); ?>",
            beforeSend: function () {
                $("#overlay3<?php echo $userDashboardID; ?>").show();
            },
            success: function (data) {
                $("#" + id).html(data);
                $("#overlay3<?php echo $userDashboardID; ?>").hide();
            }, error: function () {

            }
        });
    }


    function load_overdue_payable_receivable<?php echo $userDashboardID ?>(id) {
        $.ajax({
            async: true,
            type: 'POST',
            dataType: 'html',
            data: {userDashboardID:<?php echo $userDashboardID; ?>},
            url: "<?php echo site_url('Finance_dashboard/load_overdue_payable_receivable'); ?>",
            beforeSend: function () {
            },
            success: function (data) {
                $("#" + id).html(data);
            }, error: function () {

            }
        });
    }

    function load_fast_moving_item<?php echo $userDashboardID ?>(id) {
        var target = "load_fast_moving_item";
        var result = $.grep(position<?php echo $userDashboardID ?>, function (e) {
            return e.functionName == target;
        });

        if (result.length == 0) {
            position<?php echo $userDashboardID ?>.push({functionName: "load_fast_moving_item", position: id});
        }
        $.ajax({
            async: true,
            type: 'POST',
            dataType: 'html',
            data: {userDashboardID:<?php echo $userDashboardID; ?>},
            url: "<?php echo site_url('Finance_dashboard/load_fast_moving_item'); ?>",
            beforeSend: function () {
            },
            success: function (data) {
                $("#" + id).html(data);
            }, error: function () {

            }
        });
    }

    function load_postdated_cheque<?php echo $userDashboardID ?>(id) {
        $.ajax({
            async: true,
            type: 'POST',
            dataType: 'html',
            data: {userDashboardID:<?php echo $userDashboardID; ?>},
            url: "<?php echo site_url('Finance_dashboard/load_postdated_cheque'); ?>",
            beforeSend: function () {
            },
            success: function (data) {
                $("#" + id).html(data);
            }, error: function () {

            }
        });
    }

    function load_financial_position<?php echo $userDashboardID ?>(id) {
        $.ajax({
            async: true,
            type: 'POST',
            dataType: 'html',
            data: {userDashboardID:<?php echo $userDashboardID; ?>},
            url: "<?php echo site_url('Finance_dashboard/load_financial_position'); ?>",
            beforeSend: function () {
            },
            success: function (data) {
                $("#" + id).html(data);
            }, error: function () {

            }
        });
    }

    /*Started Function By Mushtaq Ahamed*/
    function load_shortcut_links<?php echo $userDashboardID ?>(id) {
        var target = "load_shortcut_links";
        var result = $.grep(position<?php echo $userDashboardID ?>, function (e) {
            return e.functionName == target;
        });

        if (result.length == 0) {
            position<?php echo $userDashboardID ?>.push({functionName: "load_shortcut_links", position: id});
        }

        $.ajax({
            async: true,
            type: 'POST',
            dataType: 'html',
            data: {'period': $('#period<?php echo $userDashboardID; ?>').val(),userDashboardID:<?php echo $userDashboardID; ?>},
            url: "<?php echo site_url('Finance_dashboard/load_shortcut_links'); ?>",
            beforeSend: function () {
                $("#overlay8<?php echo $userDashboardID; ?>").show();
            },
            success: function (data) {
                $("#" + id).html(data);
                $("#overlay8<?php echo $userDashboardID; ?>").hide();
            }, error: function () {

            }
        });
    }

    function load_Public_links<?php echo $userDashboardID ?>(id) {
        var target = "load_Public_links";
        var result = $.grep(position<?php echo $userDashboardID ?>, function (e) {
            return e.functionName == target;
        });

        if (result.length == 0) {
            position<?php echo $userDashboardID ?>.push({functionName: "load_Public_links", position: id});
        }

        $.ajax({
            async: true,
            type: 'POST',
            dataType: 'html',
            data: {'period': $('#period<?php echo $userDashboardID; ?>').val(),userDashboardID:<?php echo $userDashboardID; ?>},
            url: "<?php echo site_url('Finance_dashboard/load_Public_links'); ?>",
            beforeSend: function () {
                $("#overlay9<?php echo $userDashboardID; ?>").show();
            },
            success: function (data) {
                $("#" + id).html(data);
                $("#overlay9<?php echo $userDashboardID; ?>").hide();
            }, error: function () {
                $("#overlay9<?php echo $userDashboardID; ?>").hide();
            }
        });
    }
    /*End Function By Mushtaq Ahamed*/

    function load_revenue_detail_analysis_by_glcode<?php echo $userDashboardID ?>(id) {
        var target = "load_revenue_detail_analysis_by_glcode";
        var result = $.grep(position<?php echo $userDashboardID ?>, function (e) {
            return e.functionName == target;
        });

        if (result.length == 0) {
            position<?php echo $userDashboardID ?>.push({functionName: "load_revenue_detail_analysis_by_glcode", position: id});
        }
        $.ajax({
            async: true,
            type: 'POST',
            dataType: 'html',
            data: {'period': $('#period'+<?php echo $userDashboardID; ?>).val(),userDashboardID:<?php echo $userDashboardID; ?>},
            url: "<?php echo site_url('Finance_dashboard/load_revenue_detail_analysis_by_glcode'); ?>",
            beforeSend: function () {
                $("#overlay10<?php echo $userDashboardID; ?>").show();
            },
            success: function (data) {
                $("#" + id).html(data);
                $("#overlay10<?php echo $userDashboardID; ?>").hide();
            }, error: function () {

            }
        });
    }
    /*Started Function By Mushtaq Ahamed*/
    function load_new_members<?php echo $userDashboardID ?>(id) {
        var target = "load_new_members";
        var result = $.grep(position<?php echo $userDashboardID ?>, function (e) {
            return e.functionName == target;
        });

        if (result.length == 0) {
            position<?php echo $userDashboardID ?>.push({functionName: "load_new_members", position: id});
        }

        $.ajax({
            async: true,
            type: 'POST',
            dataType: 'html',
            data: {'period': $('#period<?php echo $userDashboardID; ?>').val(),userDashboardID:<?php echo $userDashboardID; ?>},
            url: "<?php echo site_url('Finance_dashboard/load_new_members'); ?>",
            beforeSend: function () {
                $("#overlay11<?php echo $userDashboardID; ?>").show();
            },
            success: function (data) {
                $("#" + id).html(data);
                $("#overlay11<?php echo $userDashboardID; ?>").hide();
            }, error: function () {

            }
        });
    }

    function load_to_do_list<?php echo $userDashboardID ?>(id) {
        var target = "load_to_do_list";
        var result = $.grep(position<?php echo $userDashboardID ?>, function (e) {
            return e.functionName == target;
        });

        if (result.length == 0) {
            position<?php echo $userDashboardID ?>.push({functionName: "load_to_do_list", position: id});
        }

        $.ajax({
            async: true,
            type: 'POST',
            dataType: 'html',
            data: {'period': $('#period<?php echo $userDashboardID; ?>').val(),userDashboardID:<?php echo $userDashboardID; ?>},
            url: "<?php echo site_url('Finance_dashboard/load_to_do_list'); ?>",
            beforeSend: function () {
                $("#overlay12<?php echo $userDashboardID; ?>").show();
            },
            success: function (data) {
                $("#" + id).html(data);
                $("#overlay12<?php echo $userDashboardID; ?>").hide();
            }, error: function () {

            }
        });
    }
    /*End Function By Mushtaq Ahamed*/

    function load_sales_log<?php echo $userDashboardID ?>(id) {

        var target = "load_sales_log";
        var result = $.grep(position<?php echo $userDashboardID ?>, function (e) {
            return e.functionName == target;
        });

        if (result.length == 0) {
            position<?php echo $userDashboardID ?>.push({functionName: "load_sales_log", position: id});
        }

        $.ajax({
            async: true,
            type: 'POST',
            dataType: 'html',
            data: {period: $('#period<?php echo $userDashboardID; ?>').val(),userDashboardID:<?php echo $userDashboardID; ?>},
            url: "<?php echo site_url('Finance_dashboard/load_sales_log'); ?>",
            beforeSend: function () {
                $("#overlay18<?php echo $userDashboardID; ?>").show();
            },
            success: function (data) {
                $("#" + id).html(data);
                $("#overlay18<?php echo $userDashboardID; ?>").hide();
            }, error: function () {

            }
        });
    }

    function load_customer_order_analysis<?php echo $userDashboardID ?>(id) {
        $.ajax({
            async: true,
            type: 'POST',
            dataType: 'html',
            data: {userDashboardID:<?php echo $userDashboardID; ?>},
            url: "<?php echo site_url('Finance_dashboard/load_customer_order_analysis'); ?>",
            beforeSend: function () {
            },
            success: function (data) {
                $("#" + id).html(data);
            }, error: function () {

            }
        });
    }

    function load_head_count<?php echo $userDashboardID ?>(id) {
        var target = "load_head_count";
        var result = $.grep(position<?php echo $userDashboardID ?>, function (e) {
            return e.functionName == target;
        });

        if (result.length == 0) {
            position<?php echo $userDashboardID ?>.push({functionName: "load_head_count", position: id});
        }

        $.ajax({
            async: true,
            type: 'POST',
            dataType: 'html',
            data: {'period': $('#period<?php echo $userDashboardID; ?>').val(),userDashboardID:<?php echo $userDashboardID; ?>},
            url: "<?php echo site_url('Finance_dashboard/load_head_count'); ?>",
            beforeSend: function () {
                $("#overlay15<?php echo $userDashboardID; ?>").show();
            },
            success: function (data) {
                $("#" + id).html(data);
                $("#overlay15<?php echo $userDashboardID; ?>").hide();
            }, error: function () {

            }
        });
    }

    function load_Designation_head_count<?php echo $userDashboardID ?>(id) {
        var target = "load_Designation_head_count";
        var result = $.grep(position<?php echo $userDashboardID ?>, function (e) {
            return e.functionName == target;
        });

        if (result.length == 0) {
            position<?php echo $userDashboardID ?>.push({functionName: "load_Designation_head_count", position: id});
        }

        $.ajax({
            async: true,
            type: 'POST',
            dataType: 'html',
            data: {'period': $('#period<?php echo $userDashboardID; ?>').val(),userDashboardID:<?php echo $userDashboardID; ?>},
            url: "<?php echo site_url('Finance_dashboard/load_Designation_head_count'); ?>",
            beforeSend: function () {
                $("#overlay16<?php echo $userDashboardID; ?>").show();
            },
            success: function (data) {
                $("#" + id).html(data);
                $("#overlay16<?php echo $userDashboardID; ?>").hide();
            }, error: function () {

            }
        });
    }

    function load_payroll_cost<?php echo $userDashboardID ?>(id) {
        var target = "load_payroll_cost";
        var result = $.grep(position<?php echo $userDashboardID ?>, function (e) {
            return e.functionName == target;
        });

        if (result.length == 0) {
            position<?php echo $userDashboardID ?>.push({functionName: "load_payroll_cost", position: id});
        }

        $.ajax({
            async: true,
            type: 'POST',
            dataType: 'html',
            data: {'period': $('#period<?php echo $userDashboardID; ?>').val(),userDashboardID:<?php echo $userDashboardID; ?>},
            url: "<?php echo site_url('Finance_dashboard/load_payroll_cost'); ?>",
            beforeSend: function () {
                $("#overlay17<?php echo $userDashboardID; ?>").show();
            },
            success: function (data) {
                $("#" + id).html(data);
                $("#overlay17<?php echo $userDashboardID; ?>").hide();
            }, error: function () {

            }
        });
    }


    function load_revenue_detail_analysis_by_segment<?php echo $userDashboardID ?>(id) {
        var target = "load_revenue_detail_analysis_by_segment";
        var result = $.grep(position<?php echo $userDashboardID ?>, function (e) {
            return e.functionName == target;
        });

        if (result.length == 0) {
            position<?php echo $userDashboardID ?>.push({functionName: "load_revenue_detail_analysis_by_segment", position: id});
        }
        $.ajax({
            async: true,
            type: 'POST',
            dataType: 'html',
            data: {'period': $('#period'+<?php echo $userDashboardID; ?>).val(),userDashboardID:<?php echo $userDashboardID; ?>},
            url: "<?php echo site_url('Finance_dashboard/load_revenue_detail_analysis_by_segment'); ?>",
            beforeSend: function () {
                $("#overlay19<?php echo $userDashboardID; ?>").show();
            },
            success: function (data) {
                $("#" + id).html(data);
                $("#overlay19<?php echo $userDashboardID; ?>").hide();
            }, error: function () {

            }
        });
    }

</script>