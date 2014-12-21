<?php 
error_reporting(0);
$fixer1 = array( '¥' => '&yen;', '€' => '&euro;', '₪' => 'ISH', '₭' => 'K', '₩' => 'W', '₦' => 'N', '₲' => 'G', '₱' => 'P', '£' => '&pound;', '฿' => 'B', '₺' => 'L', '₫' => 'D' );
$fixer2 = array( '$' => chr(36), '¥' => chr(165), '€' => chr(128), '₪' => 'ISH', '₭' => 'K', '₩' => 'W', '₦' => 'N', '₲' => 'G', '₱' => 'P', '£' => chr(163), '฿' => 'B', '₺' => 'L', '₫' => 'D', '&yen;' => chr(165), '&euro;' => chr(128), '&pound;' => chr(163) );
$fixer3 = array( '&yen;' => 'YEN ', '&euro;' => 'EUR ', '₪' => 'ISH ', '₭' => 'K ', '₩' => 'W ', '₦' => 'N ', '₲' => 'G ', '₱' => 'P ', '&pound;' => 'GBP ', '฿' => 'B ', '₺' => 'L ', '₫' => 'D ' );
$STR = strtr($STR, $fixer1);
$is_RUS = get_option('wpp_save_pdf_rus');
if( woopp_detect_cyr_utf8($STR) ) $is_RUS = true;
if( !get_option('wpp_save_pdf_img_show') ){ $STR = preg_replace( '|<img\s+[^>]*>|i', '', $STR ); }
if( get_option('wpp_save_pdf_img_max_width') ){ $wpp_max_image_width = get_option('wpp_save_pdf_img_max_width'); } else{ $wpp_max_image_width = 500; }
if( $is_RUS ){ $fontName = 'timesnewromanpsmt'; if( function_exists('mb_convert_encoding') ){$STR = mb_convert_encoding($STR, "windows-1251","UTF-8");}}
else{ $fontName = 'Arial'; if( function_exists('mb_convert_encoding') ){$STR = mb_convert_encoding($STR, "ISO-8859-1","UTF-8");} }
if( $is_RUS ) { $STR = strtr($STR, $fixer3); } else{ $STR = strtr($STR, $fixer2); }
$html_find = array("&quot;", "&amp;", "&lt;", "&gt;", "&euro;", "&prime;", "&nbsp;", "&ndash;");
$html_replace   = array("\"", "&", "<", ">", "evro","'"," ", "-");
$reg_img = '|<img\s+[^>]*src[\s]*=[\s]*[\"\']+([^>\"\']+)[\"\']+[^>]*>|i';
$reg_img_align = '|align[\s]*=[\s]*[\"\']+([^>\"\']+)[\"\'][^>]*|i';
$reg_img_height = '|height[\s]*=[\s]*[\"\']+([\d]+)[\"\'][^>]*|i';
$reg_img_width = '|width[\s]*=[\s]*[\"\']+([\d]+)[\"\'][^>]*|i';
$reg_a = '|<a href=[\"\']+([^>\"\']+)[\"\']+[^><]*>([^><]+)</a>|i';
$search = array ('|<li>([^><]+)<\/li>|i','|<strong>([^><]+)<\/strong>|i','|<b>([^><]+)<\/b>|i',
				'|<i>([^><]+)<\/i>|i','|<u>([^><]+)<\/u>|i','|<code>([^><]+)<\/code>|i',
				'|<br[\/]?>|i','|<h1>([^><]+)</h1>|i','|<h2>([^><]+)</h2>|i','|<h3>([^><]+)</h3>|i',
				'|<h4>([^><]+)</h4>|i');
