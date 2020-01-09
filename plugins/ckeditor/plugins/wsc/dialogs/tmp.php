<?php
$style = !empty($Category['bgColor']) ? 'background-color:' . $Category['bgColor'] : '';
$html = '<div class="btnStyleCustom">';
$html .= '<button type="button" data-toggle="tab" tabindex="-1" data-parent="0" onclick="set_categoryInfo(0, ' . $Category['autoID'] . ' style="' . $style . '" id="categoryBtnID_' . $Category['autoID'] . '" href="#pilltab' . $Category['autoID'] . '" class="itemButton btnCategoryTab glass">';
$html .= '<span id="proname">';
$html .= str_replace("'", "&#39;", $Category['description']);
$html .= '</span>';
$html .= '</button></div>';

?>
<div class="btnStyleCustom">
    <button type="button" data-toggle="tab" tabindex="-1"
            data-parent="<?php echo $autoID ?>"
            onclick="set_categoryInfo(<?php echo $autoID ?>,<?php echo $catList['autoID'] ?>)"
            style="background-color: <?php if (!empty($catList['bgColor'])) {
                echo $catList['bgColor'];
            } ?>" id="categoryBtnID_<?php echo $catList['autoID'] ?>"
            href="#pilltab<?php echo $catList['autoID'] ?>"
            class="itemButton btnCategoryTab glass">
                                                    <span id="proname">
                                                      <?php echo str_replace("'", "&#39;", strtoupper($catList['description'])); ?>
                                                    </span>
    </button>
</div>






