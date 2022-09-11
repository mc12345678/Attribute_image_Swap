<?php
//
// +----------------------------------------------------------------------+
// |zen-cart Open Source E-commerce                                       |
// +----------------------------------------------------------------------+
// | Copyright (c) 2003 The zen-cart developers                           |
// |                                                                      |
// | http://www.zen-cart.com/index.php                                    |
// |                                                                      |
// | Portions Copyright (c) 2003 osCommerce                               |
// +----------------------------------------------------------------------+
// | This source file is subject to version 2.0 of the GPL license,       |
// | that is bundled with this package in the file LICENSE, and is        |
// | available through the world-wide-web at the following url:           |
// | http://www.zen-cart.com/license/2_0.txt.                             |
// | If you did not receive a copy of the zen-cart license and are unable |
// | to obtain it through the world-wide-web, please send a note to       |
// | license@zen-cart.com so we can mail you a copy immediately.          |
// +----------------------------------------------------------------------+
// $Id: jscript_ais.php,v 1.1 2016/10/17 21:50:47 tbowen Exp $ mc12345678 2016-12-14
//
?>
<script type="text/javascript">
<?php if (ATTRIBUTES_ENABLED_IMAGES == 'true') { ?>
function changebgcolor(id, color) {

    document.getElementById(id).style.backgroundColor = color;
};

function changevalue(field, color) {
    var i;
    for (i = 0; i < document.cart_quantity.elements.length; i += 1) {
        if (document.cart_quantity.elements[i].name === field) {
            document.cart_quantity.elements[i].value = color;
        }
    }
};

var xmlHttp;
var origImage; // "Global" variable to store the original Image.

function GetXmlHttpObject() {
    xmlHttp = null;
    try
    {
        // Firefox, Opera 8.0+, Safari
        xmlHttp = new XMLHttpRequest();
    } catch (e) {
        // Internet Explorer
        try
        {
            xmlHttp = new ActiveXObject("Msxml2.XMLHTTP");
        } catch (e) {
            xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
        }
    }
    return xmlHttp;
};

var stateChanged = function () {
    if (xmlHttp.readyState !== XMLHttpRequest.DONE) {
        return;
    }
    if (xmlHttp.status !== 200) {
//            alert("Problem retrieving data");
        console.log(this.responseText);
        return;
    }

    var product_color_image = JSON.parse(xmlHttp.responseText);
    if (product_color_image !== "") {
        document.getElementById("productMainImage").innerHTML = product_color_image;
    } else {
        document.getElementById("productMainImage").innerHTML = origImage; // Return to original image.
    }
        <?php if (defined('ZEN_COLORBOX_STATUS') && ZEN_COLORBOX_STATUS === 'true') {
           if (is_file(DIR_WS_LANGUAGES . $_SESSION['language'] . '/' . $template_dir . '/zen_colorbox_language.php')) {
               require (DIR_WS_LANGUAGES . $_SESSION['language'] . '/' . $template_dir . '/zen_colorbox_language.php');
           } elseif (is_file(DIR_WS_LANGUAGES . $_SESSION['language'] . '/zen_colorbox_language.php')) {
               require (DIR_WS_LANGUAGES . $_SESSION['language'] . '/zen_colorbox_language.php');
           } else {
               require (DIR_WS_LANGUAGES . 'english/zen_colorbox_language.php');
           }
           require(DIR_FS_CATALOG . DIR_WS_CLASSES . 'zen_colorbox/autoload_default.php');
       }
?>
};

function getattribimage(attribfield, width, height, products_options_values_id, products_id) {
    if (typeof zcJS == "undefined" || !zcJS) {

        xmlHttp = new GetXmlHttpObject();

        if (xmlHttp === null) {
            alert("Your browser does not support AJAX!");
            return;
        }
        url = "ajax.php?act=attrib_prod_info&method=swap_image<?php echo (!empty(zen_get_all_get_params(array('action')))) ? '&' . preg_replace("/&$/","",zen_get_all_get_params(array('action'))) : ''; ?>";
        params = "width=" + width + "&height=" + height + "&products_options_values_id=" + products_options_values_id + "&products_id=" + products_id;
        xmlHttp.onreadystatechange = stateChanged;
        xmlHttp.open("POST", url, true);
        xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xmlHttp.setRequestHeader("X-Requested-With", "XMLHttpRequest");
        xmlHttp.send(params);
    } else {
        var jsonData = {};

        jsonData["width"] = width;
        jsonData["height"] = height;
        jsonData["products_options_values_id"] = products_options_values_id;
        jsonData["products_id"] = products_id;

        var option = { url : "ajax.php?act=attrib_prod_info&method=swap_image<?php echo (!empty($_GET['main_page']) ? '&main_page=' . $_GET['main_page'] : '') . ((!empty(zen_get_all_get_params(array('action')))) ? '&' . preg_replace("/&$/","",zen_get_all_get_params(array('action'))) : ''); ?>",
                       data : jsonData,
                       timeout : 30000
                     };

        zcJS.ajax(option).done(
            function (response,textStatus,jqXHR) {

                var product_color_image = JSON.parse(jqXHR.responseText);
                if (product_color_image !== "") {
                    document.getElementById("productMainImage").innerHTML = product_color_image;
                } else {
                    document.getElementById("productMainImage").innerHTML = origImage; // Return to original image.
                }
                <?php if (defined('ZEN_COLORBOX_STATUS') && ZEN_COLORBOX_STATUS == 'true') {
                  require(DIR_FS_CATALOG . DIR_WS_CLASSES . 'zen_colorbox/autoload_default.php');
                  }
                ?>
            }
        ).fail( function(jqXHR,textStatus,errorThrown) {
            console.log(errorThrown);
//            alert("Status returned - " + textStatus);
        });

    }
};
<?php } ?>
function ais_init() {
    var n=document.forms.length;
    origImage = document.getElementById("productMainImage").innerHTML; // Obtain original Image information.

<?php // Has attributes, and at least one of the option names has a setting of 6 or 8 and an image
  $ais_support = defined('ATTRIBUTES_ENABLED_IMAGES') && ATTRIBUTES_ENABLED_IMAGES == 'true' && isset($_GET['products_id']) && $_GET['products_id'] != '' && zen_has_product_attributes((int)$_GET['products_id']);

  if ($ais_support) {
    $sql = "SELECT count(*) AS quantity
          from " . TABLE_PRODUCTS_OPTIONS . " popt, " . TABLE_PRODUCTS_ATTRIBUTES . " patrib
          where    patrib.products_id=" . (int)$_GET['products_id'] . "
            and      patrib.options_id = popt.products_options_id
            and      popt.language_id = " . (int)$_SESSION['languages_id'] . "
            and (popt.products_options_images_style = 6 OR popt.products_options_images_style = 8)
             limit 1";
    $has_ais = $db->Execute($sql);
    $ais_support = $has_ais->fields['quantity'] > 0;
  }

if ($ais_support) {?>
    var theForm = false;

    for (var i=0; i<n; i++) {
        if (document.forms[i].name == "cart_quantity") {
            theForm = document.forms[i];
            break;
        }
    }

    if (theForm) {
        n=theForm.elements.length;
        for (i=0; i<n; i++) {
            if (theForm.elements[i].name == "cart_quantity") {
                continue;
            }
            switch (theForm.elements[i].type) {
                case "select":
                case "select-one":
                    try {
                        theForm.elements[i].onchange();
                    } catch (err) {
                        // Action not associated with element.
                    }
                    break;
                case "text":
                    try {
                        theForm.elements[i].onkeyup();
                    } catch (err) {
                        // Action not associated with element.
                    }
                    break;
                case "checkbox":
                case "radio":
                    if (document.getElementById(theForm.elements[i].id).checked) {
                        try {
                            theForm.elements[i].onclick();
                        } catch (err) {
                            // Action not associated with element.
                        }
                    }
                    break;
                case "number":
                    try {
                        theForm.elements[i].onchange();
                    } catch (err) {
                        // Action not associated with element.
                    }
                    try {
                        theForm.elements[i].onkeyup();
                    } catch (err) {
                        // Action not associated with element.
                    }
                    try {
                        theForm.elements[i].oninput();
                    } catch (err) {
                        // Action not associated with element.
                    }
                    break;
            }
        }
    }
    <?php } ?>
};
//--------------------------------------------------------

</script>
