
<div class="fixHeader_Div" style="max-width: 100%; height: 253px">
    <table class="<?php echo table_class(); ?>" id="detailTB" style="max-width: 100%">
        <thead>
        <tr>
            <th>#</th>
            <th>Buy Qty</th>
            <th>Free Qty</th>
            <th>
                <button type="button" class="btn btn-primary btn-xs pull-right" onclick="addRow()">
                    <i class="fa fa-plus"></i>
                </button>
            </th>
        </tr>
        </thead>
        <tbody>
        <?php
        if(empty($detail)){
            ?>
            <tr>
                <td align="right">1</td>
                <td>
                    <input type="text" name="range[]" id="" class="form-control number setupColumn">
                </td>
                <td>
                    <input type="text" name="discountPer[]" id="" class="form-control number setupColumn">
                </td>
                <td align="center"></td>
            </tr>
            <?php
        }
        foreach($detail as $key=>$row){
            $remove = ( $key > 0 )? '<span class="glyphicon glyphicon-trash deleteRow" style="color:rgb(209, 91, 71); position: static"></span>' : '';
            echo '<tr>
                <td align="right">'.($key+1).'</td>
                <td>
                    <input type="hidden" name="range[]">
                    <input type="text" name="buyQty[]" id="" class="form-control number setupColumn" value="'.$row['buyQty'].'">
                </td>
                <td>
                    <input type="text" name="getFreeQty[]" id="" class="form-control number setupColumn" value="'.$row['getFreeQty'].'">
                </td>
                <td align="center">'.$remove.'</td>
            </tr>';
        }
        ?>
        </tbody>
    </table>
</div>
<script type="text/javascript">
    var detailTB = $('#detailTB');

    $(document).ready(function () {
        var title = $('#promoType').find(':selected').text();
        $('#title-label').text(title);

        detailTB.tableHeadFixer({
            head: true,
            foot: true,
            left: 1,
            right: 0,
            'z-index': 0
        });
    });

    $(document).on('keypress', '.number', function (event) {
        var amount = $(this).val();
        if (amount.indexOf('.') > -1) {
            if (event.which != 8 && isNaN(String.fromCharCode(event.which))) {
                event.preventDefault();
            }
        }
        else {
            if (event.which != 8 && event.which != 46 && isNaN(String.fromCharCode(event.which))) {
                event.preventDefault();
            }
        }
    });

    function addRow(){
        var appendRow = '<tr><td align="right"></td><td><input type="hidden" name="range[]">' ;
        appendRow += '<input type="text" name="buyQty[]" class="form-control number setupColumn"></td>';
        appendRow += '<td><input type="text" name="getFreeQty[]" class="form-control number setupColumn"></td>';
        appendRow += '<td align="center"><span class="glyphicon glyphicon-trash deleteRow" style="color:rgb(209, 91, 71); position: static"></span></tr>';
        detailTB.append(appendRow);

        addRowNumber();
    }

    function addRowNumber(){
        var i = 0;
        $('#detailTB tr').each(function(){
            $(this).find('td:eq(0)').text(i);
            i++;
        });
    }

    $(document).on('click', '.deleteRow', function(){
        $(this).closest('tr').remove();
        addRowNumber();
    });

</script>



<?php
/**
 * Created by PhpStorm.
 * User: NSK
 * Date: 2016-10-12
 * Time: 12:36 PM
 */