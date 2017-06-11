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
<script type="text/javascript"><!--
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
    if (xmlHttp.readyState === XMLHttpRequest.DONE) {
        if (xmlHttp.status === 200) {
            var product_color_image = JSON.parse(xmlHttp.responseText);
            if (product_color_image !== "") {
                document.getElementById("productMainImage").innerHTML = product_color_image;
            } else {
                document.getElementById("productMainImage").innerHTML = origImage; // Return to original image.
            }
        } else {
//            alert("Problem retrieving data");
            console.log(this.responseText);
        }
    }
}

function getattribimage(attribfield, width, height, products_options_values_id, products_id) {
    xmlHttp = new GetXmlHttpObject();

    if (xmlHttp === null) {
        alert("Your browser does not support AJAX!");
        return;
    }
    url = "ajax.php?act=attrib_prod_info&method=swap_image";
    params = "width=" + width + "&height=" + height + "&products_options_values_id=" + products_options_values_id + "&products_id=" + products_id;
    xmlHttp.onreadystatechange = stateChanged;
    xmlHttp.open("POST", url, true);
    xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xmlHttp.setRequestHeader("X-Requested-With", "XMLHttpRequest");
    xmlHttp.send(params);
}
<?php } ?>
function ais_init() {
    var n=document.forms.length;
    origImage = document.getElementById("productMainImage").innerHTML; // Obtain original Image information.

<?php if (ATTRIBUTES_ENABLED_IMAGES == 'true') {?>
    var theForm;
    
    for (var i=0; i<n; i++) {
        if (document.forms[i].name == "cart_quantity") {
            theForm = document.forms[i];
            break;
        }
    }
    
    n=theForm.elements.length;
    for (i=0; i<n; i++) {
        switch (theForm.elements[i].type) {
            case "select":
            case "select-one":
                theForm.elements[i].onchange();
                break;
            case "text'":
                theForm.elements[i].onkeyup();
                break;
            case "checkbox":
            case "radio":
                theForm.elements[i].onclick();
                break;
            case "number":
                theForm.elements[i].onchange();
                theForm.elements[i].onkeyup();
                theForm.elements[i].oninput();
                break;
        }
    }
    <?php } ?>
}
//--------------------------------------------------------

//--></script>