<?php

class zcObserverAttribImageSwap extends base
{
    private $products_options_names_fields = array();
    private $parameters;
    private $tmp_html;
    
    function __construct() 
    {
        $observeThis = array();
        $observeThis[] = 'NOTIFY_ATTRIBUTES_MODULE_START_OPTION';
        $observeThis[] = 'NOTIFY_ATTRIBUTES_MODULE_START_OPTIONS_LOOP';
        $observeThis[] = 'NOTIFY_ATTRIBUTES_MODULE_FORMAT_VALUE';
        $observeThis[] = 'NOTIFY_ATTRIBUTES_MODULE_DEFAULT_SWITCH';
        $observeThis[] = 'NOTIFY_ATTRIBUTES_MODULE_OPTION_BUILT';
        $observeThis[] = 'FUNCTIONS_LOOKUPS_OPTION_NAME_NO_VALUES_OPT_TYPE';
        
        $this->attach($this, $observeThis);
    }
    
    // NOTIFY_ATTRIBUTES_MODULE_START_OPTION
    function updateNotifyAttributesModuleStartOption(&$callingClass, $notifier, $products_options_names_fields)
    {
        $this->products_options_names_fields = $products_options_names_fields;
        $this->parameters = '';
    }
    
    // NOTIFY_ATTRIBUTES_MODULE_START_OPTIONS_LOOP
    function updateNotifyAttributesModuleStartOptionsLoop(&$callingClass, $notifier, $i, &$products_options_fields)
    {
        global $params;
        
        if ($this->products_options_names_fields['products_options_images_style'] == 6 || $this->products_options_names_fields['products_options_images_style'] == 8) {
            $params = ' onclick="getattribimage(' . '\'id[' . $this->products_options_names_fields['products_options_id'] . ']\'' . ', ' . MEDIUM_IMAGE_WIDTH . ', ' . MEDIUM_IMAGE_HEIGHT . ', ' . $products_options_fields['products_options_values_id'] . ', ' . (int)$_GET['products_id'] . ');"';
        } else {
            $params = '';
        }
        $this->parameters = $params;
        
        // Select option requires an onchange event instead of an onclick event like say a radio, or checkbox...
        if ($this->products_options_names_fields['products_options_type'] == PRODUCTS_OPTIONS_TYPE_SELECT) {
            if ($this->products_options_names_fields['products_options_images_style'] == 6 || $this->products_options_names_fields['products_options_images_style'] == 8) {
                $params = ' onchange="getattribimage(' . '\'id[' . $this->products_options_names_fields['products_options_id'] . ']\'' . ', ' . MEDIUM_IMAGE_WIDTH . ', ' . MEDIUM_IMAGE_HEIGHT . ', this.value, ' . (int)$_GET['products_id'] . ');"';
            } else {
                $params = '';
            }
            $this->parameters = $params;
        }
        
        // this to make arribute images off from admin if option 7 is selected.
        if (/*$this->products_options_names_fields['products_options_images_style'] == 6 ||*/ $this->products_options_names_fields['products_options_images_style'] == 7) {
            $products_options_fields['attributes_image'] = '';
        }
    }
    
