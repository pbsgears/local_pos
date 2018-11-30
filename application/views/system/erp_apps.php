<style>
    .content pre {
        background-color: #F3F5F7;
        border: 1pt solid #AEBDCC;
        font-family: courier, monospace;
        padding: 5pt;
        white-space: pre-wrap;
        word-wrap: break-word;
    }

    /* inlice applications which mimic front ones */
    .technologies {
        line-height: 0;
        list-style-type: none;
        margin: 0;
        padding: 0;
        width: 100%;
        background-color: #B3D4FC;
    }

    .technologies li {
        height: 225px;
        margin: 0 8px 8px 0;
        width: 225px;
        background: none repeat scroll 0 0 #FFFFFF;
        display: inline-block;
        float: left;
    }

    .technologies li a {
        color: #8397A6;
        display: inline-block;
        height: 100%;
        position: relative;
        width: 100%;
    }

    .technologies li a span {
        bottom: 20px;
        display: inline-block;
        font-size: 14px;
        position: absolute;
        text-align: center;
        text-transform: uppercase;
        width: 100%;
    }

    .technologies li a.simulation {
        background: url("img/simulation.png") no-repeat center center;
    }

    .technologies li a.category {
        background: url("img/category.png") no-repeat center center;
    }

    .technologies li a.synchronisation {
        background: url("img/synchronisation.png") no-repeat center center;
    }

    .technologies li a.clustering {
        background: url("img/clustering.png") no-repeat center center;
    }

    .technologies li a.reporting {
        background: url("img/reporting.png") no-repeat center center;
    }

    .technologies li a.ubm {
        background: url("img/unified_business.png") no-repeat center center;
    }

    .technologies li a.workflow {
        background: url("img/workflow.png") no-repeat center center;
    }

    /* inlice applications which mimic front ones */
    .applications {
        line-height: 0;
        list-style-type: none;
        margin: 0;
        padding: 0;
        width: 100%;
        background-color: #B3D4FC;
    }

    .applications li {
        height: 147px;
        margin: 0 8px 8px 0;
        width: 147px;
        background: none repeat scroll 0 0 #FFFFFF;
        /*border: 1px solid #C4CFD7;*/
        display: inline-block;
        float: left;
    }

    .applications li a {
        color: #8397A6;
        display: inline-block;
        height: 100%;
        position: relative;
        width: 100%;
    }

    .applications li a span {
        bottom: 20px;
        display: inline-block;
        font-size: 14px;
        position: absolute;
        text-align: center;
        text-transform: uppercase;
        width: 100%;
    }

    /* Web combined content images */
    .scaled-xsmall {
        height: 170px;
    }

    h3.combined-header {
        margin-bottom: 10px;
    }

    .DataB .auto-float,
    .float-right {
        float: left;
        padding-right: 20px;
        padding-bottom: 20px;
    }

    .DataA .auto-float,
    .float-left {
        float: left;
        padding-right: 20px;
        padding-bottom: 20px;
    }

    /* facebood integration */
    #fbplikebox {
        display: block;
        padding: 0;
        z-index: 99999;
        background: white;
        position: fixed;
    }

    .fbplbadge {
        background-color: #3B5998;
        display: block;
        height: 150px;
        top: 50%;
        margin-top: -75px;
        position: absolute;
        left: -47px;
        width: 47px;
        background-image: url("img/vertical-right.png/");
        background-repeat: no-repeat;
        overflow: hidden;
        -webkit-border-top-left-radius: 8px;
        -webkit-border-bottom-left-radius: 8px;
        -moz-border-radius-topleft: 8px;
        -moz-border-radius-bottomleft: 8px;
        border-top-left-radius: 8px;
        border-bottom-left-radius: 8px;
    }

    /* listbox for simplicity */
    .discussion-thread-listbox table.listbox tr.DataA:hover,
    .discussion-thread-listbox table.listbox tr.DataB:hover,
    .discussion-thread-listbox table.listbox table tr.tbody:hover,
    .simplicity-pretty-listbox table.listbox tr.DataA:hover,
    .simplicity-pretty-listbox table.listbox tr.DataB:hover,
    .simplicity-pretty-listbox table.listbox table tr.tbody:hover {
        color: inherit;
        background-color: white;
    }

    .content h2.simplicity-pretty-listbox-header {
        /*  margin-top:30px;
          border-top: 1px solid #BABABA;*/
        padding: 10px 0 0;
    }

    .content .simplicity-pretty-listbox table,
    .content .simplicity-pretty-listbox table tbody tr td {
        border: 0 solid #ECEBE2;
        padding-bottom: 20px;
    }

    .discussion-thread-listbox table.listbox tr.DataB,
    .simplicity-pretty-listbox table.listbox tr.DataB {
        background-color: transparent;
    }

    .simplicity-pretty-listbox .listbox-head,
    .simplicity-pretty-listbox .listbox-label-line {
        display: none;
    }

    .content .simplicity-pretty-listbox .bottomPosts {
        border-top: 0px solid #BABABA;
        margin: 0;
        padding-bottom: 0px;
    }

    .simplicity-pretty-listbox .bottomPosts .row {
        padding: 0px
    }

    /* bt5 applications slider images */
    .application-meta {
        width: 100%;
        border: none;
    }

    .galleria-lightbox-image {
        left: 30px !important;
        right: 30px !important;
    }

    .galleria-counter {
        color: black;
    }

    .galleria-stage {
        bottom: 20px;
    }

    .galleria-info {
        background: none repeat scroll 0 0 rgba(235, 81, 0, 0.6);
        border-radius: 4px;
        color: #fff;
        font-size: 20px;
        line-height: 30px;
        text-align: left;
        width: 90%;
        top: 0;
    }

    .galleria-info-description {
        color: white;
    }

    #bt5-image-slider {
        width: 100%;
        height: 600px;
    }

    .galleria-info-text,
    .galleria-container {
        background: transparent;
    }

    /*
    .galleria-thumbnails-container {
      display:none;
    }*/

    /* tables inside Web Page content */
    .content table {
        border: 1px solid #ECEBE2;
    }

    .content table tbody tr th {
        border-left: 1px solid #ECEBE2;
    }

    .content table tbody tr td {
        border-top: 1px solid #ECEBE2;
        border-left: 1px solid #ECEBE2;
    }

    /* Form */
    #dialog_submit_button {
        margin-top: 1em;
    }

    html,
    button,
    input,
    select,
    textarea {
        color: #404040;
    }

    .content .field .error {
        background-color: inherit;
        color: #FF4400;
        display: block;
    }

    .required label {
        background-image: url("img/required_mark.png");
        background-position: 0 0.5em;
        background-repeat: no-repeat;
        padding-left: 10px;
    }

    /* in web forn view and web section view we show nothing below breadcrumb so no need of margin */
    #list-mode-content_id #wrapper_breadcrumb,
    #web-section-mode-content_id #wrapper_breadcrumb {
        margin-bottom: 0;
    }

    /* in list/dialog mode we reduce top padding */
    .dialog-mode-content,
    .list-mode-content,
    .web-section-mode-content {
        padding: 24px 0 0;
    }

    /* in web site front we need padding for content */
    .Web-Site-content {
        padding-top: 56px;
    }

    /* we need some space when we show threads */
    .Discussion-Thread-content {
        padding-top: 20px;
    }

    /* we reduce padding for dialog  */
    .dialog_box .content {
        padding: 0
    }

    .dialog_box .content .center {
        text-align: left;
    }

    /* toolbar */
    #action-bar {
        background-color: #000000;
        left: 0;
        /*  margin-top: -26px; */
        /* ===== EDIT SVEN ==== */
        /*  bottom: 0;
            position: fixed; */
        /* Quick fix, put back on top: */
        top: 0;
        position: absolute;
        /* ===== EDIT SVEN ==== */
        width: 100%;
    }

    .toolboxSection .menu ul {
        margin-top: 0;
        margin-bottom: 0;
    }

    .toolbar-block li a {
        color: #000;
    }

    .toolbar-block h3 {
        margin: 0;
    }

    .toolbar-menu {
        padding-left: 10px;
        color: #FFF;
    }

    .toolbar-menu-contrast {
        color: inherit;
    }

    .toolbar-block {
        display: none;
        position: absolute;
        /* ===== EDIT SVEN ==== */
        /* bottom: 25px; */
        top: 25px;
        /* ===== EDIT SVEN ==== */
        border: 1px solid #808080;
        min-width: 250px;
        background-color: #E4EBF1;
        z-index: 1000;
        font-size: 12px;
        color: #000;
    }

    .toolbar-block ul {
        padding: 0 0 0 20px;
        list-style: square;
        margin: 0;
    }

    /* social integration */
    .teaserInfo ul li.social {
        width: 70px;
    }

    .postsocial .facebook {
        width: 85px;
    }

    .document .content {
        padding: 0px;
    }

    .content p.clear {
        margin-bottom: 0px;
        height: 0;
    }

    .document .content p.clear {
        height: 0px;
        margin-top: 0px;
    }

    /* Sven
    .document .content p {
      margin-bottom: 0px;
    }
    */
    .portal_status_message {
        color: red;
    }

    /* tabs in editable_mode */
    .document .actions ul {
        padding: 0;
        clear: both;
        width: 100%;
    }

    .document .actions ul li {
        float: left;
        display: block;
        list-style-type: none;
        margin-right: 15px;
    }

    .document .actions ul li.selected {
        font-weight: bold;
    }

    .editable legend.group_title {
        font-weight: bold;
        font-size: 110%;
    }

    .editable .field {
        margin-bottom: 10px;
    }

    .editable .center {
        float: left;
        text-align: left;
    }

    .editable .bottom {
        width: 100%;
    }

    /* hide jquery mobile ui loader */
    .ui-loader {
        display: none;
    }

    /* Hide ERP5 form hidden fieldset */
    #hidden_fieldset {
        display: none;
    }

    /* Forum */
    .bt-med,
    .formbt, /* Save and SAve adn edit buttons */
    button.discussion-post-action-button {
        border: 0;
        color: #FFF;
        padding: 6px !important;
        background-color: #AAB0B4;
    }

    .attachment p {
        margin: 0;
    }

    .discussion-post-actions {
        padding: 5px;
    }

    .discussion-thread-listbox .listbox-table-header-cell {
        display: none;
    }

    .discussion-post-header .title {
        margin-top: 0px !important;
    }

    .discussion-post-body-container {
        width: 100%;
    }

    .bt-small span {
        background: none repeat scroll 0 0 #AAB0B4;
        display: block;
        color: #FFFFFF;
        padding: 0 0 0 10px;
        position: relative;
        width: 140px;
    }

    /* ERP5 UI */
    #hidden_button,
    .hidden_label label,
    .bottom .field label {
        display: none;
    }

    .field {
        /*   float:left; */
    }

    label {
        font-weight: bold;
    }

    .last-breadcrumb {
        background: none !important;
    }

    #wrapper_breadcrumb {
        margin-bottom: 43px;
    }

    /* ERP5 listbox */
    table.listbox tr.listbox-search-line th.listbox-table-filter-cell input {
        min-width: 90%;
        width: auto;
    }

    table.listbox th img.sort-button-asc-not-selected {
        background: url("listbox-images/1toparrow.png") no-repeat scroll 100% 100% rgba(0, 0, 0, 0);
    }

    table.listbox th img.sort-button-desc-not-selected {
        background: url("listbox-images/1bottomarrow.png") no-repeat scroll 100% 100% rgba(0, 0, 0, 0);
    }

    div.listbox-page-navigation button span.image {
        margin-top: 12px;
    }

    div.listbox-page-navigation button.listbox_next_page span.image {
        background-image: url("listbox-images/1rightarrowv.png");
    }

    div.listbox-page-navigation button.listbox_last_page span.image {
        background-image: url("listbox-images/2rightarrowv.png");
    }

    div.listbox-page-navigation button.listbox_first_page span.image {
        background-image: url("listbox-images/2leftarrowv.png");
    }

    div.listbox-page-navigation button.listbox_previous_page span.image {
        background-image: url("listbox-images/1leftarrowv.png");
    }

    div.listbox-page-navigation-slider {
        min-width: inherit;
    }

    div.listbox-page-navigation {
        color: black;
    }

    div.listbox-page-navigation input.listbox_set_page,
    div.listbox-page-navigation button.listbox_next_page,
    div.listbox-page-navigation button.listbox_last_page,
    div.listbox-page-navigation button.listbox_first_page,
    div.listbox-page-navigation button.listbox_previous_page {
        vertical-align: middle;
    }

    div.listbox-head-spacer {
        background: none;
        height: 0px;
        width: 0px;
    }

    div.listbox-head-content {
        border-top: none;
        background-color: transparent;
        border-right: none;
        margin-left: 0;
    }

    div.listbox-footer,
    table.listbox tr.listbox-label-line {
        background-color: white;
    }

    .listbox-table-filter-cell,
    .listbox-table-select-cell {
        background-color: transparent;
    }

    /* teaser needs to be fixed */
    #dialog-mode-content_id .teaserin,
    #web-section-mode-content_id .teaserin,
    #list-mode-content_id .teaserin,
    #_id .headteaser .teaserin {
        padding: 24px 0;
    }

    /* front page no teaser bottom padding */
    .Web-Site-content .teaserin,
    .headteaser .teaserin {
        padding-bottom: 0;
    }

    /*
    Max width before this PARTICULAR table gets nasty
    This query will take effect for any screen smaller than 760px
    and also iPads specifically.

    @media
    only screen and (max-width: 760px),
    (min-device-width: 768px) and (max-device-width: 1024px)  {
    */

    .fontcss {
        font-size: 48px;
        margin-top: 40px;
        margin-left: 50px;
    }

    .boxname {

        text-align: center;
        margin-top: 20px;
    }

    .listname {
        /*background-color: #607D8B;*/
        background-color: #68c39a;
        margin: 5px;
        padding: 10px;
        color: white;

        margin-top: 12px;
        font-size: 12px;
        text-align: center;
    }

    .listname2 {
        /*background-color: #607D8B;*/
        background-color: #51b5c3;
        margin: 5px;
        padding: 10px;
        color: white;

        margin-top: 12px;
        font-size: 12px;
        text-align: center;
    }

    @media only screen and (max-width: 760px) {
        /* Force table to not be like tables anymore */
        table, thead, tbody, th, td, tr {
            display: block;
        }

        /* Hide table headers (but not display: none;, for accessibility) */
        thead tr {
            position: absolute;
            top: -9999px;
            left: -9999px;
        }

        tr {
            border: 1px solid #ccc;
        }

        td {
            /* Behave  like a "row" */
            border: none;
            border-bottom: 1px solid #eee;
            position: relative;
            padding-left: 50%;
        }

        td:before {
            /* Now like a table header */
            position: absolute;
            /* Top/left values mimic padding */
            top: 6px;
            left: 6px;
            width: 45%;
            padding-right: 10px;
            white-space: nowrap;
        }

        table.listbox th, table.listbox td {
            border-width: 0;
        }

        /* we need themn equal due to chaotic mixture of them in web pages' content */
        .content h1,
        .content h2 {
            font-size: 25px;
            font-weight: normal;
            line-height: 33px;
        }

        .content h2 {
            color: #404040;
            margin: 0;
        }

        .content h2 a {
            color: #404040;
            text-decoration: none;
        }

        .content h2 a:hover {
            color: rgb(2, 87, 136);
        }

    }

