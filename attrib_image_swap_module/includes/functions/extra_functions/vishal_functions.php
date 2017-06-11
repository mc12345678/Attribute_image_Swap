<?php 
  function zen_link($texts, $hrefs, $parameters = '') {

    for ($i=0, $n=sizeof($texts); $i<$n; $i++) {
	
      $link .= '  <a ';
	  
	if (zen_not_null($hrefs)) $link .= 'href="' . zen_output_string($hrefs[$i]['text']) . '"';
	  
    if (zen_not_null($parameters)) $link .= ' ' . $parameters;

      $link .= '>' . zen_output_string($texts[$i]['text'], array('"' => '&quot;', '\'' => '&#039;', '<' => '&lt;', '>' => '&gt;')) . '</a>' . "\n";
    }

    return $link;
  }


?>