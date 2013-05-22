<?php
/*
Plugin Name: SPP inject Term
Description: Inject Term ke SPP buatan Mastah Masbuchin yg keren banget. SPP HARUS DI INSTALL DULU. 9/5/2012
Plugin URI: http://ninjaplugins.com/products/stupidpie
Author: CoLiq
Author URI: http://coliq.us
Version: 0.5

*/

/*
Release Notes:

	# 0.5
	* Enable term as RSS. live result: http://yourdomain.tld/feed/spp 
	* Auto term injector using RSS
	
	# 0.0.2
	* inject term

*/


add_action('admin_menu', 'liq_spp_inject',11);

function liq_spp_inject(){
add_menu_page('SPP Settings',__('SPP setting','spp'), 'administrator', __FILE__,'liq_spp_term_callback','');

#add_submenu_page(__FILE__, 'SPP rss', 'SPP rss', 'administrator', 'liq-spp-term','liq-spp-terms');

	//call register settings function
	add_action( 'admin_init', 'liq_spp_register_settings' );

}

function liq_spp_register_settings() {
	//register our settings
	register_setting( 'liq-spp-setting', 'liq_spp' );
}


function liq_spp_insertdb(){
global $wpdb;

	if(isset($_POST['liq_spp_term'])){
		$aterm = array();
		
		if(isset($_POST['spp_linebreak']))
			$aterm = explode("\n", $_POST['liq_spp_term']);
		else
			$aterm = explode(',', $_POST['liq_spp_term']);
		
		$minword = $_POST['spp_minword'];

		if(sizeof($aterm) > 0){
			$tot = 0;
			foreach($aterm as $term)
				if( liq_spp_save_term($term) ) $tot++;
			
		}


    echo '<div id="message" class="updated">
	<p style="text-align:center;"><strong>'.$tot .'</strong> of <strong>'. sizeof($aterm).'</strong> total terms inserted.</p>
    </div>';
				
	}
}