$replace = array (  '^[li][$1]^','^[b][$1]^','^[b][$1]^','^[i][$1]^','^[u][$1]^','^[code][$1]^','^[_br_]^', '^[h1][$1]^','^[h2][$1]^','^[h3][$1]^','^[h4][$1]^');
################################################################################
//print_r($STR);
preg_match_all( $reg_img, $STR, $img_array, PREG_SET_ORDER );
$host = $_SERVER['HTTP_HOST'];
$PT = ( strtolower(substr($_SERVER["SERVER_PROTOCOL"],0,5)) == 'https' ) ? 'https' : 'http';
foreach ( $img_array as $value ) {
	$src = $value[1];
	$src = preg_replace( '|\?[^\`]*|', '', $src );
	preg_match( $reg_img_align, $value[0], $img_array_align );
	preg_match( $reg_img_height, $value[0], $img_array_height );
	preg_match( $reg_img_width, $value[0], $img_array_width );
	////////////////////////////////////////////////////////////////////////////////
	if( strpos($src, 'http://') === FALSE && strpos($src, 'https://') === FALSE ){
		$src = preg_replace( '|([\.\.\/]+)(.*)|i' , $PT.'://'.$host.'/$2', $src);
	}
	else{
	    $makeitlocal =  trim(get_option( 'siteurl' ), '/') . '/';
	    $src = str_replace($makeitlocal, '', $src);
	}
	////////////////////////////////////////////////////////////////////////////////
	if( (int)$wpp_max_image_width < (int)$img_array_width[1] ){
		$img_array_height[1] = $img_array_height[1] * ( $wpp_max_image_width / $img_array_width[1] );
		$img_array_width[1] = $wpp_max_image_width;
	}
	////////////////////////////////////////////////////////////////////////////////
	$STR = str_replace( $value[0] ,' ^[img]['.$src.']['.$img_array_align[1].']['.$img_array_height[1].']['.$img_array_width[1].']^ ', $STR);
	
}
preg_match_all( $reg_a, $STR, $a_array, PREG_SET_ORDER );
foreach ( $a_array as $value ) {
	$src = $value[1];
	$cont = str_replace( '^', '', $value[2] );
	$STR=str_replace( $value[0] , '^[a]['.$value[1].']['.$cont.']^', $STR);
}

$STR = preg_replace( $search ,$replace ,$STR );
$STR = trim(strip_tags($STR));
$STRINGS = explode( '^[_br_]^', $STR );

//print_r($STRINGS);
###############################################################################
function wpp_path( $file ){
	$f = fopen($file,'rb');
	if(!$f){
		$makeitlocal =  trim(get_option( 'siteurl' ), '/') . '/';
		$file = str_replace($makeitlocal, '', $file);
		$f = fopen($file,'rb');
		if( !$f && strpos($file, 'http') !== FALSE ){
			echo ('[WPP PDF Error] Please turn on "allow_url_fopen" on your server to allow insert images in PDF Document, more info here: http://stackoverflow.com/questions/3694240/add-allow-url-fopen-to-my-php-ini-using-htaccess');
		}
	}
	fclose($f);
	return $file;
}

