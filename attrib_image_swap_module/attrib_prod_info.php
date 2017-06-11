<?php
require('includes/application_top.php');

$products_options_values_id = $_REQUEST['products_options_values_id'];
$alt = $_REQUEST['alt'];	
$width = $_REQUEST['width'];	
$height = $_REQUEST['height'];	
$products_id = $_REQUEST['products_id'];	


$sql = "select    pa.attributes_image 
from      " . TABLE_PRODUCTS_ATTRIBUTES . " pa 
where     pa.products_id = '" . (int)$products_id . "'
and       pa.options_values_id = '" . (int)$products_options_values_id ."'";

$account_query = $db->bindVars($sql, ':customersID', $_SESSION['customer_id'], 'integer');
$products_color_image = $db->Execute($sql);

$attributes_image = $products_color_image->fields['attributes_image'];
if($attributes_image!=''){
$image = zen_image('images/'.$attributes_image, $alt, $width, $height);
?>
<?php echo '<a href="javascript:popupWindow(\'' . zen_href_link(FILENAME_POPUP_IMAGE_ADDITIONAL, 'products_image_large_additional=' . 'images/'.$attributes_image) . '\')">' . $image . '<br /><span class="imgLink">' . zen_image('images/bigger_picture.jpg', 'larger image', '140', '44') . '</span></a>'; ?>
<?php }?>