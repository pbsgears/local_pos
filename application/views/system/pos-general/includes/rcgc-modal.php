<div aria-hidden="true" role="dialog" id="rcgc_modal" class="modal" style="z-index: 1000000;">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="color-line"></div>
            <div class="modal-header pos_Header" id="" style="background-color: #0581B8;">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color:white;"><span
                        aria-hidden="true" style="color:white;">&times;</span></button>
                <h4 class="modal-title" style="color:white;">RCGC Payments</h4>
            </div>
            <div class="modal-body" style="">
                <?php echo form_open('', 'role="form" id="rcgcform"'); ?>
                <input type="hidden" name="paymentglautoid" id="paymentglautoid">

                <div class="row">
                    <div class="form-group col-sm-3">
                        <label class="title">Member ID</label>
                    </div>
                    <div class="form-group col-sm-4">
                        <input type="text" class="form-control " id="memberid" name="memberid">
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-3">
                        <label class="title">Member Name</label>
                    </div>
                    <div class="form-group col-sm-4">
                        <input type="text" class="form-control " id="membername" name="membername">
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-3">
                        <label class="title">Contact Number</label>
                    </div>
                    <div class="form-group col-sm-4">
                        <input type="text" class="form-control " id="contactnumber" name="contactnumber">
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-3">
                        <label class="title">Mail Address</label>
                    </div>
                    <div class="form-group col-sm-4">
                        <input type="text" class="form-control " id="mailaddress" name="mailaddress">
                    </div>
                </div>
                </form>
            </div>

            <div class="modal-footer" style="padding: 10px">
                <button class="btn btn-primary btn-sm" type="button"
                        onclick="setrcgccustdetails()">
                    Submit
                </button>
                <button data-dismiss="modal" class="btn btn-default btn-sm" type="button">Close</button>
            </div>
        </div>
    </div>
</div>