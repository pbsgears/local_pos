<div class="posg-help-container">
    <div class="ac">
        <button class="button f-13 button-caution button-raised button-longshadow button-pill" type="button"
                onclick="showHelp()">
            <i class="fa fa-question-circle" aria-hidden="true"></i> Help - shortcuts
        </button>
    </div>
</div>


<div class="modal fade" id="posg_help_modal" role="dialog" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog modal-sm" style="width: 50%">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header pos_Header" id="">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><i class="fa fa-question-circle" aria-hidden="true"></i> Help - Shortcut </h4>
            </div>
            <div class="modal-body">
                <table class="table table-bordered table-condensed table-striped customTbl">
                    <thead>
                    <tr>
                        <th>Shortcut Key</th>
                        <th>Description</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <th colspan="2">General Shortcuts</th>
                    </tr>

                    <tr>
                        <td class="ac">F1</td>
                        <td>
                            <ol>
                                <li>Open Payment Window</li>
                                <li>Pay Bill</li>
                            </ol>
                        </td>
                    </tr>
                    <tr>
                        <td class="ac">F2</td>
                        <td> Hold Bill</td>
                    </tr>
                    <tr>
                        <td class="ac">F3</td>
                        <td> Return Invoice</td>

                    <tr>
                    <tr>
                        <td class="ac">F4</td>
                        <td> Search Item From Item Master</td>

                    <tr>
                        <td class="ac">F5</td>
                        <td> New Bill</td>
                    </tr>
                    <tr>
                        <td class="ac">F6</td>
                        <td> Open Customer</td>
                    </tr>
                    <tr>
                        <td class="ac">F7</td>
                        <td> Recall or Open Bill</td>
                    </tr>
                    <tr>
                        <td class="ac">F8</td>
                        <td> Edit Qty.</td>
                    </tr>
                    <tr>
                        <td class="ac">F9</td>
                        <td> Barcode search</td>
                    </tr>
                    <tr>
                        <td class="ac">F10</td>
                        <td><strong>Search : </strong> Customer</td>
                    </tr>
                    <tr>
                        <td class="ac">F11</td>
                        <td> General Discount %</td>
                    </tr>
                    <tr>
                        <td class="ac">F12</td>
                        <td> Edit Item</td>
                    </tr>
                    <tr>
                        <td class="ac">Enter</td>
                        <td>
                            <ol>
                                <li>Select exact net amount in Payment window</li>
                                <li>Select invoice in recall</li>
                                <li>Select customer in customer window</li>
                            </ol>

                        </td>
                    </tr>
                    <tr>
                        <td class="ac">shift + Enter</td>
                        <td> Select <strong> 'Yes'</strong> in the confirmation box</td>
                    </tr>
                    <tr>
                        <td class="ac">Delete</td>
                        <td>
                            Delete Item in invoice
                        </td>
                    </tr>
                    <tr>
                        <td class="ac">Arrow Up</td>
                        <td> Select upwards or move to up</td>
                    </tr>
                    <tr>
                        <td class="ac">Arrow Down</td>
                        <td> Select Downward or move to down</td>
                    </tr>
                    <tr>
                        <th colspan="2">Edit Item</th>
                    </tr>
                    <tr>
                        <td class="ac">Ctrl + F</td>
                        <td><strong>Search </strong> - Search Item in keyword search</td>
                    </tr>
                    <tr>
                        <td class="ac">Ctrl + Q</td>
                        <td> Edit item [F12] <i class="fa fa-arrow-right" aria-hidden="true"></i> change Discount
                            Percentage
                        </td>
                    </tr>
                    <tr>
                        <td class="ac">Ctrl + D</td>
                        <td> Edit item [F12] <i class="fa fa-arrow-right" aria-hidden="true"></i> change Discount Amount
                        </td>
                    </tr>
                    <tr>
                        <td class="ac">Ctrl + E</td>
                        <td> Edit item [F12] <i class="fa fa-arrow-right" aria-hidden="true"></i> change Sales Price
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <div class="modal-footer" style="padding: 10px">
                <button data-dismiss="modal" class="btn btn-primary btn-md" type="button"
                <button data-dismiss="modal" class="btn btn-danger btn-md" type="button">Close</button>
            </div>
        </div>
    </div>
</div>


<script>
    function showHelp() {
        $("#posg_help_modal").modal('show')
    }


</script>