    // NOTIFY_ATTRIBUTES_MODULE_FORMAT_VALUE
    function updateNotifyAttributesModuleFormatValue(&$callingClass, $notifier, $products_options_fields)
    {
        global $products_options_array, $selected_attribute, $products_options_details, $tmp_radio, $tmp_checkbox/*, $zv_display_select_option*/;
        
        $products_options_values_id = $products_options_fields['products_options_values_id'];
        
        if ($this->products_options_names_fields['products_options_type'] == PRODUCTS_OPTIONS_TYPE_LINK) {
            $this->tmp_html = $this->zen_link($products_options_array, '', $this->parameters . ' class="product_attrib_link"');
        } /*else {
            $zv_display_select_option ++;
        }*/ // mc12345678 Commented out because it appears that if this option type is a link, then there is something available to be done, if it is not a link, then as long as it is not a readonly it is already addressed and therefore $zv_display_select_option should not be increased again... ie. if all items are readonly, then this alone will make the please select be displayed even though there is no item to be selected and tmp_html will never have been populated with this content. 
        
        // Addresses offering attribute image swap capability for radio buttons.  Items identified in this list will be replaced as applicable.
        if ($this->products_options_names_fields['products_options_type'] == PRODUCTS_OPTIONS_TYPE_RADIO) {
            
            switch ($this->products_options_names_fields['products_options_images_style']) {
                case '8':
                case '6':
                    $tmp_radio .= zen_draw_radio_field('id[' . $this->products_options_names_fields['products_options_id'] . ']', $products_options_values_id, $selected_attribute, 'id="' . 'attrib-' . $this->products_options_names_fields['products_options_id'] . '-' . $products_options_values_id . '"'.$this->parameters) . '<label class="attribsRadioButton zero" for="' . 'attrib-' . $this->products_options_names_fields['products_options_id'] . '-' . $products_options_values_id . '">' . $products_options_details . '</label><br />' . "\n";
                break;
                
                case '7':
                    $tmp_radio .= zen_draw_radio_field('id[' . $this->products_options_names_fields['products_options_id'] . ']', $products_options_values_id, $selected_attribute, 'id="' . 'attrib-' . $this->products_options_names_fields['products_options_id'] . '-' . $products_options_values_id . '"') . '<label class="attribsRadioButton zero" for="' . 'attrib-' . $this->products_options_names_fields['products_options_id'] . '-' . $products_options_values_id . '">' . $products_options_details . '</label><br />' . "\n";
                break;
            }
        }
        
        // Addresses offering attribute image swap capability for checkboxes.  Items identified in this list will be replaced as applicable.
        if ($this->products_options_names_fields['products_options_type'] == PRODUCTS_OPTIONS_TYPE_CHECKBOX) {
            switch ($this->products_options_names_fields['products_options_images_style']) {
                case '8':
                case '6':
                    $tmp_checkbox .= zen_draw_checkbox_field('id[' . $this->products_options_names_fields['products_options_id'] . ']['.$products_options_values_id.']', $products_options_values_id, $selected_attribute, 'id="' . 'attrib-' . $this->products_options_names_fields['products_options_id'] . '-' . $products_options_values_id . '"'.$this->parameters) . '<label class="attribsCheckbox" for="' . 'attrib-' . $this->products_options_names_fields['products_options_id'] . '-' . $products_options_values_id . '">' . $products_options_details . '</label><br />' . "\n";
                break;
                
                case '7':
                    $tmp_checkbox .= zen_draw_checkbox_field('id[' . $this->products_options_names_fields['products_options_id'] . ']['.$products_options_values_id.']', $products_options_values_id, $selected_attribute, 'id="' . 'attrib-' . $this->products_options_names_fields['products_options_id'] . '-' . $products_options_values_id . '"') . '<label class="attribsCheckbox" for="' . 'attrib-' . $this->products_options_names_fields['products_options_id'] . '-' . $products_options_values_id . '">' . $products_options_details . '</label><br />' . "\n";
                break;					  
                
            }
        }
    }
    
    //  NOTIFY_ATTRIBUTES_MODULE_DEFAULT_SWITCH
    function updateNotifyAttributesModuleDefaultSwitch(&$callingClass, $notifier, $products_options_names_fields, &$options_name, &$options_menu, &$options_comment, &$options_comment_position, &$options_html_id)
    {
        
        // LINK
        if ($products_options_names_fields['products_options_type'] == PRODUCTS_OPTIONS_TYPE_LINK) {
            $options_name[] = $products_options_names_fields['products_options_name'];
            $options_menu[] = $this->tmp_html . "\n";
            $options_comment[] = $products_options_names_fields['products_options_comment'];
            $options_comment_position[] = ($products_options_names_fields['products_options_comment_position'] == '1' ? '1' : '0');
            $options_html_id[] = 'lnk-attrib-' . $products_options_names_fields['products_options_id'];
        }
    }
    
