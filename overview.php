<?php

	global $wpdb;
	$wpp_options = array(	'wpp_onoff_saving_manager',
							'wpp_onoff_saving_doc',
							'wpp_onoff_saving_pdf',
							'wpp_saving_position', 
							'wpp_saving_align',
							'wpp_saving_location_postend',
							'wpp_saving_location_custom',
							'wpp_saving_type',
							'wpp_onoff_save_follow',
							'wpp_save_text_align',
							'wpp_onoff_print_manager', 
							'wpp_print_location_postend', 
							'wpp_print_location_custom',
							'wpp_print_type',
							'wpp_print_app',
							'wpp_global_jquery_load'
							);
	
	if( $_POST[ 'wpp_hidden' ] == 'x' ) {
		foreach( $wpp_options as $wpp ) {
			( $_POST[ $wpp ] == '' ) ? $wpp_op = 0 : $wpp_op = $_POST[ $wpp ];
			update_option( $wpp, $wpp_op );
		}
		?> <div class="updated"><p><strong><?php _e( 'Options saved.' ); ?></strong></p></div> <?php
	}
	
	foreach( $wpp_options as $wpp ) {
		if( get_option($wpp) ) {
			$wpp_checked['checkbox'][ $wpp ][ 'checked' ] = 'checked="checked"';
			$wpp_checked['radio'][ $wpp ][ 'on_check' ] = 'checked="checked"';
			$wpp_checked['radio'][ $wpp ][ 'off_check' ] = '';
		} 
		else {
			$wpp_checked['checkbox'][ $wpp ][ 'checked' ] = '';
			$wpp_checked['radio'][ $wpp ][ 'on_check' ] = '';
			$wpp_checked['radio'][ $wpp ][ 'off_check' ] = 'checked="checked"';
		}
	}

?>
<br />
<style type="text/css">
.wpp_option_table {
background-color:#CCCCCC;
}
.wpp_option_th {
background-color:#F9F9F9;
text-align:left;
font-weight:100;
padding:2px;
width:60%;
}
.wpp_option_td {
background-color:#F9F9F9;
text-align:left;
font-weight:100;
padding:2px;
width:40%;
}
.wpp_option_top_th {
background-color:#F0F0F0;
text-align:left;
font-weight:bold;
padding:2px;
width:60%;
}
.wpp_option_top_td {
background-color:#F0F0F0;
text-align:left;
font-weight:bold;
padding:2px;
width:40%;
}
ul li{
padding-left:0px;
font-size:13px;
}
.wpp_yes{
	list-style-image:url(<?php echo WPP_PATH ?>img/1.gif);
}
.wpp_no{
	list-style-image:url(<?php echo WPP_PATH ?>img/0.gif);
}
</style>
    <link rel="stylesheet" href="<?php echo WPP_PATH ?>bxslider/jquery.bxslider.css" type="text/css" />
    <script src="<?php echo WPP_PATH ?>bxslider/jquery.min.js"></script>
    <script src="<?php echo WPP_PATH ?>bxslider/jquery.bxslider.js"></script>
 				

	<form name="form_options" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
		<input type="hidden" name="wpp_hidden" value="x">
        <table width="100%" border="0">
          <tr>
            <td style="padding:10px; padding-left:0px; vertical-align:top; width:500px;">
                    <div class="slider">
                        <ul class="bxslider">
                          <li><a href="https://wordpress.org/plugins/woodiscuz-woocommerce-comments/screenshots/"><img src="<?php echo WPP_PATH ?>img/gc/3.png" title="Free Download from Wordpress.org" style="padding:0px 0px 20px 20px;" /></a></li>
                          <li><a href="https://wordpress.org/plugins/advanced-content-pagination/screenshots/"><img src="<?php echo WPP_PATH ?>img/gc/1.png" title="Free Download from Wordpress.org" style="padding:0px 0px 20px 20px;" /></a></li>
                          <li><a href="https://wordpress.org/plugins/author-and-post-statistic-widgets/"><img src="<?php echo WPP_PATH ?>img/gc/2.png" title="Free Download from Wordpress.org" style="padding:0px 0px 20px 20px;" /></a></li>
                          
                        </ul>
                    </div>
                    <div style="clear:both"></div>
            </td>
            <td valign="top" style="padding:10px; padding-right:0px;">
            
            <table width="100%" border="0" cellspacing="1" class="widefat">
                <thead>
                <tr>
                <th>&nbsp;Information</th>
                </tr>
                </thead>
                <tbody>
                    <tr valign="top">
                        <td style="background:#FFF; text-align:left; font-size:12px;">&nbsp;
                        
                        </td>
                    </tr>
                </tbody>
              </table>
			  <br />
              <table width="100%" border="0" cellspacing="1" class="widefat">
                    <thead>
                    <tr>
                    <th>&nbsp;Like WooCommerce PDF &amp; Print plugin?</th>
                    </tr>
                    </thead>
                    <tbody>
                        <tr valign="top">
                            <td style="background:#FFF; text-align:left; font-size:12px;">
                            <ul>
                            <li>If you like WooPP and want to encourage us to develop and maintain it,why not do any or all of the following:</li>
                            <li>- Link to it so other folks can find out about it.</li>
                            <li>- Give it a good rating on <a href="http://wordpress.org/extend/plugins/universal-post-manager/" target="_blank">WordPress.org.</a></li>
                            <li>- We spend as much of my spare time as possible working on WooCommerce PDF &amp; Print and any donation is appreciated. Donations play a crucial role in supporting Free and Open Source Software projects. <div style="width:200px; float:right;">
                            <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="VNMGTCF9NRH5C">