function liq_spp_term_callback(){


	if(isset($_POST['liq_spp_term'])){liq_spp_insertdb();}
?>

	<div class="wrap">
	<div id="icon-options-general" class="icon32">
	<br></div>
	<h2>Add Term to SPP</h2>
	<table class="widefat" id="icl_languages_selection_table">
				<thead>
					<tr>
						<th>Add Term to SPP</th>
					</tr>
				</thead>
	<tbody>
	<tr><td>	
	<form method="post" action="">


		<table class="form-table">
			<tbody>
			<tr valign="top">
			<td><input checked="checked" type="checkbox" id="" name="spp_linebreak" /> I m using line break
			</td>
			</tr>
			<tr valign="top">
			<td>
			<textarea id="" rows="10" cols="120" name="liq_spp_term"></textarea></td>
			</tr>
			<tr><td>
			Note: make sure each term separated by comma ",". example:<br>
			<span style="font-size:12px;font-family:arial;color:blue;">
			spp oke banget gitu loh, masbuchin gianteng, gerakan anti galau indonesia
			</span>
			</td>
			</tr>

			</tbody>
			 
		</table>
		
		<p class="submit">
		<input type="submit" class="button-primary" value="<?php _e('Insert') ?>" />
		</p>
	</form>

	</td></tr>
	</tbody>
	</table>


	<h2>SPP RSS</h2>
	<table class="widefat" id="icl_languages_selection_table">
				<thead>
					<tr>
						<th>Enable SPP RSS</th>
					</tr>
				</thead>
	<tbody>

	<form method="post" action="options.php"> 
	<?php
	settings_fields( 'liq-spp-setting' ); 
	$opt = get_option('liq_spp');
	?>

			<tr valign="top">
			<td><input type="checkbox" id="" name="liq_spp[liq_spp_rss]" value="1" <?php checked($opt['liq_spp_rss'],1); ?>/> Enable RSS. <?php if( $opt['liq_spp_rss'] == 1) echo 'Your RSS link: <strong><a target="_blank" href="'.get_bloginfo('home').'/feed/spp">'.get_bloginfo('home').'/feed/spp</a></strong>';?>
			</td>
			</tr>
			<tr valign="top">
			<td><input type="text" id="" name="liq_spp[liq_spp_rss_num]" value="<?php echo $opt['liq_spp_rss_num']; ?>" size="5"/> Term to show in RSS
			</td>
			</tr>
			<tr valign="top">
			<td>
				<input type="submit" class="button-primary" name="liq_spp_submit" value="<?php _e('Update') ?>" />	
			</td>
			</tr>
	</form>
			</tbody>			 
		</table>



	<h2>RSS to Term</h2>
	<table class="widefat" id="icl_languages_selection_table">
				<thead>
					<tr>
						<th>Auto Term Injector</th>
					</tr>
					<tr>
						<td>
						<p>	
							Use in TEXT widget as shortcode <strong>[spp_feedkw x=10 url=http://mytargetdomain.tld/feed]</strong><br />
							s = 1 or 0, save term or not. default 1<br />
							x = number of term to show<br />
							url = feed URL</p>							
						</td>
					<tr>
				</thead>
	<tbody>

	</div>

<?php
}

#########################################
# SPP Feed
#########################################
function liq_get_spp_post($howmany = 10){
	global $wpdb;

	$howmany = (int) $howmany;
	
	$sql = "SELECT `term` FROM `".$wpdb->prefix."spp` ORDER BY RAND() LIMIT ".$howmany.";";
	$oterm = $wpdb->get_results($sql);

	return $oterm;	
}

function liq_spp_feed(){
	$opt = get_option('liq_spp');
	$oterm = liq_get_spp_post($opt['liq_spp_rss_num']);

	header("Content-Type: application/rss+xml; charset=UTF-8");
	echo '<?xml version="1.0"?>';
	?>
	<rss version="2.0"
		xmlns:content="http://purl.org/rss/1.0/modules/content/"
		xmlns:dc="http://purl.org/dc/elements/1.1/"
		xmlns:atom="http://www.w3.org/2005/Atom"
		xmlns:sy="http://purl.org/rss/1.0/modules/syndication/">
		
		<channel>
			<title><?php bloginfo_rss('name');?></title>
			<link><?php bloginfo_rss('url') ?></link>
			<atom:link href="<?php self_link(); ?>" rel="self" type="application/rss+xml" />
			<description><?php bloginfo_rss("description") ?></description>
			<language><?php echo get_option('rss_language'); ?></language>
			<sy:updatePeriod><?php echo apply_filters( 'rss_update_period', 'hourly' ); ?></sy:updatePeriod>
			<sy:updateFrequency><?php echo apply_filters( 'rss_update_frequency', '1' ); ?></sy:updateFrequency>

	<?php $date = mktime(); ?>
	<?php foreach ($oterm as $post) { ?>
	<?php 		
		$permalink = build_permalink_for($post->term,0);
		$date = strtotime('-11 hours',$date);
	?>
	<item>
		<title><?php echo $post->term; ?></title>
		<link><?php echo $permalink; ?></link>
		<description><?php echo '<![CDATA['.$post->term.'.. <a href="'.$permalink.'">Continue reading</a>'.']]>';  ?></description>
		<pubDate><?php echo mysql2date('D, d M Y H:i:s +0000',  date('d-m-Y H:i:s',$date) );?></pubDate>
	</item>
	<?php } ?>
	</channel>
	</rss>

<?php
}


function liq_spp_add_feed() {
	global $wp_rewrite;	
	add_feed('spp', 'liq_spp_feed');
	add_action('generate_rewrite_rules', 'liq_spp_rewrite_rule');
	$wp_rewrite->flush_rules();

}

function liq_spp_rewrite_rule( $wp_rewrite ){
  $new_rules = array(
    'feed/(.+)' => 'index.php?feed='.$wp_rewrite->preg_index(1)
  );
  $wp_rewrite->rules = $new_rules + $wp_rewrite->rules;
}

$opt = get_option('liq_spp');
if( $opt['liq_spp_rss'] == 1 )
	add_action('init', 'liq_spp_add_feed');



#########################################
# SPP Auto Term Injector
#########################################
	function liq_spp_feedkw( $atts ) {
		extract( shortcode_atts( array(
			's' => 1,
			'x' => 10,
			'url'	=> ''
		), $atts ) );
		
		$xml = liq_spp_fetch($url);
		
		if( $xml->channel->item ){

			$result = '<ul class="spp_feeder">'; 
				$maxs = 1;
				foreach ($xml->channel->item as $item) {
					
					if( $s==1 ) liq_spp_save_term(liq_spp_clean_term($item->title));

					$result .= '<li><a href="'.build_permalink_for(sanitize_title($item->title),0).'">'.$item->title.'</a></li>';
					
					if($maxs == $x) break;

					$maxs++;
				}
			$result .= '</ul>';

		} else $result = 'No feed to display';

					
		return $result;
	}

   function liq_spp_save_term($term){
	   global $wpdb;
		if( !empty($term) && spp_filter_before_save($term) ){
			if($wpdb->query( $wpdb->prepare( "INSERT IGNORE INTO ".$wpdb->prefix."spp (`term` ) VALUES ( %s )", trim($term) )) ) return true; else return false;
		}
   
   }

	function liq_spp_clean_term($term){
		$term		 = sanitize_title($term);
		$term		 = preg_replace('/[^%a-z0-9 _-]/', '', $term);
		$term		 = preg_replace('|-|', ' ', $term);
		return $term;
	}

   function liq_spp_fetch($url) {
        $feed = file_get_contents($url);
		$xml = new SimpleXmlElement($feed);
        return $xml;
   }
    
add_shortcode( 'spp_feedkw', 'liq_spp_feedkw' );

?>