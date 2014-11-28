<?php 
error_reporting(0);
if( !get_option('wpp_save_pdf_img_show') ){ $STR = preg_replace( '|<img\s+[^>]*>|i', '', $STR ); }
if( get_option('wpp_save_pdf_img_max_width') ){ $wpp_max_image_width = get_option('wpp_save_pdf_img_max_width'); } else{ $wpp_max_image_width = 500; }
if( get_option('wpp_save_pdf_rus') ){ $fontName = 'timesnewromanpsmt'; if( function_exists('mb_convert_encoding') ){$STR = mb_convert_encoding($STR, "windows-1251","UTF-8");}}
else{ $fontName = 'Arial'; if( function_exists('mb_convert_encoding') ){$STR = mb_convert_encoding($STR, "ISO-8859-1","UTF-8");} }
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

require('pdf/fpdf.php');

class PDF extends FPDF
{

 function Image($file, $x, $y, $w=0, $h=0, $type='', $link='', $isMask=false, $maskImg=0)
{
	//Put an image on the page
	if(!isset($this->images[$file]))
	{
		//First use of image, get info
		if($type=='')
		{
			$pos=strrpos($file,'.');
			if(!$pos)
				$this->Error('Image file has no extension and no type was specified: '.$file);
			$type=substr($file,$pos+1);
		}
		$type=strtolower($type);
		$mqr=get_magic_quotes_runtime();
		set_magic_quotes_runtime(0);
		if($type=='jpg' || $type=='jpeg')
			$info=$this->_parsejpg($file);
		elseif($type=='png'){
			$info=$this->_parsepng($file);
			if ($info=='alpha') return $this->ImagePngWithAlpha($file,$x,$y,$w,$h,$link);
		}
		else
		{
			//Allow for additional formats
			$mtd='_parse'.$type;
			if(!method_exists($this,$mtd))
				$this->Error('Unsupported image type: '.$type);
			$info=$this->$mtd($file);
		}
		set_magic_quotes_runtime($mqr);

		if ($isMask){
      $info['cs']="DeviceGray"; // try to force grayscale (instead of indexed)
    }
		$info['i']=count($this->images)+1;
		if ($maskImg>0) $info['masked'] = $maskImg;###
		$this->images[$file]=$info;
	}
	else
		$info=$this->images[$file];
	//Automatic width and height calculation if needed
	if($w==0 && $h==0)
	{
		//Put image at 72 dpi
		$w=$info['w']/$this->k;
		$h=$info['h']/$this->k;
	}
	if($w==0) $w=$h*$info['w']/$info['h'];
	if($h==0) $h=$w*$info['h']/$info['w'];

	if ($isMask) $x = ($this->CurOrientation=='P'?$this->CurPageFormat[0]:$this->CurPageFormat[1]) + 10; // embed hidden, ouside the canvas
	$this->_out(sprintf('q %.2f 0 0 %.2f %.2f %.2f cm /I%d Do Q',$w*$this->k,$h*$this->k,$x*$this->k,($this->h-($y+$h))*$this->k,$info['i']));
	if($link) $this->Link($x,$y,$w,$h,$link);

	return $info['i'];
}

// needs GD 2.x extension
// pixel-wise operation, not very fast
function ImagePngWithAlpha($file,$x,$y,$w=0,$h=0,$link='')
{
	$tmp_alpha = tempnam('/tmp/', 'mska');
	$this->tmpFiles[] = $tmp_alpha;
	$tmp_plain = tempnam('/tmp/', 'mskp');
	$this->tmpFiles[] = $tmp_plain;

	list($wpx, $hpx) = getimagesize($file);
	$img = imagecreatefrompng($file);
	$alpha_img = imagecreate( $wpx, $hpx );

	// generate gray scale pallete
	for($c=0;$c<256;$c++) ImageColorAllocate($alpha_img, $c, $c, $c);

	// extract alpha channel
	$xpx=0;
	while ($xpx<$wpx){
		$ypx = 0;
		while ($ypx<$hpx){
			$color_index = imagecolorat($img, $xpx, $ypx);
			$col = imagecolorsforindex($img, $color_index);
			imagesetpixel($alpha_img, $xpx, $ypx, $this->_gamma( (127-$col['alpha'])*255/127)  );
	    ++$ypx;
		}
		++$xpx;
	}

	imagepng($alpha_img, $tmp_alpha);
	imagedestroy($alpha_img);

	// extract image without alpha channel
	$plain_img = imagecreatetruecolor ( $wpx, $hpx );
	imagecopy ($plain_img, $img, 0, 0, 0, 0, $wpx, $hpx );
	imagepng($plain_img, $tmp_plain);
	imagedestroy($plain_img);

	//first embed mask image (w, h, x, will be ignored)
	$maskImg = $this->Image($tmp_alpha, 0,0,0,0, 'PNG', '', true);

	//embed image, masked with previously embedded mask
	$this->Image($tmp_plain,$x,$y,$w,$h,'PNG',$link, false, $maskImg);
}

function Close()
{
	parent::Close();
	// clean up tmp files
	if( !empty($this->tmpFiles) ){ foreach($this->tmpFiles as $tmp) @unlink($tmp);}
}

function _putimages()
{
	$filter=($this->compress) ? '/Filter /FlateDecode ' : '';
	reset($this->images);
	while(list($file,$info)=each($this->images))
	{
		$this->_newobj();
		$this->images[$file]['n']=$this->n;
		$this->_out('<</Type /XObject');
		$this->_out('/Subtype /Image');
		$this->_out('/Width '.$info['w']);
		$this->_out('/Height '.$info['h']);

		if (isset($info["masked"])) $this->_out('/SMask '.($this->n-1).' 0 R'); ###

		if($info['cs']=='Indexed')
			$this->_out('/ColorSpace [/Indexed /DeviceRGB '.(strlen($info['pal'])/3-1).' '.($this->n+1).' 0 R]');
		else
		{
			$this->_out('/ColorSpace /'.$info['cs']);
			if($info['cs']=='DeviceCMYK')
				$this->_out('/Decode [1 0 1 0 1 0 1 0]');
		}
		$this->_out('/BitsPerComponent '.$info['bpc']);
		if(isset($info['f']))
			$this->_out('/Filter /'.$info['f']);
		if(isset($info['parms']))
			$this->_out($info['parms']);
		if(isset($info['trns']) && is_array($info['trns']))
		{
			$trns='';
			for($i=0;$i<count($info['trns']);$i++)
				$trns.=$info['trns'][$i].' '.$info['trns'][$i].' ';
			$this->_out('/Mask ['.$trns.']');
		}
		$this->_out('/Length '.strlen($info['data']).'>>');
		$this->_putstream($info['data']);
		unset($this->images[$file]['data']);
		$this->_out('endobj');
		//Palette
		if($info['cs']=='Indexed')
		{
			$this->_newobj();
			$pal=($this->compress) ? gzcompress($info['pal']) : $info['pal'];
			$this->_out('<<'.$filter.'/Length '.strlen($pal).'>>');
			$this->_putstream($pal);
			$this->_out('endobj');
		}
	}
}

// GD seems to use a different gamma, this method is used to correct it again
function _gamma($v){
	return pow ($v/255, 2.2) * 255;
}

// this method overwriing the original version is only needed to make the Image method support PNGs with alpha channels.
// if you only use the ImagePngWithAlpha method for such PNGs, you can remove it from this script.
function _parsepng($file)
{
	//Extract info from a PNG file
	$f=fopen($file,'rb');
	if(!$f)
		$this->Error('Can\'t open image file: '.$file);
	//Check signature
	if(fread($f,8)!=chr(137).'PNG'.chr(13).chr(10).chr(26).chr(10))
		$this->Error('Not a PNG file: '.$file);
	//Read header chunk
	fread($f,4);
	if(fread($f,4)!='IHDR')
		$this->Error('Incorrect PNG file: '.$file);
	$w=$this->_readint($f);
	$h=$this->_readint($f);
	$bpc=ord(fread($f,1));
	if($bpc>8)
		$this->Error('16-bit depth not supported: '.$file);
	$ct=ord(fread($f,1));
	if($ct==0)
		$colspace='DeviceGray';
	elseif($ct==2)
		$colspace='DeviceRGB';
	elseif($ct==3)
		$colspace='Indexed';
	else {
		fclose($f);      // the only changes are
		return 'alpha';  // made in those 2 lines
	}
	if(ord(fread($f,1))!=0)
		$this->Error('Unknown compression method: '.$file);
	if(ord(fread($f,1))!=0)
		$this->Error('Unknown filter method: '.$file);
	if(ord(fread($f,1))!=0)
		$this->Error('Interlacing not supported: '.$file);
	fread($f,4);
	$parms='/DecodeParms <</Predictor 15 /Colors '.($ct==2 ? 3 : 1).' /BitsPerComponent '.$bpc.' /Columns '.$w.'>>';
	//Scan chunks looking for palette, transparency and image data
	$pal='';
	$trns='';
	$data='';
	do
	{
		$n=$this->_readint($f);
		$type=fread($f,4);
		if($type=='PLTE')
		{
			//Read palette
			$pal=fread($f,$n);
			fread($f,4);
		}
		elseif($type=='tRNS')
		{
			//Read transparency info
			$t=fread($f,$n);
			if($ct==0)
				$trns=array(ord(substr($t,1,1)));
			elseif($ct==2)
				$trns=array(ord(substr($t,1,1)),ord(substr($t,3,1)),ord(substr($t,5,1)));
			else
			{
				$pos=strpos($t,chr(0));
				if($pos!==false)
					$trns=array($pos);
			}
			fread($f,4);
		}
		elseif($type=='IDAT')
		{
			//Read image data block
			$data.=fread($f,$n);
			fread($f,4);
		}
		elseif($type=='IEND')
			break;
		else{ ( $n+4 < 1 ) ? $g = 1 : $g=$n+4;
				//ini_set("memory_limit","1200M");
				fread($f,$g);
			}
	}
	while($n);
	if($colspace=='Indexed' && empty($pal))
		$this->Error('Missing palette in '.$file);
	fclose($f);
	return array('w'=>$w,'h'=>$h,'cs'=>$colspace,'bpc'=>$bpc,'f'=>'FlateDecode','parms'=>$parms,'pal'=>$pal,'trns'=>$trns,'data'=>$data);
	}