function wpp_alpha( $file ){
	$file = wpp_path($file);
	$dir = basename(WP_CONTENT_URL) . '/uploads/wpp/';
	$file_jpg = basename(WP_CONTENT_URL) . '/uploads/wpp/' . str_replace('.png', '.jpg', strtolower(basename($file)));
	if(!is_dir($dir)) @mkdir($dir, 0775);
	if(file_exists($file_jpg)) @unlink($file_jpg);
	$jpeg = imagecreatefromjpeg($file_jpg);
	$png = imagecreatefrompng($file);
	list($width, $height) = getimagesize($file_jpg);
	list($newwidth, $newheight) = getimagesize($file);
	$out = imagecreatetruecolor($newwidth, $newheight);
	imagecopyresampled($out, $jpeg, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
	imagecopyresampled($out, $png, 0, 0, 0, 0, $newwidth, $newheight, $newwidth, $newheight);
	imagejpeg($out, $file_jpg, 100);
	if( file_exists($file_jpg) ){
		return $file_jpg;
	}
	else{
		return $file;
	}
}

function wpp_clean( $file ){
	if( strpos( $file, basename(WP_CONTENT_URL) . '/uploads/wpp/' ) !== FALSE ) {
		@unlink($file);
	}
}

require('pdf/fpdf.php');

class PDF extends FPDF
{

	function Header()
	{
		global $wpp_html_link;
		$header_text_1 = get_option( 'wpp_ph_page_exported_from' ) . ' - ';
		$header_text_2 = get_option('blogname');
		$header_text_3 = ' ';
		$header_text_4 = get_option('wpp_ph_exp_date') . ': '.date("D M j G:i:s Y / O ").' GMT';
		
		if( $is_RUS ){
			$fontName = 'timesnewromanpsmt';
			$this->AddFont('timesnewromanpsmt','','rtimes.php');
			if(function_exists('mb_convert_encoding')){$header_text_2 = mb_convert_encoding( get_option('blogname'), "windows-1251","UTF-8");}
		}
		else{
			$fontName = 'Times';
			if(function_exists('mb_convert_encoding')){$header_text_2 = mb_convert_encoding( get_option('blogname'), "ISO-8859-1","UTF-8");}
		}

		$this->SetFont($fontName,'',10);
		$this->SetTextColor(150,150,150);
		$this->Write(0, $header_text_1 );
		$this->SetFont('','U');
		$this->SetTextColor(140,140,140);
		$this->Write(0, str_replace('&amp;','&',$header_text_2), $wpp_html_link );
		$this->SetFont('','',8);
		$this->SetTextColor(150,150,150);
		$this->Write(0, $header_text_3 );
		$this->Ln(1);
		$this->Write(5, $header_text_4 );
		$this->Ln(15);
	}

	
	function Footer()
	{
		$footer_text_1 = '[ Export product details as PDF file has been powered by ';
		$footer_text_2 = ' WooCommerce PDF & Print ' ;
		$footer_text_3 = ' plugin. ]';
		$this->SetY(-10);
		//$this->Image(WPP_PATH.'img/mini.png',($this->GetX()+1.5),($this->GetY()+1.4),4,3,'gif','http://profprojects.com');
		$this->SetFont('Times','I',8);
		$this->SetTextColor(150,150,150);
		$this->Write(6, $footer_text_1 );
		$this->SetTextColor(130,130,130);
		$this->Write(6, $footer_text_2, 'http://www.gvectors.com/?page=wpp' );
		$this->SetTextColor(150,150,150);
		$this->Write(6, $footer_text_3 );
		$this->SetTextColor(50,50,50);
		$this->Cell(0,6,'|  Page '.$this->PageNo().'/{nb}  |',0,0,'R');
	}
}

$pdf=new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();

if( $is_RUS ){
	$pdf->AddFont('TimesNewRomanPSMT','','rtimes.php');
}

$pdf->SetFont( $fontName, '', 10 );
$pdf->SetTextColor(50,50,50);

###################################################################################################################
//print_r($STRINGS ); 
//exit();
$i=0;
foreach ( $STRINGS as $strrow ) {

	$strrow = str_replace($html_find, $html_replace, $strrow);
	//echo $strrow;
	$strrow = preg_replace('|\]\^[\r\n\s\t]+\^\[|is', "]^ ^[", $strrow);
	$strrow = preg_replace('|\r\n\r\n|is', "\r\n", $strrow);
	$strrow = preg_replace('|\n\n|is', "\n", $strrow);

	$array_row = explode( '^', $strrow );
	//print_r($array_row);
	foreach( $array_row as $row ){
	
		if( $is_RUS ) $row = str_replace(array('[strong]', '[b]', '[i]', '[u]'), '[font]', $row);
	
		switch( $row ){
			###################################################
			case strpos( $row, 'a][')>0 : 
			{
				$a_data = explode( '][', $row );
				if( $a_data[1] ) {
					
					if( strpos( $a_data[2], 'img')>0 ) {
					
						if( $a_data[3] ) {
						
							$X = trim(round($pdf->GetX(),0));
							$Y = trim(round($pdf->GetY(),0));
							
							////////////////////////////////////////////////////////
							$W = (trim(str_replace(']','',$a_data[6]))/5.2);
							$H = (trim($a_data[5])/5.2);
							if( $W && $H ){ 
								//size data exists
							} 
							else{
								$image = getimagesize( $a_data[3] );
								$W = trim($image[0])/5.2;
								$H = trim($image[1])/5.2;
							}
							if( !$W || !$H ) { $W = 40; $H = 40; }
							////////////////////////////////////////////////////////
							
							if( strtolower(file_extension(trim($a_data[3]))) == 'png' ){ $file = wpp_alpha( trim($a_data[3]) ); } else{ $file = trim($a_data[3]); }
							
							if( ($Y+$H) > 290 ) {
								$pdf->AddPage();
								$pdf->Image( $file, 10, 20, $W, $H, file_extension($file),trim($a_data[1]) );
								$pdf->Ln( $H + 5 );
							}
							else {
								$pdf->Image( $file, $X, $Y, $W, $H, file_extension($file),trim($a_data[1]));
								if( $H < 10 ){
									$pdf->SetLeftMargin( $W+$X );
								}
								else{
									$pdf->Ln( $H + 6 );
								}
							}
							wpp_clean($file); 
						}
					}
					else {
						$pdf->SetTextColor(0,0,200);
						$pdf->SetFont('','U');
						$pdf->Write(5, str_replace(']','',$a_data[2]), trim($a_data[1]) );
						$pdf->SetTextColor(50,50,50);
						$pdf->SetFont( $fontName, '', 10 );
					}
				}
			}
			break;
			###################################################
			case strpos( $row, 'img][')>0 : 
			{
				$img_data = explode( '][', $row );
				if( $img_data[1] ) {
				
					$X = trim(round($pdf->GetX(),0));
					$Y = trim(round($pdf->GetY(),0));
					
					////////////////////////////////////////////////////////
					$W = (trim(str_replace(']','',$img_data[4]))/5.2);
					$H = (trim($img_data[3])/5.2);
					if( $W && $H ){ 
						//size data exists
					} 
					else{
						$image = getimagesize( $img_data[1] );
						$W = trim($image[0])/5.2;
						$H = trim($image[1])/5.2;
					}
					if( !$W || !$H ) { $W = 40; $H = 40; }
					////////////////////////////////////////////////////////
					
					if( strtolower(file_extension(trim($img_data[1]))) == 'png' ){ $file = wpp_alpha( trim($img_data[1]) ); } else{ $file = trim($img_data[1]); }
					
					if( ($Y+$H) > 290 ) {
						$pdf->AddPage();
						$pdf->Image( $file, 10, 20, $W, $H, file_extension($file) );
						$pdf->Ln( $H + 6 );
					}
					else {
						$pdf->Image( $file, $X, $Y, $W, $H, file_extension($file) );
						if( $H < 10 ){
							$pdf->SetLeftMargin( $W+$X );
						}
						else{
							$pdf->Ln( $H + 6 );
						}
					}
					wpp_clean($file); 
				}
			}
			break;
			###################################################
			case strpos( $row, 'li][')>0 : 
			{
				$l_data = explode( '][', $row );
				if( $l_data[1] ) {
					$pdf->SetLeftMargin( 20 );
					$pdf->Write(5, str_replace(']','',"- ".$l_data[1]));
					$pdf->SetLeftMargin( 10 );
				}
			}
			break;
			###################################################
			case strpos( $row, 'code][')>0 : 
			{
				$c_data = explode( '][', $row );
				if( $c_data[1] ) {
					$pdf->SetFont('','',9);
					$pdf->SetTextColor(150,100,250);
					$pdf->Write(5, str_replace(']','',$c_data[1]));
					$pdf->SetTextColor(50,50,50);
					$pdf->SetFont( $fontName, '', 10 );
				}
			}
			break;
			###################################################
			case strpos( $row, 'font][')>0 : 
			{
				$f_data = explode( '][', $row );
				if( $f_data[1] ) {
					if( $f_data[2] ){
						$_col = explode(',', trim($f_data[2],']'));
						$pdf->SetTextColor($_col[0],$_col[1],$_col[2]);
					}
					else{
						$pdf->SetTextColor(50,50,50);
					}
					if( $f_data[3] ){
						$pdf->SetFont( $fontName, '', trim($f_data[3],']') );
					}
					else{
						$pdf->SetFont( $fontName, '', 10 );
					}
					$pdf->Write(4, str_replace(']','',$f_data[1]));
				}
			}
			break;
			###################################################
			case strpos( $row, 'b][')>0 : 
			{
				$b_data = explode( '][', $row );
				if( $b_data[1] ) {
					$pdf->SetFont('','B');
					$pdf->SetTextColor(50,50,50);
					$pdf->Write(5, str_replace(']','',$b_data[1]));
					$pdf->SetTextColor(50,50,50);
					$pdf->SetFont( $fontName, '', 10 );
				}
			}
			break;
			###################################################
			case strpos( $row, 'u][')>0 : 
			{
				$u_data = explode( '][', $row );
				if( $u_data[1] ) {
					$pdf->SetFont('','U');
					$pdf->SetTextColor(50,50,50);
					$pdf->Write(5, str_replace(']','',$u_data[1]));
					$pdf->SetTextColor(50,50,50);
					$pdf->SetFont( $fontName, '', 10 );
				}
			}
			break;
			###################################################
			case strpos( $row, 'i][')>0 : 
			{
				$i_data = explode( '][', $row );
				if( $i_data[1] ) {
					$pdf->SetFont('','I');
					$pdf->SetTextColor(50,50,50);
					$pdf->Write(5, str_replace(']','',$i_data[1]));
					$pdf->SetTextColor(50,50,50);
					$pdf->SetFont( $fontName, '', 10 );
				}
			}
			break;
			###################################################
			case strpos( $row, 'strong][')>0 : 
			{
				$b_data = explode( '][', $row );
				if( $b_data[1] ) {
					$pdf->SetFont('','B');
					$pdf->SetTextColor(50,50,50);
					$pdf->Write(5, str_replace(']','',$b_data[1]));
					$pdf->SetTextColor(50,50,50);
					$pdf->SetFont( $fontName, '', 10 );
				}
			}
			break;
			###################################################
			case strpos( $row, 'h1][')>0 : 
			{
				$h_data = explode( '][', $row );
				if( $h_data[1] ) {
					$pdf->SetFont('','',20);
					$pdf->Write(5, str_replace(']','',$h_data[1]));
					$pdf->SetTextColor(50,50,50);
					$pdf->SetFont( $fontName, '', 10 );
					$pdf->Ln( 10 );
				}
			}
			break;
			###################################################
			case strpos( $row, 'h2][')>0 : 
			{
				$h_data = explode( '][', $row );
				if( $h_data[1] ) {
					$pdf->SetFont('','',16);
					$pdf->Write(5, str_replace(']','',$h_data[1]));
					$pdf->SetTextColor(50,50,50);
					$pdf->SetFont( $fontName, '', 10 );
					$pdf->Ln( 5 );
				}
			}
			break;
			###################################################
			case strpos( $row, 'h3][')>0 : 
			{
				$h_data = explode( '][', $row );
				if( $h_data[1] ) {
					$pdf->SetFont('','',12);
					$pdf->Write(5, str_replace(']','',$h_data[1]));
					$pdf->SetTextColor(50,50,50);
					$pdf->SetFont( $fontName, '', 10 );
				}
			}
			break;
			###################################################
			case strpos( $row, 'h4][')>0 : 
			{
				$h_data = explode( '][', $row );
				if( $h_data[1] ) {
					$pdf->SetFont('','',10);
					$pdf->Write(5, str_replace(']','',$h_data[1]));
					$pdf->SetTextColor(50,50,50);
					$pdf->SetFont( $fontName, '', 10 );
				}
			}
			break;
			###################################################
			default : {
				
				$pdf->SetFont( $fontName, '', 10 );
				$pdf->Write(5,$row);
				$pdf->SetLeftMargin( 10 );
			}
		}
	}
	$i = $i+1;

}
$file = trim( str_replace( ' ', '-' , $html_title ) );
$pdf->Output($file.'.pdf','I');

?>