<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
<img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
</form>

                            </ul>
                            </td>
                        </tr>
                    </tbody>
                 </table>
                
            </td>
          </tr>
        </table>

		<br />
		<table width="100%" border="0" cellspacing="1" class="widefat">
        <thead>
			<tr valign="top">
				<th>
				<strong><?php _e( 'Turn On/Off Saving Manager' ) ?></strong>
				</th>
				<th style="padding-left:0px;">
				<input type="radio" id="wpp_onoff_saving_manager_1" name="wpp_onoff_saving_manager" value="1" 
				<?php echo $wpp_checked['radio'][ 'wpp_onoff_saving_manager' ]['on_check'] ?>/> 
				<label for="wpp_onoff_saving_manager_1"><?php _e( 'On' ) ?></label>&nbsp;
				<input type="radio" id="wpp_onoff_saving_manager_0" name="wpp_onoff_saving_manager" value="0" 
				<?php echo $wpp_checked['radio'][ 'wpp_onoff_saving_manager' ]['off_check'] ?> />
				<label for="wpp_onoff_saving_manager_0"><?php _e( 'Off' ) ?></label>
				</th>
			</tr>
        </thead>
			<tr valign="top">
				<th class="wpp_option_th">
				<?php _e( 'Turn on/off Saving as Word Document' ) ?>
				</th>
				<td class="wpp_option_td">
				<input type="radio" id="wpp_onoff_saving_doc_1" name="wpp_onoff_saving_doc" value="1" 
				<?php echo $wpp_checked['radio'][ 'wpp_onoff_saving_doc' ][ 'on_check' ] ?>/> 
				<label for="wpp_onoff_saving_doc_1"><?php _e( 'On' ) ?></label> &nbsp;&nbsp; 
				<input type="radio" id="wpp_onoff_saving_doc_0" name="wpp_onoff_saving_doc" value="0" 
				<?php echo $wpp_checked['radio'][ 'wpp_onoff_saving_doc' ][ 'off_check' ] ?> />
				<label for="wpp_onoff_saving_doc_0"><?php _e( 'Off' ) ?></label>
				</td>
			</tr>
			<tr valign="top">
				<th class="wpp_option_th">
				<?php _e( 'Turn on/off Saving as PDF' ) ?>
				</th>
				<td class="wpp_option_td">
				<input type="radio" id="wpp_onoff_saving_pdf_1" name="wpp_onoff_saving_pdf" value="1" 
				<?php echo $wpp_checked['radio'][ 'wpp_onoff_saving_pdf' ][ 'on_check' ] ?>/> 
				<label for="wpp_onoff_saving_pdf_1"><?php _e( 'On' ) ?></label> &nbsp;&nbsp; 
				<input type="radio" id="wpp_onoff_saving_pdf_0" name="wpp_onoff_saving_pdf" value="0" 
				<?php echo $wpp_checked['radio'][ 'wpp_onoff_saving_pdf' ][ 'off_check' ] ?> />
				<label for="wpp_onoff_saving_pdf_0"><?php _e( 'Off' ) ?></label>
				</td>
			</tr>
            <tr valign="top">
				<th class="wpp_option_th">
				<?php _e( 'Allow search engines to index saving documents' ) ?>
				</th>
				<td class="wpp_option_td">
				<input type="radio" id="wpp_onoff_save_follow_1" name="wpp_onoff_save_follow" value="1" 
				<?php echo $wpp_checked['radio'][ 'wpp_onoff_save_follow' ]['on_check'] ?>/> 
				<label for="wpp_onoff_save_follow_1"><?php _e( 'Allow' ) ?></label> &nbsp;&nbsp; 
				<input type="radio" id="wpp_onoff_save_follow_0" name="wpp_onoff_save_follow" value="0" 
				<?php echo $wpp_checked['radio'][ 'wpp_onoff_save_follow' ]['off_check'] ?> />
				<label for="wpp_onoff_save_follow_0"><?php _e( 'Disallow' ) ?></label>
				</td>
			</tr>
            <tr valign="top">
				<th class="wpp_option_th">
				<?php _e( 'Alignment of saving and printing documents' ) ?>
				</th>
				<td class="wpp_option_td">
                <?php 
				$wpp_save_text_align = array( 'left'=>'', 'right'=>'', 'center'=>'', 'justify'=>'');
				switch( get_option( 'wpp_save_text_align' ) ){
					case 'left' : $wpp_save_text_align['left'] = 'selected="selected"' ;break;
					case 'right' : $wpp_save_text_align['right'] = 'selected="selected"' ;break;
					case 'center' : $wpp_save_text_align['center'] = 'selected="selected"' ;break;
					case 'justify' : $wpp_save_text_align['justify'] = 'selected="selected"' ;break;
				}
				?>
				<select name="wpp_save_text_align">
                    <option value="left" <?php echo $wpp_save_text_align['left'] ?>>left &nbsp;</option>
                    <option value="right" <?php echo $wpp_save_text_align['right'] ?>>right &nbsp;</option>
                    <option value="center" <?php echo $wpp_save_text_align['center'] ?>>center &nbsp;</option>
                    <option value="justify" <?php echo $wpp_save_text_align['justify'] ?>>justify &nbsp;</option>
                </select>
				</td>
			</tr>
			
			<tr valign="top">
				<th class="wpp_option_th">
				<?php _e( 'Appearance type of saving buttons and strings' ) ?>
				</th>
				<td class="wpp_option_td">
				<input type="radio" id="wpp_saving_type_0" name="wpp_saving_type" value="0" 
				<?php echo $wpp_checked['radio'][ 'wpp_saving_type' ][ 'off_check' ] ?>/> 
				<label for="wpp_saving_type_0"><?php _e( 'String' ) ?></label> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<input type="radio" id="wpp_saving_type_1" name="wpp_saving_type" value="1" 
				<?php echo $wpp_checked['radio'][ 'wpp_saving_type' ][ 'on_check' ] ?> /> 
				<label for="wpp_saving_type_1"><?php _e( 'Button' ) ?></label> 
				</td>
			</tr>
            <!--
			<tr valign="top">
				<th class="wpp_option_th">
				<?php _e( 'Location of saving buttons and strings' ) ?>
				</th>
				<td class="wpp_option_td">
				<input id="wpp_saving_location_custom" type="checkbox" name="wpp_saving_location_custom" value="1" 
				<?php echo $wpp_checked['checkbox'][ 'wpp_saving_location_custom' ][ 'checked' ] ?> /> 
				<label for="wpp_saving_location_custom">
				<?php _e( 'Custom Location. Put this code in template files wherever you want<br>' ) ?> 
				</label>
				<code style="font-size:15px; font-weight:bold">&nbsp; &lt;?php wpp_save() ?&gt; </code><br />
				</td>
			</tr>
            -->
		</table>
		<br />
		<table width="100%" border="0" cellspacing="1" class="widefat">
        <thead>
			<tr valign="top">
				<th>
				<strong><?php _e( 'Turn On/Off Print Manager' ) ?></strong>
				</th>
				<th style="padding-left:0px;">
				<input type="radio" id="wpp_onoff_print_manager_1" name="wpp_onoff_print_manager" value="1" 
				<?php echo $wpp_checked['radio'][ 'wpp_onoff_print_manager' ]['on_check'] ?>/> 
				<label for="wpp_onoff_print_manager_1"><?php _e( 'On' ) ?></label>&nbsp;
				<input type="radio" id="wpp_onoff_print_manager_0" name="wpp_onoff_print_manager" value="0" 
				<?php echo $wpp_checked['radio'][ 'wpp_onoff_print_manager' ]['off_check'] ?> />
				<label for="wpp_onoff_print_manager_0"><?php _e( 'Off' ) ?></label>
				</th>
			</tr>
        </thead>
			<tr valign="top">
				<th class="wpp_option_th">
				<?php _e( 'Appearance mode of print button and string' ) ?>
				</th>
				<td class="wpp_option_td">
				<input type="radio" id="wpp_print_app_0" name="wpp_print_app" value="0" 
				<?php echo $wpp_checked['radio'][ 'wpp_print_app' ][ 'off_check' ] ?>/> 
				<label for="wpp_print_app_0"><?php _e( 'Single' ) ?></label>&nbsp;
				
				<input type="radio" id="wpp_print_app_1" name="wpp_print_app" value="1" 
				<?php echo $wpp_checked['radio'][ 'wpp_print_app' ][ 'on_check' ] ?> /> 
				<label for="wpp_print_app_1"><?php _e( 'With saving buttons or strings' ) ?></label> <br />
				</td>
			</tr>
			<tr valign="top">
				<th class="wpp_option_th">
				<?php _e( 'Appearance type of print button and string' ) ?>
				</th>
				<td class="wpp_option_td">
				<input type="radio" id="wpp_print_type_0" name="wpp_print_type" value="0" 
				<?php echo $wpp_checked['radio'][ 'wpp_print_type' ][ 'off_check' ] ?>/> 
				<label for="wpp_print_type_0"><?php _e( 'String' ) ?></label> &nbsp;
				<input type="radio" id="wpp_print_type_1" name="wpp_print_type" value="1" 
				<?php echo $wpp_checked['radio'][ 'wpp_print_type' ][ 'on_check' ] ?> /> 
				<label for="wpp_print_type_1"><?php _e( 'Button' ) ?></label> <br />
				</td>
			</tr>
            <!--
			<tr valign="top">
				<th class="wpp_option_th">
				<?php _e( 'Location of print button and string' ) ?><br />
				<span style="color:#777777; font-style:italic; font-weight:100; font-size:12px;">
				(<?php _e( 'Only for Single mode of Appearance' ) ?>)</span>
				</th>
				<td class="wpp_option_td">
				<input id="wpp_print_location_custom" type="checkbox" name="wpp_print_location_custom" value="1" 
				<?php echo $wpp_checked['checkbox'][ 'wpp_print_location_custom' ][ 'checked' ] ?> /> 
				<label for="wpp_print_location_custom">
				<?php _e( 'Custom Location. Put this code in template files wherever you want<br>' ) ?> 
				</label>
				<code style="font-size:15px; font-weight:bold">&nbsp; &lt;?php wpp_print() ?&gt; </code><br />
				</td>
			</tr>
            -->
		</table>
			<p class="submit" align="right">
			<input type="submit" class="button button-primary" name="Submit" value="<?php _e( 'Update Options' ) ?>" />
			&nbsp;&nbsp;&nbsp;&nbsp;
			</p>
		</form>
		</div>
        <script>
$('.bxslider').bxSlider({
  mode: 'fade',
  captions: false,
  auto: true
});
</script>