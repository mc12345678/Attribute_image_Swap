<?php
/**
 *  File depends on operations defined in the observer class for Attribute Image Swap.
 **/

class zcObserverAttribImageSwapShoppingCart extends base
{
    function __construct()
    {
        $observeThis = array();
        $observeThis[] = 'NOTIFY_HEADER_END_SHOPPING_CART';

        $this->attach($this, $observeThis);
    }


    // NOTIFY_HEADER_END_SHOPPING_CART
    /**
     * @param $callingClass
     * @param $notifier
     * @param $paramsArray
     */
    function updateNotifyHeaderEndShoppingCart(&$callingClass, $notifier, $paramsArray) {
        global $productArray, $flagAnyOutOfStock, $db, $zcObserverAttribImageSwap;

        // This is defined outside of the below loop because $swappable relates to the image settings for option_names which 
        //   are independent of specific product, therefore would not want to reset the value while looping through product.
        $swappable = array();

        // Step through each product to process for swapping images.
        for ($i = 0, $n = count($productArray); $i < $n; $i++) {
            // If the product doesn't have attributes then move on to next product.
            if (!(!empty($productArray[$i]['attributes']) && is_array($productArray[$i]) && array_key_exists('attributes', $productArray[$i]) && is_array($productArray[$i]['attributes']))) {
                //trigger_error('no attributes: ' . print_r($productArray[$i], true), E_USER_WARNING);
                continue;
            }

            $productArray[$i]['attributeImage'] = array();


            // Generate SQL to identify option selections that support swapping images.
            $sql = "select    pa.options_values_id, pa.options_id
                    from      " . TABLE_PRODUCTS_ATTRIBUTES . " pa,
                    " . TABLE_PRODUCTS_OPTIONS . " po
                    where     pa.options_id = po.products_options_id
                    and       pa.products_id = :products_id:
                    and       pa.options_values_id = :products_options_values_id:
                    and       po.products_options_images_style in (:products_options_images_style:)
                    and       po.language_id = :language_id:";

            $sql = $db->bindVars($sql, ':products_id:', $productArray[$i]['id'], 'integer');
            $sql = $db->bindVars($sql, ':products_options_images_style:', implode(',', array(6, 8)), 'noquotestring');
            $sql = $db->bindVars($sql, ':language_id:', $_SESSION['languages_id'], 'integer');

            // Loop through all of the attributes of the product to identify which attributes support swapping the image.
            foreach ($productArray[$i]['attributes'] as $key => $value) {

                // skip processing if the attribute doesn't define an options_values_id.
                //   Needed to identify if attribute supports image swapping.
                if (!(!empty($value) && is_array($value) && (isset($value['options_values_id']) || array_key_exists('options_values_id', $value)))) {
                    //trigger_error('value: ' . print_r($value, true), E_USER_WARNING);
                    continue;
                }

                // Check if the $key is one that is to be swappable, if not
                //   then continue.
                $sql_swap = $db->bindVars($sql, ':products_options_values_id:', $value['options_values_id'], 'integer');
                $swap_result = $db->Execute($sql_swap);

                if ($swap_result->RecordCount() == 0) {
                    //trigger_error('ZERO records: ' . print_r($swap_result, true), E_USER_WARNING);
                    continue;
                }

                // step through the results and continue to build the swappable array.
                while (!$swap_result->EOF) {
                    //trigger_error('swap results: ' . print_r($swap_result, true), E_USER_WARNING);
                    if (empty($swappable[$swap_result->fields['options_id']])) {
                        $swappable[$swap_result->fields['options_id']] = $swap_result->fields['options_values_id'];
                    }
                    $swap_result->MoveNext();
                }

                // If the current $key isn't in the swappable array then need to move to next one.
                if (!(isset($swappable[$key]) || array_key_exists($key, $swappable))) {
                    //trigger_error('not swappable: ' . print_r($swappable, true), E_USER_WARNING);
                    continue;
                }

                //trigger_error('swap data: ' . print_r($swappable, true) . ' VALUE: ' . print_r($value, true), E_USER_WARNING);
                // Gather swappable image name.
                $image_name = $zcObserverAttribImageSwap->get_attrib_image($productArray[$i]['id'], $value['options_values_id']);
                //  If an image is assigned, then add it to the array of images associated with this product.
                if (zen_not_null($image_name)) {
                    $productArray[$i]['attributeImage'][] = $image_name;
                }
            }

            // Reassign the product image if an attribute image was collected and the shopping cart is set to show the image.
            if (!empty($productArray[$i]['attributeImage']) && IMAGE_SHOPPING_CART_STATUS === '1') {
                // Set the productsImage to be the image identified in the last attributeImage.  If this image/set is 
                // to be created by some sort of overlay process or other image handling, then apply the final overlay below.
                $productArray[$i]['productsImage'] = zen_image(DIR_WS_IMAGES . end($productArray[$i]['attributeImage']), $productArray[$i]['productsName'], IMAGE_SHOPPING_CART_WIDTH, IMAGE_SHOPPING_CART_HEIGHT);
            }
            unset($productArray[$i]['attributeImage']);

        } // EOF for ($i = 0, $n = count($productArray); $i < $n; $i++) {
    }


    function update(&$callingClass, $notifier, $paramsArray = array()) {

        if ($notifier == 'NOTIFY_HEADER_END_SHOPPING_CART') {
            $this->updateNotifyHeaderEndShoppingCart($callingClass, $notifier, $paramsArray);
        }
    }
}