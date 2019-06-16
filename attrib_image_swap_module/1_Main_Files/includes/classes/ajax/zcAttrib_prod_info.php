<?php
/**
 * @copyright 2019
 * @author mc12345678 of http://mc12345678.com
 **/

class zcAttrib_prod_info extends base 
{
  function swap_image()
  {
    global $db, $zco_notifier, $zcObserverAttribImageSwap;

    // mc12345678 Used to assign and indicate to return to the original image.
    $image_return = "";

    $products_options_values_id = (int)$_POST['products_options_values_id'];
//    $alt = $_POST['alt'];
    $width = (float)$_POST['width'];
    $height = (float)$_POST['height'];
    $products_id = (int)$_POST['products_id'];
    $alt = zen_get_products_name($products_id, (int)$_SESSION['languages_id']);
    
    $products_image = $zcObserverAttribImageSwap->get_attrib_image($products_id, $products_options_values_id);
    
    // If there is no image then return the default/last value of $image_return
    if ($products_image == '') {
      return $image_return;
    }
    
    // Get html image code using attribute image or standard image.
//    if ($products_image != '') {
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
    
      if ((defined('PRODUCTS_IMAGE_LARGER_TEXT_FILE_IMAGE') ? PRODUCTS_IMAGE_LARGER_TEXT_FILE_IMAGE : true) && file_exists(DIR_WS_IMAGES . 'bigger_picture.jpg')) {
          $larger_text = zen_image(DIR_WS_IMAGES . 'bigger_picture.jpg', 'larger image', '140', '44'); // Image to be stored and substitute for the standard text.
      } else {
          $larger_text = TEXT_CLICK_TO_ENLARGE;  // Standard default text for an image
      }

      if(function_exists('zen_colorbox') && ZEN_COLORBOX_STATUS == 'true') {
        $products_name = zen_products_lookup((int)$products_id, 'products_name');

        // Do the older style of supporting zen_colorbox.
        if(ZEN_COLORBOX_GALLERY_MODE == 'true' && ZEN_COLORBOX_GALLERY_MAIN_IMAGE == 'true') {
          $rel = 'colorbox';
        } else {
          $rel = 'colorbox-' . rand(100, 999);
        }

        $image_return = '<a href="' . zen_colorbox($products_image_large, addslashes($products_name), (defined('LARGE_IMAGE_WIDTH') ? LARGE_IMAGE_WIDTH : ''), (defined('LARGE_IMAGE_HEIGHT') ? LARGE_IMAGE_HEIGHT : '')) . '" data-cbox-rel="' . $rel . '" class="'. "nofollow" . '" title="'. addslashes($products_name) . '">' . $image . '<br /><span class="imgLink">' . $larger_text . '</span></a>';

        // Use the actual zen_colorbox processing (module)
        $zen_colorbox_file = DIR_WS_MODULES . zen_get_module_directory('zen_colorbox');

        if (is_file($zen_colorbox_file) && (ZEN_COLORBOX_GALLERY_MAIN_IMAGE == 'true')) {
          $flag_display_large = true;
          $thumb_slashes = $image;

          include $zen_colorbox_file;

          // Remove the excess script related portions of the response to give just
          //   the basic HTML content
          $script_link = str_replace('<script type="text/javascript"><!--' . "\n" . 'document.write(\'', '', $script_link);
          $script_link = str_replace(');' . "\n" . '//--></script>', '', $script_link);
          $script_link = str_replace('\/', '/', $script_link);
          $script_link = str_replace('\'', '', $script_link);
          $image_return = $script_link;
        }
      } else {
        // Generating only the javascript version of the link, because if javascript is disabled on the client side, then none of this is executed.
        $image_return = '<a href="javascript:popupWindow(\'' . zen_href_link(FILENAME_POPUP_IMAGE_ADDITIONAL, 'products_image_large_additional=' . $products_image_large) . '\')">' . $image . '<br /><span class="imgLink">' . $larger_text . '</span></a>';
      }
//    }
    return $image_return; 
  }
}
