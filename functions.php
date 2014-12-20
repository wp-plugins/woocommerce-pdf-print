<?php

if( !function_exists('wpp_allow_1') ){
	function wpp_allow_1(){
	}
}

###########################################################################################################
function wpp_img_url($content){
	
	preg_match_all('|<img\s+[^>]*src=[\"\']+([^>\"\']+)[\"\']+\s+[^>]*>|i', $content, $body_array, PREG_SET_ORDER);
	preg_match('|^(.+)'.basename(WP_CONTENT_URL).'\/.+$|i', WPP_PATH, $img_url_array);
	if( !empty($body_array) ) {
			
		for ($i=0; $i < count($body_array); $i++){
				
			if( strpos($body_array [$i][1],'http') === FALSE ){
				if( preg_match('|^(.*)'.basename(WP_CONTENT_URL).'\/(.+)$|i', $body_array[$i][1],$img_curr_url_array) ){
					$img[$body_array[$i][1]] = $img_url_array[1]. basename(WP_CONTENT_URL) . '/'.$img_curr_url_array[2];
				}
				else{
					$img[$body_array[$i][1]] = $img_url_array[1].$body_array[$i][1];
				}
			}
			else{
				$img[$body_array[$i][1]] = $body_array [$i][1];	
			}
		}
	}
	return $img;
}

###########################################################################################################
if( !function_exists('file_extension') ){
	function file_extension($filename)
	{
		return substr(strrchr($filename, '.'), 1);
	}
}
###########################################################################################################

function wpp_textmaker($text)
{
	$search = array ("|\n|i");
	$replace = array ("<br> ");
	$text = preg_replace($search,$replace,$text);
	
	$search_2 = array ("|(h\d>)[\s\t\r\n]*<br[^>]*>|i",
					   "|(p>)[\s\t\r\n]*<br[^>]*>|i",
					   "|(ul>)[\s\t\r\n]*<br[^>]*>|i",
					   "|(ol>)[\s\t\r\n]*<br[^>]*>|i",
					   "|(li>)[\s\t\r\n]*<br[^>]*>|i");
					   
	$replace_2 = array ("$1","$1","$1","$1","$1");
	$text=preg_replace($search_2,$replace_2,$text);
	
	return $text;
}

###########################################################################################################
function wpp_print( $print = true ) {

			global $post;
			if( $post->post_status != 'publish' ) return false;
			
			if( is_single() || is_page() ){
				if(post_password_required($post->ID)){
					return false;
				}
			}
			
			$wpp_follow = ( get_option( 'wpp_onoff_save_follow' ) ) ? '' : 'rel="nofollow"';
			
			###################################################
			$print_string = stripslashes( get_option( 'wpp_save_print_button_text' ) );
			
			if( strpos(get_permalink(), '?') === FALSE ){ $upx = get_permalink() . '?'; } else{ $upx = get_permalink() . '&'; }
			
			$button['string']['print'] = '<a href="'.$upx.'wpp_export=print" target="_blank" '.$wpp_follow.'>'.$print_string.'</a>';
				/////////////////////////////////////////////////
			$button['icon']['print'] = '<a href="'.$upx.'wpp_export=print" target="_blank" '.$wpp_follow.'><img src="'.WPP_PATH.'img/'.get_option( 'wpp_save_print_icon_url').'" align="absmiddle" border="0" align="Print this Post" title="Print this Post" /></a>';
				//////////////////////////////////////////////////
			$button['button']['print'] = '<a href="'.$upx.'wpp_export=print" target="_blank" '.$wpp_follow.'><img src="'.WPP_PATH.'img/'.get_option( 'wpp_save_print_button_url').'" style="padding:2px" align="absmiddle" border="0" align="Print this Post" title="Print this Post" /></a>';
			
			####################################################
			if( get_option( 'wpp_onoff_print_manager' ) ) {
		
					if( get_option( 'wpp_print_type' ) ) {
						//Button
						if( get_option( 'wpp_save_print_button_type' ) ) {
							//button
							$sm_print = $button['button']['print'];
						}
						else {
							//icon
							$sm_print = $button['icon']['print'];
						}
					}
					else {
						//String
						$sm_print = $button['string']['print'];
					}
			}
			
			$sm = '<div class="wpp_print">'.$sm_print.'</div>';
			
			#################################################################
			if( is_single() && get_option( 'wpp_print_in_post' ) ) {
			
			if( get_option( 'wpp_onoff_print_manager' ) && get_option( 'wpp_print_location_postend' ) ) {

					//////////////////////////////
					if( $print && get_option( 'wpp_print_location_custom' ) ) { 
						print( $sm ); 
					}
					elseif( $print && !get_option( 'wpp_print_location_custom' ) ) { 
						return false;
					}
					else {
						$sm = '<br><br>'.$sm.'<br>';
						return $sm;
					}
					//////////////////////////////
				}
			}
			elseif ( is_page() && !is_front_page() && get_option( 'wpp_print_in_page' ) ) {
				if( get_option( 'wpp_onoff_print_manager' ) && get_option( 'wpp_print_location_postend' ) ) {
				
					//////////////////////////////
					if( $print && get_option( 'wpp_print_location_custom' ) ) { 
						print( $sm ); 
					}
					elseif( $print && !get_option( 'wpp_print_location_custom' ) ) { 
						return false;
					}
					else {
						$sm = '<br><br>'.$sm.'<br>';
						return $sm;
					}
					//////////////////////////////
			}
			##################################################################
	}
}



