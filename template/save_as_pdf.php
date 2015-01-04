<?php 
$STR = '<table width="700" border="0"  bgcolor="#CCCCCC" cellspacing="1" cellpadding="5">
  <tr>
    <td bgcolor="#FDFDFD" style="font-size:14px; color:#666666;">
	<h1>'. $wpp_title .'</h1>
	'.$wpp_featured_image.' '.(($wpp_price_html) ? $wpp_price.'<br>
	' : '').''.(($wpp_sku) ? '<p><b>'.get_option( 'wpp_ph_sku' ).'</b>: '.$wpp_sku.'</p><br>
	' : '' ).''.(($wpp_stock) ? '<p><b>'.get_option( 'wpp_ph_in_stock' ).'</b>: '.$wpp_stock.'</p><br>
	' : '' ).''.(($wpp_rating) ? '<p><b>'.get_option( 'wpp_ph_rating' ).'</b>: '.$wpp_rating.'</p>
	' : '' ).''.(($wpp_categories) ? '<p><b>'.get_option( 'wpp_ph_prod_cats' ).'</b>: '.$wpp_categories.'</p><br>
	' : '' ).''.(($wpp_tags) ? '<p><b>'.get_option( 'wpp_ph_prod_tags' ).'</b>: '.$wpp_tags.'</p><br>
	' : '' ).''.(($wpp_permalink) ? '<p><b>'.get_option( 'wpp_ph_prod_page' ).'</b>: <a href="'.$wpp_permalink.'">'.$wpp_permalink.'</a></p><br>
	' : '' ).''.(($wpp_has_variants) ? "\r\n<h2>".get_option( 'wpp_ph_prod_vars' )."</h2><br>\r\n<p>".$wpp_variants.'</p><br>
	' : "\r\n" ).''.(($wpp_summary) ? "<h2>".get_option( 'wpp_ph_prod_summ' )."</h2><br>\r\n<p>".$wpp_summary.'</p><br>
	' : '' ).''.(($wpp_description) ? "\r\n<h2>".get_option( 'wpp_ph_prod_desc' )."</h2>\r\n<p>".$wpp_description.'</p><br>
	' : '' ).''.((trim(strip_tags($attribute_html))) ? "\r\n<h2>".get_option( 'wpp_ph_prod_attr' )."</h2><br>\r\n<p>".$attribute_html.'</p><br>
	' : '' ).''.wpp_pdf_tabs($custom_tab_title_html, $custom_tab_content_html).'
	'.((!empty($wpp_gallery)) ? "\r\n<h2>".get_option( 'wpp_ph_prod_gall' )."</h2><br>\r\n<p>".$wpp_gallery_images.'</p>
	' : '' ).'
	
	</td>
  </tr>
</table>';

function wpp_pdf_tabs($title_html, $content_html){
	foreach($title_html as $k => $title){ $h .= ((trim(strip_tags($content_html[$k]))) ? "\r\n<h2>".$title."</h2><br>\r\n<p>".$content_html[$k].'</p><br>' : '' );} return $h;
}


?>