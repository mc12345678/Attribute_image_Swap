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
// $Id: jscript_main.php 5444 2006-12-29 06:45:56Z drbyte $
//
?>
<script language="javascript" type="text/javascript">
function changebgcolor(id,color) {

			document.getElementById(id).style.backgroundColor=color;
}
function changevalue(field,color)
{
	for(var i=0;i<document.cart_quantity.elements.length;i++)
	{
			if(document.cart_quantity.elements[i].name==field)
			{
			document.cart_quantity.elements[i].value=color;
			}
	}
}

var xmlHttp
function getattribimage(attribfield,width,height,products_options_values_id,products_id)
{


				xmlHttp=GetXmlHttpObject();
				if (xmlHttp==null)
				{
					alert ("Your browser does not support AJAX!");
					return;
				} 
				
				var url="attrib_prod_info.php";
				url=url+"?width="+width+"&height="+height+"&products_options_values_id="+products_options_values_id+"&products_id="+products_id;
				xmlHttp.onreadystatechange=stateChanged;
				xmlHttp.open("GET",url,true);
				xmlHttp.send(null);

}

function stateChanged() 
{ 
	if (xmlHttp.readyState==4)
	{    
			
			var product_color_image=xmlHttp.responseText;
			if(product_color_image!=''){
			document.getElementById('productMainImage').innerHTML = product_color_image;
			}
	}
}
//--------------------------------------------------------

function GetXmlHttpObject()
{
var xmlHttp=null;
try
  {
  // Firefox, Opera 8.0+, Safari
   xmlHttp=new XMLHttpRequest();
  }
catch (e)
  {
  // Internet Explorer
  try
    {
     xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
    }
  catch (e)
    {
     xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
    }
  }
return xmlHttp;
}


function GetXmlHttpObject()
{
var xmlHttp=null;
try
  {
  // Firefox, Opera 8.0+, Safari
  xmlHttp=new XMLHttpRequest();
  }
catch (e)
  {
  // Internet Explorer
  try
    {
    xmlHttp=new ActiveXObject("Msxml2.XMLHTTP");
    }
  catch (e)
    {
    xmlHttp=new ActiveXObject("Microsoft.XMLHTTP");
    }
  }
return xmlHttp;
}
</script>