<?php

class zcAttrib_prod_info extends base 
{
  function swap_image()
  {
    global $db;

    $products_options_values_id = $_POST['products_options_values_id'];
//    $alt = $_POST['alt'];
    $width = $_POST['width'];
    $height = $_POST['height'];
    $products_id = $_POST['products_id'];
    $alt = zen_get_products_name($products_id, $_SESSION['languages_id']);
    
    $sql = "select    pa.attributes_image
            from      " . TABLE_PRODUCTS_ATTRIBUTES . " pa
            where     pa.products_id = :products_id:
            and       pa.options_values_id = :products_options_values_id:";
    
    $sql = $db->bindVars($sql, ':products_id:', $products_id, 'integer');
    $sql = $db->bindVars($sql, ':products_options_values_id:', $products_options_values_id, 'integer');
    
    //$account_query = $db->bindVars($sql, ':customersID', $_SESSION['customer_id'], 'integer');
    $products_color_image = $db->Execute($sql);
    
    $products_image = $products_color_image->fields['attributes_image'];
    
    // Get html image code using attribute image or standard image.
    if ($products_image != '') {
        // Found an attribute image, now further image location necessary.
/*    } else { // mc12345678 Commented out to support restoring the image to its original image instead of what is "calculated" below.
        $sql = "select p.products_image
                FROM " . TABLE_PRODUCTS . " p
                WHERE p.products_id = :products_id:";
        
        $sql = $db->bindVars($sql, ':products_id:', $products_id, 'integer');
        $products_color_image = $db->Execute($sql);
        unset($sql);
        
        if ($products_color_image->fields['products_image'] == '' and PRODUCTS_IMAGE_NO_IMAGE_STATUS == '1') {
          $products_image = PRODUCTS_IMAGE_NO_IMAGE;
        } else {
          $products_image = $products_color_image->fields['products_image'];
        }
    }*/

    require_once(DIR_WS_MODULES . zen_get_module_directory(FILENAME_MAIN_PRODUCT_IMAGE));
    
    $image = zen_image(addslashes($products_image_medium), $alt, $width, $height);
    
    if (file_exists(DIR_WS_IMAGES . 'bigger_picture.jpg')) {
        $larger_text = zen_image(DIR_WS_IMAGES . 'bigger_picture.jpg', 'larger image', '140', '44'); // Image to be stored and substitute for the standard text.
    } else {
        $larger_text = TEXT_CLICK_TO_ENLARGE;  // Standard default text for an image
    }
    
    $image_return = '<a href="javascript:popupWindow(\'' . zen_href_link(FILENAME_POPUP_IMAGE_ADDITIONAL, 'products_image_large_additional=' . $products_image_large) . '\')">' . $image . '<br /><span class="imgLink">' . $larger_text . '</span></a>';
  } else { // mc12345678 Used to assign an indicate to return to the original image.
    $image_return = "";
  }
    return $image_return; 
  }
}