function wpp_save( $print = true) {
			
			global $post;
			if( $post->post_status != 'publish' ) return false;
			###################################################
			$wpp_follow = ( get_option( 'wpp_onoff_save_follow' ) ) ? '' : 'rel="nofollow"';
			$doc_string = stripslashes( get_option( 'wpp_save_doc_button_text' ) );
			$pdf_string = stripslashes( get_option( 'wpp_save_pdf_button_text' ) );
			$print_string = stripslashes( get_option( 'wpp_save_print_button_text' ) );
			
			if( strpos(get_permalink(), '?') === FALSE ){ $upx = get_permalink() . '?'; } else{ $upx = get_permalink() . '&'; }
			
			$button['string']['doc'] = '<a href="'.$upx.'wpp_export=doc" '.$wpp_follow.'>'.$doc_string.'</a>';
			$button['string']['pdf'] = '<a href="'.$upx.'wpp_export=pdf" '.$wpp_follow.'>'.$pdf_string.'</a>';
			$button['string']['print'] = '<a href="'.$upx.'wpp_export=print" target="_blank" '.$wpp_follow.'>'.$print_string.'</a>';
				/////////////////////////////////////////////////
			$button['icon']['doc'] = '<a href="'.$upx.'wpp_export=doc" '.$wpp_follow.'><img src="'.WPP_PATH.'img/'.get_option( 'wpp_save_doc_icon_url').'" align="absmiddle" border="0" align="'.$doc_string.'" title="'.$doc_string.'" /></a>';
			$button['icon']['pdf'] = '<a href="'.$upx.'wpp_export=pdf" '.$wpp_follow.'><img src="'.WPP_PATH.'img/'.get_option( 'wpp_save_pdf_icon_url').'" align="absmiddle" border="0" align="'.$pdf_string.'" title="'.$pdf_string.'" /></a>';
			$button['icon']['print'] = '<a href="'.$upx.'wpp_export=print" target="_blank" '.$wpp_follow.'><img src="'.WPP_PATH.'img/'.get_option( 'wpp_save_print_icon_url').'" align="absmiddle" border="0" align="'.$print_string.'" title="'.$print_string.'" /></a>';
				//////////////////////////////////////////////////
			$button['button']['doc'] = '<a href="'.$upx.'wpp_export=doc" '.$wpp_follow.'><img src="'.WPP_PATH.'img/'.get_option( 'wpp_save_doc_button_url').'" style="padding:2px" align="absmiddle" border="0" align="'.$doc_string.'" title="'.$doc_string.'" /></a>';
			$button['button']['pdf'] = '<a href="'.$upx.'wpp_export=pdf" '.$wpp_follow.'><img src="'.WPP_PATH.'img/'.get_option( 'wpp_save_pdf_button_url').'" style="padding:2px" align="absmiddle" border="0" align="'.$pdf_string.'" title="'.$pdf_string.'" /></a>';
			$button['button']['print'] = '<a href="'.$upx.'wpp_export=print" target="_blank" '.$wpp_follow.'><img src="'.WPP_PATH.'img/'.get_option( 'wpp_save_print_button_url').'" style="padding:2px" align="absmiddle" border="0" align="'.$print_string.'" title="'.$print_string.'" /></a>';
			
			#################################################################
			if( get_option( 'wpp_onoff_saving_doc' ) ) {
		
				if( get_option( 'wpp_saving_type' ) ) {
					//Button
					if( get_option( 'wpp_save_doc_button_type' ) ) {
						//button
						$sm_doc = $button['button']['doc'];
					}
					else {
						//icon
						$sm_doc = $button['icon']['doc'];
					}
				}
				else {
					//String
					$sm_doc = $button['string']['doc'];
				}
			}
			#################################################################
			if( get_option( 'wpp_onoff_saving_pdf' ) ) {
		
				if( get_option( 'wpp_saving_type' ) ) {
					//Button
					if( get_option( 'wpp_save_pdf_button_type' ) ) {
						//button
						$sm_pdf = $button['button']['pdf'];
					}
					else {
						//icon
						$sm_pdf = $button['icon']['pdf'];
					}
				}
				else {
					//String
					$sm_pdf = $button['string']['pdf'];
				}
			}
			#################################################################
			if( get_option( 'wpp_onoff_print_manager' ) ) {
		
				if( get_option( 'wpp_print_app' ) ) {
				
					if( get_option( 'wpp_print_type' ) ) {
						//Button
						if( get_option( 'wpp_save_print_button_type' ) ) {
							//button
							$sm_print = $button['button']['print'];
						}
						else {
							//icon
							$sm_print = $button['icon']['print'];
						}
					}
					else {
						//String
						$sm_print = $button['string']['print'];
					}
				}
			}
			
			
			$sm = str_replace(array('  &nbsp;  &nbsp;  &nbsp;  &nbsp;  &nbsp;', '  &nbsp;  &nbsp;  &nbsp;  &nbsp;', '  &nbsp;  &nbsp;  &nbsp;', '  &nbsp;  &nbsp;',), '  &nbsp;', $sm_pdf.'  &nbsp;'.$sm_doc.' &nbsp;'.$sm_print);
			
			if( is_product() ) {
				if( get_option( 'wpp_onoff_saving_manager' )) {
					if( get_option('wpp_save_pdf_button_type') && strpos($sm,'000/') !== FALSE ) {
						echo "<style> #wpp-buttons img { border-radius: 3px; box-shadow: 0 1px 4px rgba(0, 0, 0, 0.2); } </style>";
					}
					else{
						echo "<style> #wpp-buttons img { padding-right: 5px; } </style>";
					}
					if( $print && get_option( 'wpp_saving_location_custom' ) ) { 
						print( $sm ); 
					}
					else {
						return $sm ;
					}
				}
			}
			
			##################################################################
}

