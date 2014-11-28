<?php 
$STR = '<table width="700" border="0"  bgcolor="#CCCCCC" cellspacing="1" cellpadding="5">
  <tr>
    <td bgcolor="#FDFDFD" style="font-size:14px; color:#666666;">
	<h1>'. $wpp_title .'</h1>
	'.$wpp_featured_image.' '.(($wpp_price_html) ? $wpp_price.'<br>
	' : '').''.(($wpp_sku) ? '<p><b>SKU</b>: '.$wpp_sku.'</p><br>
	' : '' ).''.(($wpp_stock) ? '<p><b>In stock</b>: '.$wpp_stock.'</p><br>
	' : '' ).''.(($wpp_rating) ? '<p><b>Rating</b>: '.$wpp_rating.'</p>
	' : '' ).''.(($wpp_categories) ? '<p><b>Product Categories</b>: '.$wpp_categories.'</p><br>
	' : '' ).''.(($wpp_tags) ? '<p><b>Product Tags</b>: '.$wpp_tags.'</p><br>
	' : '' ).''.(($wpp_permalink) ? '<p><b>Product Page</b>: <a href="'.$wpp_permalink.'">'.$wpp_permalink.'</a></p><br>
	' : '' ).''.(($wpp_has_variants) ? "\r\n<h2>Product Variants</h2><br>\r\n<p>".$wpp_variants.'</p><br>
	' : "\r\n" ).''.(($wpp_summary) ? "<h2>Product Summary</h2><br>\r\n<p>".$wpp_summary.'</p><br>
	' : '' ).''.(($wpp_description) ? "\r\n<h2>Product Description</h2>\r\n<p>".$wpp_description.'</p><br>
	' : '' ).''.((trim(strip_tags($attribute_html))) ? "\r\n<h2>Product Attributes</h2><br>\r\n<p>".$attribute_html.'</p><br>
	' : '' ).''.((!empty($wpp_gallery)) ? "\r\n<h2>Product Gallery</h2><br>\r\n<p>".$wpp_gallery_images.'</p>
	' : '' ).'
	
	</td>
  </tr>
</table>';

?>