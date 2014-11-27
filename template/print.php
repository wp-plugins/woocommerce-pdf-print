<?php 
$print = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>'. $print_title.' : '. get_option('blogname') .' : '.get_option('siteurl').'</title>
<meta http-equiv="Content-Type" content="text/html; charset='.get_option('blog_charset').'" />
<style media="print" type="text/css">
#hidden_{
	display:none;
}
'.get_option('wpp_print_css').'
</style>
<style media="all" type="text/css">
'.get_option('wpp_print_css').'
img{
	border:hidden;
	float:left;
}
</style>
</head>
<body style="font-family:Arial, Helvetica, sans-serif">
<div align="center">
<table width="595" style="width:595px;text-align:'.get_option('wpp_save_text_align').'" border="0"  bgcolor="#FFFFFF" cellspacing="1" cellpadding="5" >
  <tr>
    <td  bgcolor="#FFFFFF" style="font-size:12px; color:#000000;">
	'.$pt_header.'
	</td>
  </tr>
  <tr>
    <td bgcolor="#FFFFFF" style="font-size:14px; color:#000000;">
	<h1>'. $wpp_title .'</h1>
	'.str_replace('<img ','<img style="padding:5px; border:1px solid #CCCCCC; background:#F5F5F5; float:left; margin-right:40px;" ', $wpp_featured_image).' 
	'.(($wpp_price_html) ? '<p style="font-size:20px; color:#006600;">' .$wpp_price.'</p>
	' : '').''.(($wpp_sku) ? '<p style="font-size:14px;"><b>SKU</b>: '.$wpp_sku.'</p>
	' : '' ).''.(($wpp_stock) ? '<p style="font-size:14px;"><b>In stock</b>: '.$wpp_stock.'</p>
	' : '' ).''.(($wpp_rating) ? '<p style="font-size:14px;"><b>Rating</b>: '.$wpp_rating.'</p>
	' : '' ).''.(($wpp_categories) ? '<p style="font-size:14px;"><b>Product Categories</b>: '.$wpp_categories.'</p>
	' : '' ).''.(($wpp_tags) ? '<p style="font-size:14px;"><b>Product Tags</b>: '.$wpp_tags.'</p>
	' : '' ).''.(($wpp_permalink) ? '<p style="font-size:14px;"><b>Product Page</b>: <a href="'.$wpp_permalink.'">'.$wpp_permalink.'</a></p>
	' : '' ).'
	<p class=MsoNormal>&nbsp;</p><div style="clear:both"></div>
	'.(($wpp_has_variants) ? "<h2 style=\"color:#333333\">Product Variants</h2><p>".$wpp_variants.'</p>
	' : "\r\n" ).''.(($wpp_summary) ? "<h2 style=\"color:#333333\">Product Summary</h2><p>".$wpp_summary.'</p>
	' : '' ).''.(($wpp_description) ? "<h2 style=\"color:#333333\">Product Description</h2><p>".$wpp_description.'</p>
	' : '' ).''.((trim(strip_tags($attribute_html))) ? "<h2 style=\"color:#333333\">Product Attributes</h2><p>".$attribute_html.'</p>
	' : '' ).''.((!empty($wpp_gallery)) ? "<h2 style=\"color:#333333\">Product Gallery</h2>".str_replace('<img ','<img style="padding:5px; border:1px solid #CCCCCC; background:#F5F5F5; float:left; margin-right:5px;" ', $wpp_gallery_images) .'
	' : '' ).'
	<p class=MsoNormal>&nbsp;</p>
	<div style="clear:both"></div>
	</td>
  </tr>
  <tr>
    <td  bgcolor="#FFFFFF" style="font-size:13px; color:#000000;">
	'.$pt_links.'
	</td>
  </tr>
  <tr>
    <td  bgcolor="#FFFFFF" style="font-size:13px; color:#000000;">
	'.$pt_date.'
	'.$pt_md_date.'
	</td>
  </tr>
  <tr>
    <td bgcolor="#FFFFFF" style="font-size:12px; color:#000000">
	 <div id="hidden_" align="right">
	 <a href="javascript:window.print()">
	 <img src="'.WPP_PATH.'img/'.get_option( 'wpp_save_print_icon_url').'" style="border:#999999 solid 1px;" align="Print this Post" title="Print this Post"/></a>
	 </div>
	</td>
  </tr>
  <tr>
    <td  bgcolor="#FFFFFF" style="font-size:12px; color:#000000;">
	'.$pt_footer.'
	</td>
  </tr>
</table>
</div><br>
</body>
</html>';

?>