	function Header()
	{
		global $wpp_html_link;
		$header_text_1 = 'Product data were exported from - ';
		$header_text_2 = get_option('blogname');
		$header_text_3 = ' ';
		$header_text_4 = 'Export date: '.date("D M j G:i:s Y / O ").' GMT';
		
		if( get_option('wpp_save_pdf_rus') ){
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
		$footer_text_1 = '        Output as PDF file has been powered by [';
		$footer_text_2 = ' WooCommerce PDF & Print ' ;
		$footer_text_3 = '] plugin by www.gVectors.com';
		$this->SetY(-10);
		//$this->Image(WPP_PATH.'img/mini.png',($this->GetX()+1.5),($this->GetY()+1.4),4,3,'gif','http://profprojects.com');
		$this->SetFont('Times','I',8);
		$this->SetTextColor(150,150,150);
		$this->Write(6, $footer_text_1 );
		$this->SetTextColor(150,150,255);
		$this->Write(6, $footer_text_2, 'http://www.gvectors.com/?page=wpp' );
		$this->SetTextColor(150,150,150);
		$this->Write(6, $footer_text_3 );
		$this->SetTextColor(50,50,50);
		$this->Cell(0,6,'|  Page '.$this->PageNo().'/{nb}  |',0,0,'R');
	}
}


//include( 'array.php' );
//$fontName = 'TimesNewRomanPSMT';
//$fontName = 'ArialArmenianMT';
//$fontName = 'ArTarumianHandes';
//$fontName = 'Sylfaen';
//$fontName = 'ArmTimesItalic';
//$fontName = 'Sylfaen';
//$fontName = 'flysung';


$pdf=new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();

if( get_option('wpp_save_pdf_rus') ){
	$pdf->AddFont('TimesNewRomanPSMT','','rtimes.php');
}

$pdf->SetFont( $fontName, '', 10 );
$pdf->SetTextColor(50,50,50);


###################################################################################################################
//$pdf->AddFont('flysung','','fireflysung.php');
//$pdf->SetFont('flysung','',10); 
###################################################################################################################
//$pdf->AddFont('TimesNewRomanPSMT','','rtimes.php');
//$pdf->SetFont('TimesNewRomanPSMT','',10); 
###################################################################################################################
//$pdf->AddFont('ArialArmenianMT','','armarial.php');
//$pdf->SetFont('ArialArmenianMT','',10); 
###################################################################################################################
//$pdf->AddFont('ArTarumianHandes','','handes.php');
//$pdf->SetFont('ArTarumianHandes','',10); 
###################################################################################################################
//$pdf->AddFont('Sylfaen','','sylfaen.php');
//$pdf->SetFont('Sylfaen','',10); 
###################################################################################################################
//$pdf->AddFont('ArmTimesItalic','','armtimes.php');
//$pdf->SetFont('ArmTimesItalic','',10); 
###################################################################################################################
//$pdf->AddFont('ArmTimes','','armtimes.php');
//$pdf->SetFont('ArmTimes','',10); 
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
							
							
							if( ($Y+$H) > 290 ) {
								$pdf->AddPage();
								$pdf->Image( trim($a_data[3]), 10, 20, $W, $H, file_extension(trim($a_data[3])),trim($a_data[1]) );
								$pdf->Ln( $H + 5 );
							}
							else {
								$pdf->Image( trim($a_data[3]), $X, $Y, $W, $H, file_extension(trim($a_data[3])),trim($a_data[1]));
								if( $H < 10 ){
									$pdf->SetLeftMargin( $W+$X );
								}
								else{
									$pdf->Ln( $H + 6 );
								}
							}
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
					
					if( ($Y+$H) > 290 ) {
						$pdf->AddPage();
						$pdf->Image( trim($img_data[1]), 10, 20, $W, $H, file_extension(trim($img_data[1])) );
						$pdf->Ln( $H + 6 );
					}
					else {
						$pdf->Image( trim($img_data[1]), $X, $Y, $W, $H, file_extension(trim($img_data[1])) );
						if( $H < 10 ){
							$pdf->SetLeftMargin( $W+$X );
						}
						else{
							$pdf->Ln( $H + 6 );
						}
					}
					
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