    //  NOTIFY_ATTRIBUTES_MODULE_OPTION_BUILT
    function updateNotifyAttributesModuleOptionBuilt(&$callingClass, $notifier, $products_options_names_fields, &$options_name, &$options_menu, &$options_comment, &$options_comment_position, &$options_html_id, &$options_attributes_image)
    {
        global $products_options;
        
        if ($products_options->RecordCount() == 1 && $products_options_names_fields['products_options_type'] == PRODUCTS_OPTIONS_TYPE_LINK) {
            array_pop($options_name);
            array_pop($options_menu);
            array_pop($options_comment);
            array_pop($options_comment_position);
            array_pop($options_html_id);
            //array_pop($options_attributes_image);  // This may need to be commented out because the $options_attributes_image variable is not addressed in the below called function and if it should remain associated with this attribute assignment as part of displaying the information.  
            // With testing, (grid product having a single option value for each of a single option name, one single option value and one multiple option values and in conjunction with another non-grid attribute that displayed a picture.) it didn't seem to matter one way or the other.  
            //   Flow pushes the attribute image to the $options_attributes_image array before coming to this notifier and does so for all attribute types.  
            //   That said, it would seem that if the single option value option type were called upon and that images are in other ways addressed by the below code, then this array item should be popped off the set as well.
            // This could maybe be commented out for 2 reasons: 1) images are not addressed in the below called function, and 2) if they need to be cleared they  will below.
            
            $this->updateNotifyAttributesModuleDefaultSwitch($callingClass, $notifier, $products_options_names_fields, $options_name, $options_menu, $options_comment, $options_comment_position, $options_html_id);
        }
        
        // Removes the image from being displayed adjacent to the attribute (products_options_images_style as selected on options Name Manager is set to 8 for the option name.
        if (/*$products_options_names_fields['products_options_type'] == PRODUCTS_OPTIONS_TYPE_SELECT &&*/ $this->products_options_names_fields['products_options_images_style'] == 8) {
          array_pop($options_attributes_image);
        }
    }
    
    // FUNCTIONS_LOOKUPS_OPTION_NAME_NO_VALUES_OPT_TYPE', $opt_type, $test_var)
    function updateFunctionsLookupsOptionNameNoValuesOptType(&$callingClass, $notifier, $opt_type, &$test_var) {

        // Check to see if the option type that is being evaluated is the link type added by this program.
        if ($opt_type == PRODUCTS_OPTIONS_TYPE_LINK) {
            // Set $test_var to false, which means that this option type is not expected to have a value associated with it.
            // In ZC 1.5.5, this allows a product to be added to the cart that has this attribute.
            // Calling function is found in includes/functions/functions_lookups.php: zen_option_name_base_expects_no_values
            //  and in: admin/includes/functions/general.php
            $test_var = false;
        }
    }
    
    function update(&$callingClass, $notifier, $paramsArray = array()) {
        
        if ($notifier == 'NOTIFY_ATTRIBUTES_MODULE_START_OPTION') {
            $this->updateNotifyAttributesModuleStartOption($callingClass, $notifier, $paramsArray);
        }
        
        if ($notifier == 'NOTIFY_ATTRIBUTES_MODULE_START_OPTIONS_LOOP') {
            global $products_options;
            $this->updateNotifyAttributesModuleStartOptionsLoop($callingClass, $notifier, $paramsArray, $products_options->fields);
        }
        
        if ($notifier == 'NOTIFY_ATTRIBUTES_MODULE_FORMAT_VALUE') {
            $this->updateNotifyAttributesModuleFormatValue($callingClass, $notifier, $paramsArray);
        }
        
        if ($notifier == 'NOTIFY_ATTRIBUTES_MODULE_DEFAULT_SWITCH') {
            global $options_name, $options_menu, $options_comment, $options_comment_position, $options_html_id;
            $this->updateNotifyAttributesModuleDefaultSwitch($callingClass, $notifier, $paramsArray, $options_name, $options_menu, $options_comment, $options_comment_position, $options_html_id);
        }
        
        if ($notifier == 'NOTIFY_ATTRIBUTES_MODULE_OPTION_BUILT') {
            global $options_name, $options_menu, $options_comment, $options_comment_position, $options_html_id, $options_attributes_image;
            $this->updateNotifyAttributesModuleOptionBuilt($callingClass, $notifier, $paramsArray, $options_name, $options_menu, $options_comment, $options_comment_position, $options_html_id, $options_attributes_image);
        }
        
        if ($notifier == 'FUNCTIONS_LOOKUPS_OPTION_NAME_NO_VALUES_OPT_TYPE') {
            global $test_var;
            $this->updateFunctionsLookupsOptionNameNoValuesOptType($callingClass, $notifier, $paramsArray, $test_var);
        }
    }
    
    // function zen_link has been captured here instead of as an additional function.
    function zen_link($texts, $hrefs, $parameters = '') {
        
        for ($i=0, $n=sizeof($texts); $i<$n; $i++) 
        {
        
            $link .= '  <a ';
	          
            if (zen_not_null($hrefs)) $link .= 'href="' . zen_output_string($hrefs[$i]['text']) . '"';
            
            if (zen_not_null($parameters)) $link .= ' ' . $parameters;
            
            $link .= '>' . zen_output_string($texts[$i]['text'], array('"' => '&quot;', '\'' => '&#039;', '<' => '&lt;', '>' => '&gt;')) . '</a>' . "\n";
        }
        
        return $link;
    }
}