function wpp_text_filter( $wpp_body ){
	$wpp_body = stripslashes($wpp_body);
	$wpp_body = wpp_linkifyYouTubeURLs($wpp_body );
	$wpp_body = str_replace( "’" , "'", $wpp_body );
	$wpp_body =  do_shortcode( $wpp_body );
	$wpp_body = apply_filters('the_content', $wpp_body);
	$xsearch = array ( "'<script[^>]*?>.*?</script>'si", "'<style[^>]*?>.*?</style>'si",  "'<head[^>]*?>.*?</head>'si", "'<link[^>]*?>.*?</link>'si", "'<link[^>]*?>'si", "'<object[^>]*?>.*?</object>'si"); 
	$xreplace = array ( "", "", "", "", "", "");                 
	$wpp_body = preg_replace($xsearch, $xreplace, $wpp_body);
	return $wpp_body;
}

function wpp_export( $export ) {
	
	error_reporting(0);
	ob_clean();
	$export = addslashes(substr(strip_tags($_GET[ 'wpp_export' ]), 0, 5));
	@set_time_limit (864000);
	if(ini_get('max_execution_time')!=864000)@ini_set('max_execution_time',864000);
	
	$post_id = url_to_postid( $_SERVER['REQUEST_URI'] );
	$post = get_post( $post_id ); 
	if( $post->post_status != 'publish' ) return false;
	if( post_password_required($post->ID)) return false;
	$product = get_product( $post->ID );
	$out = '';
	if ( function_exists('has_post_thumbnail') && has_post_thumbnail($post->ID) ) { $thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'shop_single' ); $wpp_featured_image = ($thumbnail[0]) ? $thumbnail[0] : $thumbnail[0]; } else{ $wpp_featured_image = ''; }
	if( $thumbnail[1] ) $wpp_featured_image = '<img width="'.$thumbnail[1].'" height="'.$thumbnail[2].'" src="'.$thumbnail[0].'"/>';
	
	$wpp_price_html = strip_tags($product->get_price_html());
	$wpp_title = $post->post_title;
	$wpp_sku = $product->get_sku();
	$wpp_permalink = $product->get_permalink();
	$wpp_stock = $product->get_stock_quantity();
	$wpp_rating = strip_tags($product->get_rating_html());
	$wpp_categories = $product->get_categories(', ');
	$wpp_tags = $product->get_tags(', ');
	$wpp_summary = wpp_text_filter( $post->post_excerpt );
	$wpp_description = wpp_text_filter( $post->post_content );
	
	$attribute_html = "\r\n<ul>";
	if($product->enable_dimensions_display()) { 
		$attribute_html .= (($product->get_dimensions()) ? '<li>Dimensions: '.$product->get_dimensions()."\r\n</li>" : ''); 
	}
	$attribute_html .= (($product->get_weight()) ? '<li>Weight: '.$product->get_weight().' '.get_option('woocommerce_weight_unit')."\r\n</li>" : '');
	if( $product->has_attributes() ){ 
		$wpp_attributes = $product->get_attributes(); 
		if(!empty($wpp_attributes)){
			foreach( $wpp_attributes as $attribute){
				if( $attribute['is_taxonomy'] ){
					$product_terms = wp_get_post_terms( $post->ID, $attribute['name'] ); $p_terms = array();
					$attribute_name = ucwords(str_replace('attribute_','',str_replace('pa_','',$attribute['name'])));
					foreach($product_terms as $p_term){
						$p_terms[] = $p_term->name;
					}
					$attribute_html .= '<li>'.$attribute_name.': '.implode(', ', $p_terms)."\r\n</li>";
				}
				else{
					$attribute_html .= '<li>'.$attribute['name'].': '.$attribute['value']."\r\n</li>";
				}
			}
		}
	}
	$attribute_html .= '</ul>';
	$attribute_html = str_replace(array(' |', '|'),',',$attribute_html);
	$wpp_has_variants = false;
	$wpp_variants = "\r\n<ul>";
	$args = array( 'post_type' => 'product_variation', 'post_status' => 'publish', 'numberposts' => -1, 'orderby'  => 'menu_order', 'order'  => 'asc', 'post_parent'   => $post->ID);
	$variations = get_posts( $args ); 
    if(!empty($variations)){
		$wpp_has_variants = true;
		foreach ( $variations as $variation ) {
			$vars = new WC_Product_Variation($variation->ID);
			$wpp_variants .= '<li>'. str_replace($vars->get_sku(), '', html_entity_decode(strip_tags(wpp_text_filter(str_replace( '&ndash;', ' - ', $vars->get_formatted_name())) ), ENT_QUOTES, "UTF-8") ) ."\r\n</li>";
			unset($vars);
		}
		$wpp_variants .= '</ul>';
		$wpp_variants = str_replace(array('<li>  -  ',',   -  '), array('<li>','  -  '), $wpp_variants);
	}
	
	if( $product_meta = get_post_meta( $post->ID ) ){
		foreach( $product_meta as $meta_key => $meta_value ){
			if( strpos($meta_key, '_wpt_field_') !== FALSE ){
				$custom_tab_slug = str_replace('_wpt_field_', '', $meta_key);
				$custom_tab = get_posts(array( 'name' => $custom_tab_slug, 'posts_per_page' => 1, 'post_type' => 'woo_product_tab', 'post_status' => 'publish' ));
				if(strip_tags(trim($meta_value[0]))){
					$custom_tab_title_html[] = $custom_tab[0]->post_title;
					$custom_tab_content_html[] = wpp_text_filter($meta_value[0]);
				}
			}
			continue;
		}
	}
	
	
	$wpp_gallery = $product->get_gallery_attachment_ids();
	foreach( $wpp_gallery as $wpp_img ){
		$wpp_gallery_images .= '&nbsp;'.wp_get_attachment_image( $wpp_img, 'shop_single' ) . '&nbsp;';
	}
	
	
	
	if( $export == 'doc' ) {
		
		$doc_title = stripslashes( $post->post_title );
		$wpp_description = stripslashes( $wpp_description );
		$wpp_price = 'Price: ' . html_entity_decode(str_replace( '&ndash;', ' - ', $product->get_price_html()), ENT_QUOTES, "UTF-8");
		if( get_option('wpp_save_doc_img_max_width') ){ $wpp_max_image_width = get_option('wpp_save_doc_img_max_width'); } else{ $wpp_max_image_width = 500; }
		if( preg_match_all('|width=[\'\"]*(\d+)[\'\"]*[^\>\<]+height=[\'\"]*(\d+)[\'\"]*|i', $wpp_description, $doc_img_sizes, PREG_SET_ORDER ) ){ foreach( $doc_img_sizes as $doc_img ){ $doc_img_w = $doc_img[1]; $doc_img_h = $doc_img[2]; if( (int)$wpp_max_image_width < (int)$doc_img_w ){ $doc_img_h = $doc_img_h * ( $wpp_max_image_width / $doc_img_w ); $doc_img_w = $wpp_max_image_width; } $wpp_description = preg_replace('|width=[\'\"]*'.$doc_img[1].'[\'\"]*[^\>\<]+height=[\'\"]*'.$doc_img[2].'[\'\"]*|i', 'width="'.$doc_img_w.'" height="'.$doc_img_h.'"', $wpp_description );}}
		$wpp_description = preg_replace('|\[caption[^\]]+\]([^\]]*)\[\/caption\]|is', '$1', $wpp_description);
		$wpp_description = preg_replace( array('|<embed[^>]+>[^>]*<\/embed>|i', '|<object[^>]+>|i','|<\/object>|i', '|<param[^>]+>|i', '|<script[^>]+>[^>]*<\/script>|i'),'', $wpp_description );
		preg_match_all('|<img[^>]+(class[\s\n\t\r]*=[\s\n\t\r]*[\'\"]+[^\'\">]+[\'\"]+)[^>]*>|i', $wpp_description, $body_array, PREG_SET_ORDER);
		if( !empty($body_array) ) {
			for ($i=0; $i < count($body_array); $i++){
			    $replace = preg_replace( '|(.+)class[\s\n\t\r]*=[\s\n\t\r]*[\'\"]+align([^\s\t\r\n\'\">]+)[^\'\">]*[\'\"]+(.+)|i', '</span><![if !vml]> $1 align = \'$2\' $3 <![endif]><span style=\'font-size:10.5pt;color:#666666;\'>', $body_array [$i][0] );
				$wpp_description = str_replace( $body_array [$i][0], $replace , $wpp_description);
			}
		}
		$wpp_doc_urls = wpp_img_url( $wpp_description );
		if(!empty($wpp_doc_urls)){ foreach( $wpp_doc_urls as $k => $v ){ $wpp_description = str_replace( $k, $v, $wpp_description ); }}
		$doc_post_date = $post->post_date;
    	$doc_post_date_gmt = $post->post_date_gmt;
		$doc_modified_date = $post->post_modified;
		$doc_modified_date_gmt = $post->post_modified_gmt;
		if(!get_option('wpp_doc_t_image')){ $wpp_description = preg_replace( '|<img[^><]+>|i','', $wpp_description ); }
		$doc_t_title = (($doc_title && get_option('wpp_doc_t_title'))? '<h2 style="color:#000000;text-align:'.get_option('wpp_save_text_align').'">'.$doc_title.'</h2><br>' :'');
		$doc_t_date = ((get_option('wpp_doc_t_date'))?'Post date: '.$doc_post_date.'<br>Post date GMT: '.$doc_post_date_gmt.'<br>':'');
		$doc_t_md_date = ((get_option('wpp_doc_t_md'))?'Post modified date: '.$doc_modified_date.'<br>
		Post modified date GMT: '.$doc_modified_date_gmt:'');
		if( get_option('wpp_save_doc_template') ){
			require( WPP_FOLDER . 'template/save_as_word_document.php' );
		}
		else{
			require( WPP_FOLDER . 'template/save_as_word_document_oo.php' );
		}
		$doc = trim($doc);
		$length = strlen( $doc );
		$file = trim( str_replace( ' ', '-' , trim($doc_title) ), '-' );
		$file = $file.'.doc';
		header ('Content-Type: application/msword; charset=utf-8' );
		if(headers_sent()) echo ('Some data has already been output, can\'t send MS Doc file');
		header('Content-Length: '.$length );
		header ('Content-Disposition: attachment; filename='.$file );
		header('Cache-Control: private, max-age=0, must-revalidate');
		header('Pragma: public');
		ini_set('zlib.output_compression','0');
		echo $doc;
		
	}
	elseif( $export == 'pdf' ) {
		
		global $wpp_html_link;
		$html_title = stripslashes( $post->post_title );
		$wpp_html_link = stripslashes( $post->guid );
		//Price Style
		if( $product->is_on_sale() ){ $wpp_price = html_entity_decode(str_replace(array('<del>', '</del>'), array('^[font][Real Price: ', '][200,200,200][12]^<br>
 ^[font][Current Sale Price:'), str_replace( '&ndash;', ' - ', $product->get_price_html())), ENT_QUOTES, "UTF-8").'][15, 117, 84][12]^';
		} else{ $wpp_price = '^[font][Price: ' . html_entity_decode( strip_tags( $product->get_price_html()), ENT_QUOTES, "UTF-8").'][15, 117, 84][12]^'; }
		//Limiting image sizes
		preg_match_all('|<img[^>]+(class[\s\n\t\r]*=[\s\n\t\r]*[\'\"]+[^\'\">]+[\'\"]+)[^>]*>|i', $html_body, $body_array, PREG_SET_ORDER);
		if( !empty($body_array) ) {
			for ($i=0; $i < count($body_array); $i++){
				$replace = preg_replace( '|(.+)class[\s\n\t\r]*=[\s\n\t\r]*[\'\"]+align([^\s\t\r\n\'\">]+)[^\'\">]*[\'\"]+(.+)|i','$1 align = "$2" style="padding:5px" $3', $body_array [$i][0] );
				$html_body = str_replace( $body_array [$i][0], $replace , $html_body);
			}
		}
		$html_body = preg_replace('|<noscript>.+?</noscript>|is', '', $html_body);
		$html_body = preg_replace( "|\r|", "<br>" , $html_body);
		$wpp_pdf_urls = wpp_img_url( $html_body );
		if(!empty($wpp_pdf_urls)){
			foreach( $wpp_pdf_urls as $k => $v ){
				if( strpos( $k, '../' ) === FALSE ){
					$html_body = str_replace( $k, $v, $html_body );
				}
				else{
					continue;
				}
			}
		}
		require( WPP_FOLDER . 'template/save_as_pdf.php' );
		require( WPP_FOLDER . 'pdf.php' );
	}
	elseif( $export == 'print' ) {
	
		$print_title = stripslashes( $post->post_title );
		$wpp_description = stripslashes( $wpp_description );
		$wpp_price = 'Price: ' . html_entity_decode(str_replace( '&ndash;', ' - ', $product->get_price_html()), ENT_QUOTES, "UTF-8");
		if( get_option('wpp_save_print_img_max_width') ){ $wpp_max_image_width = get_option('wpp_save_print_img_max_width'); } else{ $wpp_max_image_width = 500; }
		if( preg_match_all('|width=[\'\"]*(\d+)[\'\"]*[^\>\<]+height=[\'\"]*(\d+)[\'\"]*|i', $wpp_description, $print_img_sizes, PREG_SET_ORDER ) ){
			foreach( $print_img_sizes as $print_img ){
				$print_img_w = $print_img[1]; $print_img_h = $print_img[2];
				if( (int)$wpp_max_image_width < (int)$print_img_w ){ $print_img_h = $print_img_h * ( $wpp_max_image_width / $print_img_w ); $print_img_w = $wpp_max_image_width; }
				$wpp_description = preg_replace('|width=[\'\"]*'.$print_img[1].'[\'\"]*[^\>\<]+height=[\'\"]*'.$print_img[2].'[\'\"]*|i', 'width="'.$print_img_w.'" height="'.$print_img_h.'"', $wpp_description );
			}
		}
		$wpp_description = preg_replace('|\[caption[^\]]+\]([^\]]*)\[\/caption\]|is', '$1', $wpp_description);
		$wpp_description = preg_replace( array('|<embed[^>]+>[^>]*<\/embed>|i', '|<object[^>]+>|i','|<\/object>|i', '|<param[^>]+>|i', '|<script[^>]+>[^>]*<\/script>|i'),'', $wpp_description );
		if(get_option('wpp_pt_links')){
			preg_match_all("|<a href[\s\n\t\r]*=[\s\n\t\r]*[\'\"]+([^>\'\"]+)[\'\"]+[^><]*>([^><]+)</a>|i",$wpp_description,$links_array,PREG_SET_ORDER);
			if( !empty($links_array) ) {
				for ($i=0; $i < count($links_array); $i++){
					$print_links .= '<li>'.wpp_phrase_spliter( $links_array[$i][1], 50, ' ', false ).'</li>';
					$wpp_description = str_replace( $links_array[$i][0],'<u>'.$links_array[$i][0].'</u> <sup>'.($i+1).'</sup>',$wpp_description);
				}
			}
		}
		preg_match_all('|<img[^>]+(class[\s\n\t\r]*=[\s\n\t\r]*[\'\"]+[^\'\">]+[\'\"]+)[^>]*>|i', $wpp_description, $body_array, PREG_SET_ORDER);
		if( !empty($body_array) ) {
			for ($i=0; $i < count($body_array); $i++){
				$replace = preg_replace( '|(.+)class[\s\n\t\r]*=[\s\n\t\r]*[\'\"]+align([^\s\t\r\n\'\">]+)[^\'\">]*[\'\"]+(.+)|i','$1 align = "$2" style="padding:5px" $3', $body_array[$i][0] );
				$wpp_description = str_replace( $body_array[$i][0], $replace , $wpp_description);
			}
		}
		$print_excerpt = stripslashes( $post->post_excerpt );
		$print_post_date = $post->post_date;
    	$print_post_date_gmt = $post->post_date_gmt;
		$print_modified_date = $post->post_modified;
		$print_modified_date_gmt = $post->post_modified_gmt;
		if(!get_option('wpp_pt_image')){
			 $wpp_description = preg_replace( '|<img[^><]+>|i','', $wpp_description );
		}
		$pt_header = (get_option('wpp_pt_head_site'))? '<a href="'.get_option('siteurl').'" target="_blank" class="wpp_header_2">'.get_option('blogname').'</a> <br/>': '';
		$pt_header .= (get_option('wpp_pt_head_url'))? '<a href="'.get_permalink($post->ID).'" target="_blank" class="wpp_header_3">'.get_permalink($post->ID).'</a> <br />': '';
		$pt_header .= (get_option('wpp_pt_head_date'))? 'Export date: '. date("D M j G:i:s Y / O ") .' GMT<br />': '';
		$pt_header .= '<hr />';
		$pt_title = 
		(($print_title && get_option('wpp_pt_title'))? '<h2 style="text-align:'.get_option('wpp_save_text_align').'">'.$print_title.'</h2><br>' :'');
		$pt_excerpt = 
		(($print_excerpt && get_option('wpp_pt_excerpt'))?'<strong>Excerpt:</strong> '.$print_excerpt:'');
		$pt_links = 
		(($print_links && get_option('wpp_pt_links'))?'<strong>Links:</strong> <ol type="1">'.$print_links.'</ol>':'');
		$pt_date = 
		((get_option('wpp_pt_date'))?'Post date: '.$print_post_date.'<br>Post date GMT: '.$print_post_date_gmt.'<br><br>':'');
		$pt_md_date = 
		((get_option('wpp_pt_md'))?'Post modified date: '.$print_modified_date.'<br>
		Post modified date GMT: '.$print_modified_date_gmt:'');
		$pt_footer = ((get_option('wpp_pt_header'))?'<br>Export date: '. date("D M j G:i:s Y / O ") .' GMT
		<br> This page was exported from '.get_option('blogname').' 
		[ <a href="'.(str_replace( array('&wpp_export=print','?wpp_export=print'), '', $_SERVER['REQUEST_URI'] )).'" target="_blank">'.get_option('siteurl').'</a> ]<hr/>
		Export of Post and Page has been powered by [ WooCommerce PDF &amp; Print ] plugin by 
		<a href="http://www.gVectors.com" target="_blank">www.gVectors.com</a>':'');
		require( WPP_FOLDER . 'template/print.php' );
		echo $print; 
	}
	exit;
}

function wpp_saving_buttons(){
	if( !is_admin() ) {
		$sm = wpp_save( false );
	}
	echo '<div style="clear:both;margin:0px;padding:0px;border:none;"></div><div id="wpp-buttons" style="display:block; margin:12px 10px 10px auto;">'.$sm.'</div><div style="clear:both;margin:0px;padding:0px;border:none;"></div>';
}
	
################################################################################################################
function wpp_filter_manager ( $string ) { 

	global $wpdb;
	global $allowedposttags;
	global $additional_tags;
	global $post;
	global $userdata;
	
	if( is_admin() ) {
		$wpp_userdata = $userdata;
	}
	else {
		if(!IS_WPMU){
			$wpp_user = wp_get_current_user();
			$wpp_array = $wpp_user->roles;
		}
		else{
			$wpp_userdata = get_userdata( $post->post_author );
			$wpp_array = $wpp_userdata->wp_capabilities;
		}
	}
	if( $wpp_array[0] == '' )$wpp_array[0] = 'administrator';
	$wpp_user_role = array_keys( $wpp_array );
	$allowedposttags = array_merge( $allowedposttags, $additional_tags );
	$string = stripslashes(trim( $string ));
	( is_page() ) ? $mode = 'page': $mode = 'post';
	
	///////////////////////////////////////////////////////////
	///////////////// Filter Manager //////////////////////////
	///////////////////////////////////////////////////////////
	$mode_filter_post = false;
	$mode_filter_page = false;
	if( get_option( 'wpp_onoff_filter_manager' ) ) {
		if( get_option( 'wpp_filter_role_'.$wpp_user_role[0] )) { 
			if( get_option( 'wpp_phrase_filter_post' ) && $mode == 'post' ) { $mode_filter_post = true; }
			if( get_option( 'wpp_phrase_filter_page' ) && $mode == 'page' ) { $mode_filter_page = true; }
		}
		if( ( $mode == 'post' && $mode_filter_post ) || ( $mode == 'page' && $mode_filter_page ) ) {
			///////////////////////////////////////////////////////////////
			if( get_option( 'wpp_onoff_phrase_filter' ) ) {
				$wpp_res = $wpdb->get_results("SELECT `phrase`, `replace` FROM `".$wpdb->prefix."wpp_filter` ");
				if( !empty($wpp_res) ){
					foreach ( $wpp_res as $res ) {
						$find = addslashes(stripslashes($res->phrase));
						$replace = stripslashes($res->replace);
						$string = preg_replace( "|\b$find\b|i", $replace, $string );
					}
				}
			}
			/////////////////////////////////////////////////////////////////
			if( get_option( 'wpp_onoff_text_modifier' ) ) {
				$string = wpp_shortcut ( $string );
			}
			/////////////////////////////////////////////////////////////////
			if( get_option( 'wpp_onoff_long_phrase' ) ) {
				$max_length = get_option( 'wpp_filter_longphrase_maxlength' );
				$after = get_option( 'wpp_filter_longphrase_after' );
				( $after == 'divide' ) ? $return_first_part = false : $return_first_part = true ;
				$tmp_string = strip_tags( $string );
				$tmp_array = explode( ' ', $tmp_string );
				foreach ( $tmp_array as $tmp_phrase ) {
					if( mb_strlen( $tmp_phrase , 'utf-8') > $max_length ) {
						$tmp_short_replace = wpp_phrase_spliter( $tmp_phrase, $max_length, ' ', $return_first_part );
						$string = str_replace( $tmp_phrase, $tmp_short_replace, $string );
					}
				}
			}
			/////////////////////////////////////////////////////////////////
		}
	}
	return $string;
}

##################################################################################################
function wpp_arraychecker($var,$arr)
{
	$rv=false;
	for($r=0;$r<count($arr);$r++)
	{
		if($arr[$r]==$var){$rv=true;break;}
	}
	return $rv;
}
#######################################################################################################
function wpp_linkifyYouTubeURLs($text) {
    $text = preg_replace('~
        # Match non-linked youtube URL in the wild. (Rev:20130823)
        https?://         # Required scheme. Either http or https.
        (?:[0-9A-Z-]+\.)? # Optional subdomain.
        (?:               # Group host alternatives.
          youtu\.be/      # Either youtu.be,
        | youtube         # or youtube.com or
          (?:-nocookie)?  # youtube-nocookie.com
          \.com           # followed by
          \S*             # Allow anything up to VIDEO_ID,
          [^\w\s-]        # but char before ID is non-ID char.
        )                 # End host alternatives.
        ([\w-]{11})       # $1: VIDEO_ID is exactly 11 chars.
        (?=[^\w-]|$)      # Assert next char is non-ID or EOS.
        (?!               # Assert URL is not pre-linked.
          [?=&+%\w.-]*    # Allow URL (query) remainder.
          (?:             # Group pre-linked alternatives.
            [\'"][^<>]*>  # Either inside a start tag,
          | </a>          # or inside <a> element text contents.
          )               # End recognized pre-linked alts.
        )                 # End negative lookahead assertion.
        [?=&+%\w.-]*      # Consume any URL (query) remainder.
        ~ix', 
        '<p style="clear:both"> YouTube Video: <a href="http://www.youtube.com/watch?v=$1">YouTube.com/watch?v=$1</a> </p>',
        $text);
    return $text;
}
##################################################################################################
function wpp_filter_htmlsch( $str ) {
	return  $str = htmlspecialchars(trim( $str ));
}
##################################################################################################
function wpp_filter_strip( $str ) {
	return  $str = strip_tags(trim( $str ));
}
##################################################################################################
function wpp_filter_ss( $str ) {
	return  $str = htmlspecialchars(stripslashes(trim( $str )));
}
##################################################################################################
function wpp_phrase_spliter( $str, $length, $merging_string = ' ', $return_first_part = true ) {
	if( !$return_first_part ) {
		
		$str = str_replace(',', ', ', $str );
		$array_1 = explode( ' ', $str);
		foreach ($array_1 as $part_1) {
			if( mb_strlen( $part_1,'utf-8') > $length ) {
				$string_array = str_split( $part_1, $length );
				$part_1 = implode( $merging_string, $string_array);
				unset($string_array);
			}
			$new_string .= $part_1 .' ';
		}
	}
	else {
			if( mb_strlen( $str, 'utf-8') > $length ) {
				$new_string = substr( $str, 0, $length ) .'...';
			}
			else {
				$new_string = $str;
			}
	}
	return trim( $new_string );
}

?>