</style>
<div id="filter-panel" class="collapse filter-panel"></div>

<div id="dashboard_content">
    <div class="document">
        <div class="contentx">
            <fieldset class="bottom viewable">
                <div class="field page hidden_label" title="">
                    <label>
                        Page Content
                    </label>
                    <div class="input">
                        <div class="row">
                            <div class="col-md-12" style="margin-left: 50px;">
                                <h3>Core Modules</h3>
                                <div class="responsiveimg">
                                    <ul class="applications">
                                        <?php
                                        $companyID = current_companyID();
                                        $CI =& get_instance();
                                        $addon = $CI->db->query("SELECT srp_erp_navigationmenus.*,if(moduleID!='',1,0) as added FROM `srp_erp_navigationmenus` LEFT JOIN `srp_erp_moduleassign` on srp_erp_navigationmenus.navigationMenuID=srp_erp_moduleassign.navigationMenuID AND  companyID={$companyID} WHERE isAddon >= 1  ORDER BY added ,sortOrder  asc")->result_array();
                                        if (!empty($addon)) {
                                            foreach ($addon as $value) {
                                                ?>
                                                <?php if ($value['isAddon'] == 1) { ?>
                                                    <li>

                                                        <a href="<?php echo site_url('modules/' . $value['navigationMenuID']) ?>"
                                                           class="a"><i style=""
                                                                        class="<?php echo $value['pageIcon'] ?> fontcss"
                                                                        aria-hidden="true"></i>
                                                            <div class="boxname">
                                                                <?php echo $value['addonDescription'] ?></div> <?php if ($value['added'] == 1) { ?> <!--<div class="listname" style="">Added</div>--> <?php } ?>
                                                        </a>

                                                    </li>
                                                    <?php
                                                }
                                            }
                                        }
                                        ?>
                                    </ul>

                                </div>
                            </div>
                            <div class="col-md-12" style="margin-left: 50px;">
                                <h3>Addon</h3>
                                <div class="responsiveimg">
                                    <ul class="applications">
                                        <?php
                                        $companyID = current_companyID();
                                        $CI =& get_instance();
                                        $addon = $CI->db->query("SELECT srp_erp_navigationmenus.*,if(moduleID!='',1,0) as added FROM `srp_erp_navigationmenus` LEFT JOIN `srp_erp_moduleassign` on srp_erp_navigationmenus.navigationMenuID=srp_erp_moduleassign.navigationMenuID AND  companyID={$companyID} WHERE isAddon >= 1  ORDER BY added ,sortOrder  asc")->result_array();
                                        if (!empty($addon)) {
                                            foreach ($addon as $value) {
                                                ?>
                                                <?php if ($value['isAddon'] == 2) { ?>
                                                    <li>
                                                        <a href="<?php echo site_url('modules/' . $value['navigationMenuID']) ?>"
                                                           class="a"><i style=""
                                                                        class="<?php echo $value['pageIcon'] ?> fontcss"
                                                                        aria-hidden="true"></i>
                                                            <div class="boxname"><?php echo $value['addonDescription'] ?></div> <?php if ($value['added'] == 1) { ?>
                                                                <div class="listname" style="">Added</div> <?php } else { ?> <div class="listname2" style="">Add</div> <?php } ?>
                                                        </a>

                                                    </li>
                                                    <?php
                                                }
                                            }
                                        }
                                        ?>
                                    </ul>

                                </div>
                            </div>
                        </div>
                    </div>


                </div>


            </fieldset>


            <p class="clear"></p>


        </div>
    </div>
</div>
<?php echo footer_page('Right foot', 'Left foot', false); ?>

