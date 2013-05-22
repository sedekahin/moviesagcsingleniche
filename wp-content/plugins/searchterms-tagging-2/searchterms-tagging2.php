<?php
/*
Plugin Name: SEO SearchTerms Tagging 2
Plugin URI: http://exclusivewp.com/searchterms-tagging-2-plugin/
Description:  Multiply blog traffic by strengthening on page SEO, increasing the number of indexed pages, auto convert search terms into post tags, and more.
Version: 1.535
Author: Purwedi Kurniawan
Author URI: http://exclusivewp.com
*/
/**
* update: july 30, 2011
* :: security bug fix
* last update: december 29, 2010
* :: auto correct bad words list not in the right format
* :: upgrade db structure for better performance
* :: using wordpress cache to cache db query
* update: december 23, 2010
* :: option to upgrade database to support international characters (default for new installation)
* :: change search permalink (using - instead of +) 
* :: promote post with no traffic after 30 days as most recent post (max 2 post daily)
* :: auto clean up database now is mandatory
* :: record all search terms, if ID = 0 -> homepage
* :: display home keywords in admin page
**/
/**
* default values for the plugin settings
**/
define('PK_MAX_SEARCH_TERMS','10');
define('PK_AUTO_ADD','1');
define('PK_AUTO_LINK','0');
define('PK_SHOW_COUNT','0');
define('PK_BEFORE_KEYWORD','<li>');
define('PK_AFTER_KEYWORD','</li>');
define('PK_BEFORE_LIST','<ul>');
define('PK_AFTER_LIST','</ul>');
define('PK_LIST_HEADER','<h4>Incoming search terms:</h4>');
define('PK_AUTO_CLEANUP','90');
define('PK_AUTO_TAG','0');
define('PK_PROMOTE_OLD_POST','0');
define('PK_BADWORDS','http:,cache:,site:,utm_source,sex,porn,gamble,xxx,nude,squirt,gay,abortion,attack,bomb,casino,cocaine,die,death,erection,gambling,heroin,marijuana,masturbation,pedophile,penis,poker,pussy,terrorist');
define('PK_ACTIVATED','Thank you for registering the plugin. It has been activated.');
define('PK_DB_VERSION','2');
/**
* plugin action & filter
**/
register_activation_hook(__file__,'pk_stt2_admin_activation');
register_deactivation_hook(__file__,'pk_stt2_admin_deactivation');
add_action('admin_menu','pk_stt2_admin_menu_hook');
add_action('admin_notices', 'pk_stt2_admin_notices');	
add_action('wp_head', 'pk_stt2_function_wp_head_hook');
add_action('pk_stt2_admin_event_hook', 'pk_stt2_admin_scheduled_event'); 
add_action('pk_stt2_promote_old_post_event_hook', 'pk_stt2_promote_old_post_scheduled_event'); 
add_filter('the_content','pk_stt2_admin_content_filter');
/**
* == ADMIN SECTION ==
* wordpress admin setting
**/
/**
 * install the plugin and save the initial values
 * */ 
function pk_stt2_admin_activation(){
	pk_stt2_db_create_table();  
	add_option('pk_stt2_enabled', '1'); 	
	$plugin_settings = array( 'max' => PK_MAX_SEARCH_TERMS, 'auto_add' => PK_AUTO_ADD, 'auto_link' => PK_AUTO_LINK,
		'show_count' => PK_SHOW_COUNT, 'before_keyword' => PK_BEFORE_KEYWORD, 'after_keyword' => PK_AFTER_KEYWORD,
		'before_list' => PK_BEFORE_LIST, 'after_list' => PK_AFTER_LIST,'list_header' => PK_LIST_HEADER );
	
	add_option ( 'pk_stt2_settings', $plugin_settings );
	add_option ( 'onlist_status', 0 );
	add_option ( 'pk_stt2_auto_tag', PK_AUTO_TAG );
	add_option ( 'pk_stt2_promote_old_post', PK_PROMOTE_OLD_POST );
	add_option ( 'pk_stt2_db_version', PK_DB_VERSION );
	$auto_cleanup = intval( get_option ( 'pk_stt2_auto_cleanup' ) );
	$auto_cleanup = ( 0 == $auto_cleanup ) ? PK_AUTO_CLEANUP : $auto_cleanup;
	add_option ( 'pk_stt2_auto_cleanup', $auto_cleanup );	
	add_option ( 'pk_stt2_badwords', PK_BADWORDS );			
		
	if (!wp_next_scheduled('pk_stt2_admin_event_hook')) {
		wp_schedule_event( time(), 'daily', 'pk_stt2_admin_event_hook' );
	}
	
	if (!wp_next_scheduled('pk_stt2_promote_old_post_event_hook')) {
		wp_schedule_event( time(), 'twicedaily', 'pk_stt2_promote_old_post_event_hook' );
	}
	
	pk_stt2_flush_rewrite_rules();
}
/**
* uninstall the plugin
**/
function pk_stt2_admin_deactivation(){
	remove_filter('the_content','pk_stt2_admin_content_filter');
	wp_clear_scheduled_hook('pk_stt2_admin_event_hook');
	wp_clear_scheduled_hook('pk_stt2_promote_old_post_event_hook');
	remove_action('pk_stt2_admin_event_hook', 'pk_stt2_admin_scheduled_event');
	remove_action('pk_stt2_promote_old_post_event_hook', 'pk_stt2_promote_old_post_scheduled_event'); 
}
/**
* scheduled event to delete unpopular searchterms
**/
function pk_stt2_admin_scheduled_event(){
	$days = get_option('pk_stt2_auto_cleanup');
	if ( 0 < $days ) {
		$result = pk_stt2_db_cleanup( $days );
		update_option('pk_stt2_last_clean_up',date('F j, Y, g:i A').'; '.$result.' search term(s) deleted.');
	}
}
/**
* scheduled event to promote old post
**/
function pk_stt2_promote_old_post_scheduled_event(){
	$promote = get_option('pk_stt2_promote_old_post');
	if ( 1 == $promote ) {
		pk_stt2_promote_old_post();
	}
}
/*
* display admin notice to upgrade the database when previous format of database exist
*/
function pk_stt2_admin_notices() {
	if ( get_option('onlist_status') < 2 || '2' !== get_option('pk_stt2_db_version') ){		
		echo "<div class='updated'><p>" . sprintf(__('<a href="%s">SEO SearchTerms Tagging 2</a> plugin need your attention.'), "options-general.php?page=".basename(plugin_basename(__FILE__))). "</p></div>";
	}
} 
/**
 * framework to handle the admin form
 * */ 
function pk_stt2_create_admin_menu(){		
	pk_stt2_admin_print_title();
	if ( trim($_GET['onlist']) == 1 ) 
		echo '<div id="message" class="updated fade"><p><strong>'.PK_ACTIVATED.'</strong></p></div>';
		
	if ( pk_stt2_onlist() ) {
		pk_stt2_admin_print_header();
		
		if ('2' !== get_option('pk_stt2_db_version') && !isset($_POST['upgrade_db_structure'])){
			pk_stt2_admin_upgrade_db();	
		} else {
		
			/* intercept form submit */
			if (isset($_POST['submit'])){
				pk_stt2_admin_update_options();
			} elseif ( isset($_POST['upgrade_db_structure']) ){
				pk_stt2_db_upgrade_db_structure();
				if ( '2' == get_option('pk_stt2_db_version') ){ ?>
					<div id="message" class="updated fade">
					<p>SEO SearchTerms Tagging 2 database was successfully upgraded for better performance.</p>
					</div>
				<?php } else { ?>
					<div id="message" class="updated fade" style='background-color:#f66;'>
					  <p>Failed to upgrade SEO SearchTerms Tagging 2 database! </p>
					</div>
				<?php }			
			} elseif ( isset($_POST['delete']) || isset($_POST['delete_all']) ){
				pk_stt2_admin_delete_searchterms();
			} 			
			/* print admin page */
			if (isset($_GET['stats'])){
				pk_stt2_admin_print_stats();
			} elseif (isset($_GET['stt2_help'])){
				pk_stt2_admin_help();
			} elseif (isset($_GET['donate'])){
				pk_stt2_admin_donate();
			} elseif (isset($_GET['stt2_no_traffics'])){
				pk_stt2_admin_print_no_traffic($_GET['stt2_no_traffics']);
			} else {
				pk_stt2_admin_print_admin_page();
			}
		
		}		
		
		pk_stt2_admin_print_footer();
	}
}
/**
* print admin page title
**/
function pk_stt2_admin_print_title(){
	?>
	<style type="text/css">.stt2-table{ margin: 14px; } .stt2-table tr{ height: 28px; } .inside p{ margin: 14px; } .inside .frame { margin: 10px; } .inside .list li { font-size: 11px; }
	</style>
	<div class = "wrap">
		<div class="icon32" id="icon-options-general"><br></div>
		<h2>SEO SearchTerms Tagging 2</h2>
    <?php 
}
/**
* print admin page header
**/
function pk_stt2_admin_print_header(){
	$baseurl = "options-general.php?page=".basename(plugin_basename(__FILE__));
	?>
		<div><p>        If you think this plugin useful, please consider making a
		<a href="<?php echo $baseurl.'&donate'; ?>">donation</a> or write a review about it or at least give it a good rating on
		<a href="http://wordpress.org/extend/plugins/searchterms-tagging-2/" target="_blank">WordPress.org</a>.</p></div>
		<p><a href="<?php echo $baseurl; ?>">Settings</a> | <a href="<?php echo get_bloginfo('url').'/wp-admin/widgets.php'; ?>">Widgets</a> | <a href="<?php echo $baseurl.'&stt2_help=1'; ?>">Help</a> | <strong>More Stats:</strong> <a href="<?php echo $baseurl.'&stats=1&count=50'; ?>">50</a>, 
		<a href="<?php echo $baseurl.'&stats=1&count=100'; ?>">100</a>, 
		<a href="<?php echo $baseurl.'&stats=1&count=200'; ?>">200</a>, 
		<a href="<?php echo $baseurl.'&stats=1&count=300'; ?>">300</a>, 
		<a href="<?php echo $baseurl.'&stats=1&count=400'; ?>">400</a>,
		<a href="<?php echo $baseurl.'&stats=1&count=500'; ?>">500</a> | <strong>  
		<a href="<?php echo $baseurl.'&stt2_no_traffics=0'; ?>">Posts Without Any Traffic from Search Engines</a></strong> ( <a href="<?php echo $baseurl.'&stt2_no_traffics=1'; ?>">URL Only</a> )</p>
    <?php
}
/*
* delete unwanted searchterms from admin menu
*/
function pk_stt2_admin_delete_searchterms(){
	global $wpdb;
	if ( isset($_POST['delete_terms']) && !empty($_POST['delete_terms']) ){
		$msg = 'Search terms that contain "'.$_POST['delete_terms'].'"';
		$success = pk_stt2_db_delete_searchterms( $_POST['delete_terms'] );
	} elseif ( isset($_POST['delete_all']) ){
		$msg = 'All search terms';
		$success = pk_stt2_db_delete_searchterms( 'delete_all_terms' );	
	} else {
		?>
		<div id="message" class="updated fade">
			<p>        Please enter the search terms you want to delete, separate them with a comma.
			</p>
		</div>
		<?php	
	}
	$msg .= ( $success ) ? ' has been removed from the database.' : ' cannot be removed from the database or cannot be found in the database.';
	?>
	<div id="message" class="updated fade">
		<p><?php echo $msg; ?></p>
	</div>
	<?php
}
 
/**
 * display list of search terms, used only in the admin page
 * */ 
function pk_stt2_admin_print_searchterms( $type = 'popular' ){
	$count = ( isset($_GET['stats']) ) ? $_GET['count'] : 15;
	switch ( $type ) {
		case 'popular':
			$searchterms = pk_stt2_db_get_popular_terms( $count );
			break;
		case 'recent':
			$searchterms = pk_stt2_db_get_recent_terms( $count );
			break;
		case 'home':
			$searchterms = pk_stt2_db_get_home_keywords( $count );
			break;
	}
	if(!empty($searchterms)) {
		$toReturn .= "<ol>";
		foreach($searchterms as $term){		
			$permalink = ( 0 == $term->post_id ) ? 'http://www.google.com/search?q='.str_replace(' ','+',$term->meta_value) : get_permalink($term->post_id);	
			$toReturn .= "<li><a href=\"$permalink\" target=\"_blank\">$term->meta_value</a>";	
			$toReturn .= " ($term->meta_count)</li>";
		}
		$toReturn = trim($toReturn,', ');		
		$toReturn .= "</ol>";
		//$toReturn = htmlspecialchars_decode($toReturn);
		return $toReturn;
    } else {
    	return false;
    }
}
/**
 *  print the admin form
 *  */ 
function pk_stt2_admin_print_admin_page(){
	$options = get_option('pk_stt2_settings');	
	$auto_cleanup = intval( get_option ( 'pk_stt2_auto_cleanup' ) );
	$auto_cleanup = ( 0 == $auto_cleanup ) ? PK_AUTO_CLEANUP : $auto_cleanup;	
	$promote_old_post = get_option('pk_stt2_promote_old_post');
	$badwords = get_option('pk_stt2_badwords');
	if ( empty($badwords) ){
	   $badwords = PK_BADWORDS;
     update_option ( 'pk_stt2_badwords', trim( $badwords,' ,.' ) );	
  };
	?>
	<div class="postbox-container" style="width: 74%;">
		<div class="metabox-holder">
			<div class="meta-box-sortables ui-sortable">    		
				<div id="stt2settings" class="postbox">
					<div class="handlediv" title="Click to toggle">
						<br/>
					</div>
					<h3 class="hndle">
						<span>General Settings ( Widgets has it own settings )</span></h3>
					<div class="inside">
						<form id="stt2-options" method="post" action="">
							<table class="stt2-table">
								<tr>
									<td width="350px">
										<label>             Enabled:
										</label>
									</td>
									<td>
										<input type="checkbox" <?php if( 1 == get_option('pk_stt2_enabled') ){ echo 'checked'; }; ?> value="1" name="enabled"/>
									</td>
								</tr>
								<tr>
									<td width="350px">
										<label>             Max number of search terms:
										</label>
									</td>
									<td>
										<input type = "text" name = "max" value = "<?php echo $options['max']; ?>" size="10"/>
									</td>
								</tr>
								<tr>
									<td>
										<label>             Text and code for list header:
										</label>
									</td>
									<td>
										<input type = "text" name = "list_header" value = "<?php echo htmlspecialchars(stripslashes($options['list_header'])); ?>" size="50"/>
									</td>
								</tr>
								<tr>
									<td>
										<label>             Text and code before and after the list:
										</label>
									</td>
									<td>         Before:
										<input type = "text" name = "before_list" value = "<?php echo htmlspecialchars(stripslashes($options['before_list'])); ?>" size="10"/>         &nbsp;&nbsp;After:
										<input type = "text" name = "after_list" value = "<?php echo htmlspecialchars(stripslashes($options['after_list'])); ?>" size="10"/>
									</td>
								</tr>
								<tr>
									<td>
										<label>             Text and code before and after each keyword:
										</label>
									</td>
									<td>         Before:
										<input type = "text" name = "before_keyword" value = "<?php echo htmlspecialchars(stripslashes($options['before_keyword'])); ?>" size="10"/>         &nbsp;&nbsp;After:
										<input type = "text" name = "after_keyword" value = "<?php echo htmlspecialchars(stripslashes($options['after_keyword'])); ?>" size="10"/>
									</td>
								</tr>
								<tr>
									<td style="vertical-align: top;">
										<label>             Convert search terms into links:
										</label>
									</td>
									<td>
										<Input type="radio" name="auto_link" value="1" <?php if ( 1 == $options['auto_link'] ){ echo 'checked'; } ;?> />         Yes, link to post content<br />
										<Input type="radio" name="auto_link" value="2" <?php if ( 2 == $options['auto_link'] ){ echo 'checked'; } ;?> />         Yes, link to search page ( Not Recommended )<br />
										<Input type="radio" name="auto_link"  value="0" <?php if ( 0 == $options['auto_link'] ){ echo 'checked'; } ;?> />         No
									</td>
								</tr>
								<tr>
									<td>
										<label>             Display search counts for each search term:
										</label>
									</td>
									<td>
										<Input type="radio" name="show_count" value="1" <?php if ( 1 == $options['show_count'] ){ echo 'checked'; } ;?> />         Yes&nbsp;&nbsp;
										<Input type="radio" name="show_count"  value="0" <?php if ( 0 == $options['show_count'] ){ echo 'checked'; } ;?> />         No
									</td>
								</tr>
								<tr>
									<td>
										<label>             Add list automatically right after post content:
										</label>
									</td>
									<td>
										<Input type="radio" name="auto_add" value="1" <?php if ( 1 == $options['auto_add'] ){ echo 'checked'; } ;?> />         Yes&nbsp;&nbsp;
										<Input type="radio" name="auto_add" value="0" <?php if ( 0 == $options['auto_add'] ){ echo 'checked'; } ;?> />         No
									</td>
								</tr>
								<tr>
									<td>
										<label>             Save popular search terms as post tags:
										</label>
									</td>
									<td>
										<Input type="radio" name="auto_tag" value="1" <?php if ( 1 == get_option('pk_stt2_auto_tag') ){ echo 'checked'; } ;?> />         Yes&nbsp;&nbsp;
										<Input type="radio" name="auto_tag" value="0" <?php if ( 0 == get_option('pk_stt2_auto_tag') ){ echo 'checked'; } ;?> />         No
									</td>
								</tr>
								<tr>
									<td>
										<label>             Auto clean up unused search terms after:
										</label>
									</td>
									<td>
										<input type = "text" name = "auto_cleanup" value = "<?php echo $auto_cleanup; ?>" size="10"/> days ( default is 90 days )
									</td>
								</tr>								
								<tr>
									<td valign="top" style="padding-top:10px;">
										<label>Block the following bad words:</label>
									</td>
									<td>
										<textarea name = "badwords" cols="55" rows="3" ><?php echo $badwords; ?></textarea>
									</td>
								</tr>
								<tr>
									<td>
										<label>             Promote old post with no search engine traffic:
										</label>
									</td>
									<td>
										<Input type="radio" name="promote_old_post" value="1" <?php if ( 1 == $promote_old_post ){ echo 'checked'; } ;?> />         Yes&nbsp;&nbsp;
										<Input type="radio" name="promote_old_post" value="0" <?php if ( 0 == $promote_old_post ){ echo 'checked'; } ;?> />         No
									</td>
								</tr>								
								<tr>
									<td colspan=2>
										<br />
										<span class="submit" style="margin-top:14px;">
											<input class="button-primary" type = "submit" name="submit" value="Save Changes" />
										</span>
									</td>
								</tr>
							</table>
						</form>
					</div>
				</div>
				<div id="stt2-cleanup" class="postbox">
					<div class="handlediv" title="Click to toggle">
					</div>
					<h3 class="hndle">
						<span>Auto Clean Up Unused Search Terms:</span></h3>
					<div class="inside">
						<p><ul style="margin: 0pt 0pt 14px 30px; list-style-type: circle;">
							<?php $last_cleanup = get_option('pk_stt2_last_clean_up'); ?>
							<li>Last database cleaned up: <?php if (!empty($last_cleanup)) { echo $last_cleanup; } else { echo 'Never'; } ?></li>
							<li>Next scheduled database clean up on
							<?php echo date('F j, Y, g:i A',wp_next_scheduled('pk_stt2_admin_event_hook')); ?>.</li>
						</ul></p>
						<p>Once a day, we perform database clean up by removing search terms that are never used again within <?php echo $auto_cleanup; ?> days. It is necessary to prevent an excessive use of server resources, especially if you use shared web hosting.</p>
						<p>If your blog has more than a thousand visitors per day, we recommend to reduce the auto clean up setting. You are free to determine when a search term is considered not useful, 7, 15, or 30 days maybe?</p>
					</div>
				</div>
				<div id="stt2-promote-post" class="postbox">
					<div class="handlediv" title="Click to toggle">
					</div>
					<h3 class="hndle">
						<span>Promote Post With No Search Engine Traffic</span></h3>
					<div class="inside">
						<p><ul style="margin: 0pt 0pt 14px 30px; list-style-type: circle;">
							<?php 
								$wo_traffic = pk_stt2_db_get_number_of_posts_wo_traffic(); 
								$total = pk_stt2_db_get_number_of_posts();
								$percentage = intval(( $wo_traffic / $total ) * 100); ?>		
							<li>Number of posts with no search engine traffic: <?php echo "$wo_traffic of $total ( $percentage% )"; ?></li>
							<li>Last promoted blog post: <?php pk_stt2_the_last_promoted_post(); ?></li>
							<li>Next scheduled promotion on	<?php echo date('F j, Y, g:i A',wp_next_scheduled('pk_stt2_promote_old_post_event_hook')); ?>.</li>
						</ul></p>
						<p>To ensure that the promoted post get indexed, use free services such as <a href="http://twitterfeed.com/" target="_blank">TwitterFeed</a> to publish your blog feed into social network sites such as Twitter, Facebook, Ping.fm etc.</p>
						<p>As an alternative, you can also use a premium plugin like <a href="http://9cab9omg4qgd8r98wbjl1jucfe.hop.clickbank.net/?tid=stt2"  target="_blank">Indexing Tool</a> to make sure that your articles are indexed.</p>
					</div>
				</div>				
				<div id="stt2-del" class="postbox">
					<div class="handlediv" title="Click to toggle">
					</div>
					<h3 class="hndle">
						<span>Delete Search Terms:</span></h3>
					<div class="inside">
						<form id="stt2-delete" method="post" action="">
							<p> Enter the search terms you want to delete, separate them with a comma ( e.g.: keyword1,keyword2 ): </p>
							<p><textarea name = "delete_terms" cols="75" rows="2" ></textarea></p>
							<p>
								<span class="submit">
									<input type = "submit" name="delete" value="Delete" /><br /><br /> &nbsp;&nbsp;&nbsp;OR <br /><br />
									<input type = "submit" name="delete_all" value="Delete All ( Reset )" />
								</span>
							</p>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="postbox-container" style="width: 25%;">
		<div class="metabox-holder">
			<div class="meta-box-sortables ui-sortable">
				<div id="stt2-donate" class="postbox">
					<div class="handlediv" title="Click to toggle">
					</div>
					<h3 class="hndle">
						<span>Please Donate to Support Us:
						</span></h3>
					<div class="inside">
						<div class="frame list">
							<?php echo pk_stt2_admin_donate(); ?>
						</div>
					</div>
				</div>
				<div id="stt2-top" class="postbox">
					<div class="handlediv" title="Click to toggle">
					</div>
					<h3 class="hndle">
						<span>Popular Search Terms:
						</span></h3>
					<div class="inside">
						<div class="frame list">
							<?php echo pk_stt2_admin_print_searchterms( 'popular' ); ?>
						</div>
					</div>
				</div>
				<div id="stt2-recent" class="postbox">
					<div class="handlediv" title="Click to toggle">
					</div>
					<h3 class="hndle">
						<span>Recent Search Terms:
						</span></h3>
					<div class="inside">
						<div class="frame list">
							<?php echo pk_stt2_admin_print_searchterms( 'recent' ); ?>
						</div>
					</div>
				</div>
				<div id="stt2-mainkeywords" class="postbox">
					<div class="handlediv" title="Click to toggle">
					</div>
					<h3 class="hndle">
						<span>Popular Home Keywords:
						</span></h3>
					<div class="inside">
						<div class="frame list">
							<?php echo pk_stt2_admin_print_searchterms( 'home' ); ?>
						</div>
					</div>
				</div>				
			</div>
		</div>
	</div>
	<?php
}
/** 
 * filter the content and add the search terms right after the post content ( on single and page only )
 * */ 
function pk_stt2_admin_content_filter($content){	
	if ( !is_home() ){
		$options = get_option('pk_stt2_settings');
		if($options['auto_add'])
			$content .= stt_terms_list();			
	}
	return $content;
}
/**
 * update options and save it to db
 * */ 
function pk_stt2_admin_update_options(){	
	if ( isset($_POST['max']) && isset($_POST['before_keyword']) && isset($_POST['after_keyword']) && 
	isset($_POST['auto_add']) && isset($_POST['auto_link']) && isset($_POST['show_count']) && isset($_POST['before_list']) 
	&& isset($_POST['after_list']) && isset($_POST['list_header']) ){	
	
		$options['max'] = ( intval($_POST['max']) < 2 ) ? 2 : intval($_POST['max']);
		$options['before_keyword'] = $_POST['before_keyword'];
		$options['after_keyword'] = $_POST['after_keyword'];
		$options['before_list'] = $_POST['before_list'];
		$options['after_list'] = $_POST['after_list'];		
		$options['list_header'] = $_POST['list_header'];				
		$options['auto_add'] = intval($_POST['auto_add']);
		$options['auto_link'] = intval($_POST['auto_link']);
		$options['show_count'] = intval($_POST['show_count']);		
		update_option( 'pk_stt2_settings', $options );			
		update_option( 'pk_stt2_enabled', $_POST['enabled'] );
		update_option( 'pk_stt2_auto_tag', intval($_POST['auto_tag']) );	
		update_option( 'pk_stt2_promote_old_post', intval($_POST['promote_old_post']) );	
		update_option ( 'pk_stt2_badwords', trim( $_POST['badwords'], ' ,.' ) );		
		pk_stt2_db_delete_searchterms ( $_POST['badwords'] );
		$autocleanup = ( 0 == intval($_POST['auto_cleanup']) ) ? 90 : intval($_POST['auto_cleanup']);
		update_option( 'pk_stt2_auto_cleanup', $autocleanup );			
		?>
		<div id="message" class="updated fade">
			<p>        Options saved.
			</p>
		</div>
		<?php
	} else {
		?>
		<div id="message" class="updated fade">
			<p>        Failed to save options.
			</p>
		</div>
		<?php
		pk_stt2_flush_rewrite_rules();
	}
}
/**
 * add the menu into WordPress admin menu
 * */ 
function pk_stt2_admin_menu_hook(){
    if (function_exists('add_options_page')) {
		add_options_page(
			'SEO SearchTerms 2',
			'SEO SearchTerms 2',
			'manage_options',
			'searchterms-tagging2.php',
			'pk_stt2_create_admin_menu'
		);
	}
}
/**
* print footer
**/
function pk_stt2_admin_print_footer(){
?>
<div class="postbox-container" style="width: 98%;">
  <div class="metabox-holder">
       <div id="stt2-copyright" class="postbox">
        <div class="inside">
          <div class="frame" style="text-align:center">
		  <p><strong>Recommended SEO Resources: <a href="http://www.warriorplus.com/linkwso/6pwllx/11123" target="_blank">Easy WP SEO Plugin</a> | <a href="http://www.warriorplus.com/linkwso/j2jz3p/11123" target="_blank">Stealth Keyword Competition Analyzer</a> | <a href="http://www.warriorplus.com/linkwso/2ns6d1/11123" target="_blank">FastAttacks SEO eBook</a></strong></p>
		  <p>Copyright &copy; 2010-2011 by Purwedi Kurniawan. Feel free to <a href="http://exclusivewp.com/contact/" target="_blank">contact me</a> if you need help with the plugin.</p> </div>
        </div>
     </div>
  </div>
</div>
</div>
<?php }
/**
* == DATABASE SECTION ==
* all main database related functions of searchterms tagging 2 plugin
**/
/**
* list blog posts with no search engine traffic
* @return: OBJECT results with ID, post_title properties
**/
function pk_stt2_db_get_posts_wo_traffic( $count=100 ){
	$results = wp_cache_get( 'stt2_posts_wo_traffic_'.$count );
	if ( false == $results ) {
		global $wpdb;
		$sql = "SELECT ID, post_title FROM $wpdb->posts
				WHERE post_type = 'post'
					AND post_status = 'publish'
					AND ID NOT IN ( SELECT post_id FROM ".$wpdb->prefix."stt2_meta )
				ORDER BY post_date DESC LIMIT ".$count.";";
		$results = $wpdb->get_results( $sql );			
		wp_cache_set( 'stt2_posts_wo_traffic_'.$count, $results, 3600 );
	} 		
	if ( $results ) return $results;
}
/**
* promote single blog post with no search engine traffic
* @params: $old_post_ID, $new_time, $gmt_time
* @return: 1 if succes
**/
function pk_stt2_db_promote_single_post_wo_traffic( $old_post_ID ){
	global $wpdb;
	$new_time = date('Y-m-d H:i:s');
	$gmt_time = get_gmt_from_date($new_time);
	$sql = "UPDATE $wpdb->posts 
		SET post_date = '$new_time', post_date_gmt = '$gmt_time', post_modified = '$new_time', post_modified_gmt = '$gmt_time' 
		WHERE ID = '$old_post_ID';";	
	$return = $wpdb->query($sql);
	return $return;
}
/**
 * get the number of posts with no traffic yet
 **/
function pk_stt2_db_get_number_of_posts_wo_traffic(){
	$post_count = wp_cache_get( 'stt2_number_of_posts_wo_traffic' );
	if ( false == $post_count ) {
		global $wpdb;
		$sql = "SELECT count(`ID`) FROM $wpdb->posts WHERE `post_status` = 'publish' AND `post_type` = 'post' AND ID NOT IN ( 
			SELECT post_id FROM ".$wpdb->prefix."stt2_meta );";
		$post_count = $wpdb->get_var($wpdb->prepare( $sql ));	
		wp_cache_set( 'stt2_number_of_posts_wo_traffic', $post_count, 86400 );
	} 		
	return $post_count;
}
/**
 * get single post ID of posts with no traffic yet
 * doesn't caching, only run twice a day, and need the most current status
 **/
function pk_stt2_db_get_id_post_wo_traffic(){
	global $wpdb;
	$sql = "SELECT `ID` FROM $wpdb->posts WHERE `post_status` = 'publish' AND `post_type` = 'post' AND ID NOT IN ( 
		SELECT post_id FROM ".$wpdb->prefix."stt2_meta WHERE `post_id` != 0 ) LIMIT 1;";
	$post_ID = $wpdb->get_var($wpdb->prepare( $sql ));	
	return $post_ID;
}
/**
* get last promoted post title
**/
function pk_stt2_db_get_last_promoted_post_title( $id ){
	$post_title = wp_cache_get( 'stt2_last_promoted_post_title_'.$id );
	if ( false == $post_title ) {
		global $wpdb;
		$sql = "SELECT `post_title` FROM $wpdb->posts WHERE `ID` = $id;";
		$post_title = $wpdb->get_var($wpdb->prepare( $sql ));	
		wp_cache_set( 'stt2_last_promoted_post_title_'.$id, $post_title );
	} 			
	return $post_title;	
}
/**
 * get the number of posts
 **/
function pk_stt2_db_get_number_of_posts(){
	$post_count = wp_cache_get( 'stt2_get_number_of_posts' );
	if ( false == $post_count ) {
		global $wpdb;
		$sql = "SELECT count(`ID`) FROM $wpdb->posts WHERE `post_status` = 'publish' AND `post_type` = 'post';";
		$post_count = $wpdb->get_var($wpdb->prepare( $sql ));	
		wp_cache_set( 'stt2_get_number_of_posts'.$id, $post_count, 3600 );
	} 				
	return $post_count;
}
/**
 * get 10 popular search terms for the posts, will be used as tags
 * @param $id: post id
 * @return: comma separated text of popular search terms 
 **/
function pk_stt2_db_get_popular_tags( $id ){
	$a_results = wp_cache_get( 'stt2_get_popular_tags_'.$id );
	if ( false == $a_results ) {
		global $wpdb;
		$sql = "SELECT meta_value FROM ".$wpdb->prefix."stt2_meta WHERE ( post_id = ".$id." AND meta_count > 25 AND meta_value NOT LIKE '%http%' ) ORDER BY meta_count DESC LIMIT 5;";
		$a_results = $wpdb->get_results( $sql );
		wp_cache_set( 'stt2_get_popular_tags_'.$id, $a_results, 86400 );
	} 	
	if ( $a_results ){
		foreach ( $a_results as $value ){
			$result .= $value->meta_value.',';
		}
		$result = trim($result,',');
	} 
	return $result;
}
/**
 * get popular search terms
 * @param $count: max number of search terms
 **/
function pk_stt2_db_get_popular_terms( $count ){
	$result = wp_cache_get( 'stt2_popular_terms' );
	if ( false == $result ) {
		global $wpdb;
		$result = $wpdb->get_results( "SELECT `meta_value`,`meta_count`,`post_id` FROM `".$wpdb->prefix."stt2_meta` WHERE `post_id` != 0 ORDER BY `meta_count` DESC LIMIT ".$count.";" );		
		wp_cache_set( 'stt2_popular_terms', $result, 86400 );
	} 
	return $result;
}
/**
 * get home keywords
 * @param $count: max number of search terms
 **/
function pk_stt2_db_get_home_keywords( $count ){
	$result = wp_cache_get( 'stt2_home_keywords' );
	if ( false == $result ) {
		global $wpdb;
		$result = $wpdb->get_results( "SELECT `meta_value`,`meta_count`,`post_id` FROM `".$wpdb->prefix."stt2_meta` WHERE `post_id` = 0 ORDER BY `meta_count` DESC LIMIT ".$count.";" );
		wp_cache_set( 'stt2_home_keywords', $result, 86400 );		
	}
	return $result;
}
/**
 * get list of search terms
 * @param $max: max number of search terms
 **/
function pk_stt2_db_get_search_terms( $max ){
	$result = wp_cache_get( 'stt2_search_terms_'.$max );
	if ( false == $result ) {
		global $wpdb, $post;	   
		$result = $wpdb->get_results( "SELECT `meta_value`,`meta_count` FROM `".$wpdb->prefix."stt2_meta` WHERE `post_id` = $post->ID ORDER BY `meta_count` DESC LIMIT ".$max.";" );		
		wp_cache_set( 'stt2_search_terms_'.$max, $result, 900 );		
	}	
	return $result;
}
/**
 * get recent search terms
 * @param $count: max number of search terms
 **/
function pk_stt2_db_get_recent_terms( $count ){
	$result = wp_cache_get( 'stt2_recent_terms' );
	if ( false == $result ) {
		global $wpdb;
		$result = $wpdb->get_results( "SELECT `meta_value`,`meta_count`,`post_id` FROM `".$wpdb->prefix."stt2_meta` WHERE `post_id` != 0 ORDER BY `last_modified` DESC LIMIT ".$count.";" );			
		wp_cache_set( 'stt2_recent_terms', $result, 900 );		
	}		
	return $result;
}
/**
 * get random search terms
 * @param $count: max number of search terms
 **/
function pk_stt2_db_get_random_terms( $count ){
	$result = wp_cache_get( 'stt2_random_terms' );
	if ( false == $result ) {
		global $wpdb;
		$result = $wpdb->get_results( "SELECT `meta_value`,`post_id` FROM `".$wpdb->prefix."stt2_meta`  WHERE `post_id` != 0 ORDER BY RAND() LIMIT ".$count.";" );			
		wp_cache_set( 'stt2_random_terms', $result, 3600 );		
	}	
	return $result;
}
/**
 * get the number of searchterms
 **/
function pk_stt2_db_get_number_of_searchterms(){
	$result = wp_cache_get( 'stt2_number_of_searchterms' );
	if ( false == $result ) {
		global $wpdb;
		$result = $wpdb->get_var("SELECT COUNT(`meta_value`) FROM ".$wpdb->prefix."stt2_meta;");
		wp_cache_set( 'stt2_number_of_searchterms', $result, 3600 );		
	}		
	return $result;
}
/**
 * delete searchterms from the database
 * @param $searchterms: comma separated search terms or 'delete_all_terms'
 * @output: number of rows effected by the delete query
 **/
function pk_stt2_db_delete_searchterms( $searchterms ){
	global $wpdb;
	if ( 'delete_all_terms' !== $searchterms ) {
		$arr_searchterms = explode(',',$searchterms);		
		foreach ($arr_searchterms as $value) {
			if (!empty($value)) {
				$success += $wpdb->query( "DELETE FROM ".$wpdb->prefix."stt2_meta WHERE LOWER(meta_value) LIKE '%". strtolower(trim($value)) ."%'; ");	
			}
		}
	} else {		
		$success = $wpdb->query( "DELETE FROM ".$wpdb->prefix."stt2_meta" );	
	}
	$opt = $wpdb->query('OPTIMIZE TABLE '.$wpdb->prefix.'stt2_meta;');
	return $success;
}
/**
* clean the database in daily basis
**/
function pk_stt2_db_cleanup( $days ){
	global $wpdb;
	$result = $wpdb->query('DELETE FROM '.$wpdb->prefix.'stt2_meta WHERE (meta_count < 10) AND (date(last_modified) < date(now()-interval '.$days.' day));');
	$opt = $wpdb->query('OPTIMIZE TABLE '.$wpdb->prefix.'stt2_meta;');
	return $result;
}
/**
 * create stt2 database; UTF-8 version
 * */ 
function pk_stt2_db_create_table() {
   global $wpdb;  
   $table_name = $wpdb->prefix.'stt2_meta';
   if($wpdb->get_var("SHOW TABLES LIKE '$table_name';") != $table_name) {      
        $sql = "CREATE TABLE `".$wpdb->prefix."stt2_meta` (
    		`post_id` INT( 20 ) NOT NULL ,
    		`meta_value` VARCHAR ( 255 ) NOT NULL,
    		`meta_count` INT( 20 ) NOT NULL DEFAULT '1',
    		`last_modified` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    		UNIQUE (`meta_value`),			
    		PRIMARY KEY ( `post_id` , `meta_value` )			
    		) CHARACTER SET utf8
			DEFAULT CHARACTER SET utf8
			COLLATE utf8_general_ci
			DEFAULT COLLATE utf8_general_ci;";	
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
		update_option('pk_stt2_db_version','2');
   }
}
/**
* update stt2 database into version 2; UTF-8 with additional UNIQUE column
**/
function pk_stt2_db_upgrade_db_structure(){
	global $wpdb;
	$sql = "CREATE TABLE `".$wpdb->prefix."stt2_meta_tmp` (
		`post_id` INT( 20 ) NOT NULL ,
		`meta_value` VARCHAR ( 255 ) NOT NULL,
		`meta_count` INT( 20 ) NOT NULL DEFAULT '1',
		`last_modified` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		UNIQUE (`meta_value`),
		PRIMARY KEY ( `post_id` , `meta_value` )			
		) CHARACTER SET utf8
		DEFAULT CHARACTER SET utf8
		COLLATE utf8_general_ci
		DEFAULT COLLATE utf8_general_ci;";
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	dbDelta($sql);
		
	$sql = "INSERT IGNORE INTO `".$wpdb->prefix."stt2_meta_tmp` ( `post_id`,`meta_value`,`meta_count`,`last_modified` )
		SELECT `post_id`,`meta_value`,`meta_count`,`last_modified` FROM `".$wpdb->prefix."stt2_meta`;";					 
	$return = $wpdb->query( $sql );
	
	$sql = "RENAME TABLE `".$wpdb->prefix."stt2_meta` TO `".$wpdb->prefix."stt2_meta_backup`,
		`".$wpdb->prefix."stt2_meta_tmp` TO `".$wpdb->prefix."stt2_meta`;";
	$return = $wpdb->query( $sql );
	
	update_option('pk_stt2_db_version','2');
}
/**
 * save search terms into database
 **/ 
function pk_stt2_db_save_searchterms( $meta_value) {	
	if ( strlen($meta_value) > 3 ){		
		if ( is_home() ){
			$ID = 0;
		} else {				
			global $post;   	
			$ID = $post->ID;			
		}
		global $wpdb; 
		$success = $wpdb->query( $wpdb->prepare( "INSERT INTO ".$wpdb->prefix."stt2_meta ( `post_id`,`meta_value`,`meta_count` ) VALUES ( %s, %s, 1 )
			ON DUPLICATE KEY UPDATE `meta_count` = `meta_count` + 1", $ID, $meta_value ) );				
	}
	return $success;
}
/**
* get popular search terms in category
* @author: pile ( http://pile.web.id )
**/
function pk_stt2_db_get_popular_searchterms_in_category( $count ){
	$results = wp_cache_get( 'stt2_popular_searchterms_in_category_'.$count );
	if ( false == $results ) {
		global $wpdb;
		$cat_ID = get_query_var('cat');
		$sql = " SELECT wps.post_id, wps.meta_value, wps.meta_count
			FROM ".$wpdb->prefix."terms
			INNER JOIN ".$wpdb->prefix."term_taxonomy ON ".$wpdb->prefix."terms.term_id = ".$wpdb->prefix."term_taxonomy.term_id
			INNER JOIN ".$wpdb->prefix."term_relationships wpr ON wpr.term_taxonomy_id = ".$wpdb->prefix."term_taxonomy.term_taxonomy_id
			INNER JOIN ".$wpdb->prefix."posts p ON p.ID = wpr.object_id
			INNER JOIN ".$wpdb->prefix."stt2_meta wps ON wps.post_id = p.ID
			WHERE taxonomy = 'category'
			AND p.post_type = 'post'
			AND p.post_status = 'publish'
			AND ( ".$wpdb->prefix."terms.term_id = '".$cat_ID."' )
			ORDER BY `wps`.`meta_count` DESC
			LIMIT ".$count.";";	
		$results = $wpdb->get_results($sql);
		wp_cache_set( 'stt2_popular_searchterms_in_category_'.$count, $results, 86400 );		
	}	
	
	return $results;
}
/**
* == MAIN SECTION ==
* all main functions of searchterms tagging 2 plugin
**/
/**
* get popular search terms on coresponding category
* @param $count: number of search terms to be displayed
**/
function stt_popular_terms_in_category( $count=10 ){
	if (is_category()) {
		$options = get_option('pk_stt2_settings');		
		$searchterms = pk_stt2_db_get_popular_searchterms_in_category( $count );		
		if(!empty($searchterms)) {
		  $result = pk_stt2_function_prepare_searchterms($searchterms,$options,true);		
		  return $result;	  
		} else {
			return false;
		}
	}
}
/**
 * save popular search terms as tags in monthly basis
  **/
function pk_stt2_function_save_tags(){
	if ( '1' == get_option('pk_stt2_auto_tag') && !empty($post->ID) ) {		
		$day_diff = ceil(( strtotime(date('F j, Y')) - strtotime( get_post_meta( $id,'stt2_update_tags',true ) ) + 1) / (60*60*24) ) ;  
		if ( 30 < $day_diff ){
			global $post;
			$tags = pk_stt2_db_get_popular_tags( $post->ID );
			wp_set_post_tags( $post->ID, $tags, true );
			update_post_meta( $post->ID,'stt2_update_tags', date('F j, Y') );		
		}
	}
}
/**
 * hooked to wp-head()
 * */ 
function pk_stt2_function_wp_head_hook() {
	
	if( 1 == intval(get_option('pk_stt2_enabled')) ){
		$referer = pk_stt2_function_get_referer();
		if (!$referer) return false;
		$delimiter = pk_stt2_function_get_delimiter($referer);
		if( $delimiter ){
			$term = pk_stt2_function_get_terms($delimiter);		
			if (!pk_stt2_is_contain_bad_words( $term )) {			
				pk_stt2_db_save_searchterms( $term );
			}
		}
		pk_stt2_function_save_tags();
	}
}
/**
 * display the search terms below post content
 * */ 
function stt_terms_list(){
	$options = get_option('pk_stt2_settings');	
	$searchterms = pk_stt2_db_get_search_terms( $options['max'] );			
	if(!empty( $searchterms )) {
      $result = pk_stt2_function_prepare_searchterms( $searchterms, $options );
	  return $result;
    } else {
    	return false;
    }	
}
/**
 * strip the slash from each search terms
 * */ 
function pk_stt2_function_stripslashes_options($options){
   foreach($options as $i=>$row){       
       $row = stripslashes($row);
       $options[$i]=$row;
   }
   return $options;
}
/**
 * common function to print the search terms
 * */ 
function pk_stt2_function_prepare_searchterms( $searchterms, $options, $popular=false ){
	global $post;
	$options = pk_stt2_function_stripslashes_options($options);
	$toReturn .= ( $popular == false ) ? $options['list_header'].$options['before_list'] : $options['before_list'];
	foreach($searchterms as $term){
		if ( 0 == $options['auto_link'] ){
			$toReturn .= $options['before_keyword'].$term->meta_value;
		} else {		
			if( !$popular ){
				if ( 1 == $options['auto_link'] ){									   
				   $permalink = get_permalink( $post->ID );		
				} elseif ( 2 == $options['auto_link']){			
					$permalink = get_bloginfo( 'url' ).'/search/'.user_trailingslashit(pk_stt2_function_sanitize_search_link($term->meta_value));
				}
			} else {
				$permalink = ( 0 == $term->post_id ) ? get_bloginfo('url') : get_permalink($term->post_id);		
			}		
			$toReturn .= $options['before_keyword']."<a href=\"$permalink\" title=\"$term->meta_value\">$term->meta_value</a>";
		}		
		$toReturn .= ( $options['show_count'] == true ) ? " ($term->meta_count)".$options['after_keyword'] : $options['after_keyword'];
	}
	$toReturn = trim($toReturn, ', ');		
	$toReturn .= $options['after_list'];
	//$toReturn = htmlspecialchars_decode($toReturn);
	return $toReturn;
}
/**
 * display popular search terms manually 
 * @param $count: number of search terms to be displayed
 * */ 
function stt_popular_terms( $count=10 ){
	$options = get_option('pk_stt2_settings');	
	$searchterms = pk_stt2_db_get_popular_terms($count);
	if(!empty($searchterms)) {
      $result = pk_stt2_function_prepare_searchterms($searchterms,$options,true);		
	  return $result;	  
    } else {
    	return false;
    }
}
/** 
 * display recent search terms manually
 * @param $count: number of search terms to be displayed 
 * */ 
function stt_recent_terms( $count=10 ){  
	$options = get_option('pk_stt2_settings');			
	$searchterms = pk_stt2_db_get_recent_terms( $count );
	if(!empty($searchterms)) {
      $result = pk_stt2_function_prepare_searchterms($searchterms,$options,true);		
	  return $result;	  
    } else {
    	return false;
    }
}
/** 
 * display random search terms manually
 * @param $count: number of search terms to be displayed 
 * */ 
function stt_random_terms( $count=10 ){ 
	$options = get_option('pk_stt2_settings');			
	$searchterms = pk_stt2_db_get_random_terms( $count );
	if(!empty($searchterms)) {
      $result = pk_stt2_function_prepare_searchterms($searchterms,$options,true);		
	  return $result;	  
    } else {
    	return false;
    }
}
/**
 * get search delimiter for each search engine
 * base on the original searchterms tagging plugin
 * */ 
function pk_stt2_function_get_delimiter($ref) {
    $search_engines = array('google.com' => 'q',
			'go.google.com' => 'q',
			'images.google.com' => 'q',
			'video.google.com' => 'q',
			'news.google.com' => 'q',
			'blogsearch.google.com' => 'q',
			'maps.google.com' => 'q',
			'local.google.com' => 'q',
			'search.yahoo.com' => 'p',
			'search.msn.com' => 'q',
			'bing.com' => 'q',
			'msxml.excite.com' => 'qkw',
			'search.lycos.com' => 'query',
			'alltheweb.com' => 'q',
			'search.aol.com' => 'query',
			'search.iwon.com' => 'searchfor',
			'ask.com' => 'q',
			'ask.co.uk' => 'ask',
			'search.cometsystems.com' => 'qry',
			'hotbot.com' => 'query',
			'overture.com' => 'Keywords',
			'metacrawler.com' => 'qkw',
			'search.netscape.com' => 'query',
			'looksmart.com' => 'key',
			'dpxml.webcrawler.com' => 'qkw',
			'search.earthlink.net' => 'q',
			'search.viewpoint.com' => 'k',
			'yandex.kz' => 'text',
			'yandex.ru' => 'text',
			'baidu.com' => 'wd',			
			'mamma.com' => 'query');
    $delim = false;
    if (isset($search_engines[$ref])) {
        $delim = $search_engines[$ref];
    } else {
        if (strpos('ref:'.$ref,'google'))
            $delim = "q";
		elseif (strpos('ref:'.$ref,'search.atomz.'))
            $delim = "sp-q";
		elseif (strpos('ref:'.$ref,'search.msn.'))
            $delim = "q";
		elseif (strpos('ref:'.$ref,'search.yahoo.'))
            $delim = "p";
		elseif (strpos('ref:'.$ref,'yandex'))
            $delim = "text";
		elseif (strpos('ref:'.$ref,'baidu'))
            $delim = "wd";	
        elseif (preg_match('/home\.bellsouth\.net\/s\/s\.dll/i', $ref))
            $delim = "bellsouth";
    }
    return $delim;
}
/**
 * retrieve the search terms from search engine query
 * */ 
function pk_stt2_function_get_terms($d) {
    $terms       = null;
    $query_array = array();
    $query_terms = null;
    $query = explode($d.'=', $_SERVER['HTTP_REFERER']);
    $query = explode('&', $query[1]);
    $query = urldecode($query[0]);
    $query = str_replace("'", '', $query);
    $query = str_replace('"', '', $query);
    $query_array = preg_split('/[\s,\+\.]+/',$query);
    $query_terms = implode(' ', $query_array);
    $terms = htmlspecialchars(urldecode(trim($query_terms)));
    return $terms;
}
/**
 * get the referer
 * */ 
function pk_stt2_function_get_referer() {
    if (!isset($_SERVER['HTTP_REFERER']) || ($_SERVER['HTTP_REFERER'] == '')) return false;
    $referer_info = parse_url($_SERVER['HTTP_REFERER']);
    $referer = $referer_info['host'];
    if(substr($referer, 0, 4) == 'www.')
        $referer = substr($referer, 4);
    return $referer;
}
/**
* sanitize link to search page
* @param $title: search engine terms
* @output: search terms in form of web safe url slug
**/
function pk_stt2_function_sanitize_search_link($title) {
	$title = strip_tags($title);
	// Preserve escaped octets.
	$title = preg_replace('|%([a-fA-F0-9][a-fA-F0-9])|', '---$1---', $title);
	// Remove percent signs that are not part of an octet.
	$title = str_replace('%', '', $title);
	// Restore octets.
	$title = preg_replace('|---([a-fA-F0-9][a-fA-F0-9])---|', '%$1', $title);
	$title = remove_accents($title);
	if (seems_utf8($title)) {
	   if (function_exists('mb_strtolower')) {
		   $title = mb_strtolower($title, 'UTF-8');
	   }
	   $title = utf8_uri_encode($title);
	}
	$title = strtolower($title);
	$title = preg_replace('/&.+?;/', '', $title); // kill entities
	$title = preg_replace('/[^%a-z0-9 _-]/', '', $title);
	$title = preg_replace('/\s+/', '-', $title);
	$title = preg_replace('|-+|', '-', $title);
	$title = trim($title, '-');
	return $title;
 }
/**
* check whether the search term contain forbidden word
**/
function pk_stt2_is_contain_bad_words( $term ){
  $option = get_option('pk_stt2_badwords');
  $option = ( empty($option) ) ? PK_BADWORDS : $option;
	$badwords = explode( ',',$option );
	$term = str_ireplace( $badwords,'***',$term );
	if( false === strpos( $term, '***' ) )
		return false;
	else
		return true;
}
/**
* == PLUGIN REGISTRATION SECTION ==
**/
/**
 * check onlist status
 */
function pk_stt2_onlist(){
	$form_1 = 'stt2_reg_form_1';
	$form_2 = 'stt2_reg_form_2';
	$pk_stt2_list = get_option('onlist_status');
	if ( trim($_GET['onlist']) == 1 || $_GET['no'] == 1 ) { 			
		$pk_stt2_list = 2; update_option('onlist_status', $pk_stt2_list);
	} 
	if ( ((trim($_GET['activate']) != '' && trim($_GET['from']) != '') || trim($_GET['activate_again']) != '') && $pk_stt2_list != 2 ) { 
		update_option('pk_stt2_name', $_GET['name']);
		update_option('pk_stt2_email', $_GET['from']);
		$pk_stt2_list = 1; update_option('onlist_status', $pk_stt2_list);
	}
	if ( $pk_stt2_list == 0 ) {
		 pk_stt2_register_1($form_1);
	} else if ( $pk_stt2_list == 1 ) {
		$name  = get_option('pk_stt2_name');
		$email = get_option('pk_stt2_email');
		pk_stt2_register_2($form_2,$name,$email);
	} else if ( $pk_stt2_list == 2 ) {
		return true;
	}
}
/**
 * Plugin registration form
 */
function pk_stt2_registration_form($form_name, $submit_btn_txt='Register', $name, $email, $hide=0, $activate_again='') {
	$wp_url = get_bloginfo('wpurl');
	$wp_url = (strpos($wp_url,'http://') === false) ? get_bloginfo('siteurl') : $wp_url;
	$thankyou_url = $wp_url.'/wp-admin/options-general.php?page='.$_GET['page'];
	$onlist_url   = $wp_url.'/wp-admin/options-general.php?page='.$_GET['page'].'&amp;onlist=1';
	$nothankyou_url   = $wp_url.'/wp-admin/options-general.php?page='.$_GET['page'].'&amp;no=1';
	
	if ( $hide == 1 ) $align_tbl = 'left';
	else $align_tbl = 'center';
	?>
	
	<?php if ( $activate_again != 1 ) { ?>
	<script><!--
	function trim(str){
		var n = str;
		while ( n.length>0 && n.charAt(0)==' ' ) 
			n = n.substring(1,n.length);
		while( n.length>0 && n.charAt(n.length-1)==' ' )	
			n = n.substring(0,n.length-1);
		return n;
	}
	function pk_stt2_validate_form_0() {
		var name = document.<?php echo $form_name;?>.name;
		var email = document.<?php echo $form_name;?>.from;
		var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
		var err = ''
		if ( trim(name.value) == '' )
			err += '- Name Required\n';
		if ( reg.test(email.value) == false )
			err += '- Valid Email Required\n';
		if ( err != '' ) {
			alert(err);
			return false;
		}
		return true;
	}
	//-->
	</script>
	<?php } ?>
	<table align="<?php echo $align_tbl;?>">
	<form name="<?php echo $form_name;?>" method="post" action="http://www.aweber.com/scripts/addlead.pl" <?php if($activate_again!=1){;?>onsubmit="return pk_stt2_validate_form_0()"<?php }?>>
	 <input type="hidden" name="meta_web_form_id" value="943418582" />	
	 <input type="hidden" name="listname" value="wp-stt2" />
	 <input type="hidden" name="redirect" value="<?php echo $thankyou_url;?>">
	 <input type="hidden" name="meta_redirect_onlist" value="<?php echo $onlist_url;?>">
	 <input type="hidden" name="meta_adtracking" value="stt2_activate" />
	 <input type="hidden" name="meta_message" value="1">
	 <input type="hidden" name="meta_required" value="from,name">
	 <input type="hidden" name="meta_forward_vars" value="1">	
	 <?php if ( $activate_again == 1 ) { ?> 	
	 <input type="hidden" name="activate_again" value="1">
	 <?php } ?>		 
	 <?php if ( $hide == 1 ) { ?> 
	 <input type="hidden" name="name" value="<?php echo $name;?>">
	 <input type="hidden" name="from" value="<?php echo $email;?>">
	 <?php } else { ?>
	 <tr><td>Name: </td><td><input type="text" name="name" value="<?php echo $name;?>" size="25" maxlength="150" /></td></tr>
	 <tr><td>Email: </td><td><input type="text" name="from" value="<?php echo $email;?>" size="25" maxlength="150" /></td></tr>
	 <?php } ?>
     <tr><td span=2>&nbsp;</td></tr>
	 <tr><td>&nbsp;</td><td><input class="button-primary" type="submit" name="activate" value="<?php echo $submit_btn_txt;?>" /> </td></tr>
	 </form>
     <form name="nothankyou" method="post" action="<?php echo $nothankyou_url;?>">
     <tr><td>&nbsp;</td><td><input class="button" type="submit" name="nothankyou" value="No Thank You!" /></td></tr>
     </form>
	</table>
	<?php
}
/**
 * Register Plugin - Step 2
 */
function pk_stt2_register_2($form_name='frm2',$name,$email) {
	$msg = 'You have not clicked on the confirmation link yet. A confirmation email has been sent to you again. Please check your email and click on the confirmation link to register the plugin.';
	if ( trim($_GET['activate_again']) != '' && $msg != '' ) {
		echo '<div id="message" class="updated fade"><p><strong>'.$msg.'</strong></p></div>';
	}
	?>
	 <center>
	 <table width="640" cellspacing="1" style="border:1px solid #e9e9e9; padding: 0 14px 14px;">
	  <tr><td align="center"><h3>Almost Done....</h3></td></tr>
	  <tr><td><h3>Step 1:</h3></td></tr>
	  <tr><td>A confirmation email has been sent to your email "<?php echo $email;?>". You must click on the confirmation link inside the email to register the plugin.</td></tr>
	  <tr><td>&nbsp;</td></tr>
	  <tr><td>The confirmation email will look like this:<br /><img src="http://exclusivewp.com/wp-content/uploads/2010/08/email-confirmation.gif" style="margin-top:10px;border:0;" /></td></tr>
	  <tr><td>&nbsp;</td></tr>
	  <tr><td><h3>Step 2:</h3></td></tr>
	  <tr><td>Click on the button below to Verify and Activate the plugin.</td></tr>
	  <tr><td>&nbsp;</td></tr>
	  <tr><td><?php pk_stt2_registration_form($form_name.'_0','Verify and Activate',$name,$email,$hide=1,$activate_again=1);?></td></tr>
	  <tr><td>&nbsp;</td></tr>
	 </table>
	 <p>&nbsp;</p>
	 <table width="640" cellspacing="1" style="border:1px solid #e9e9e9; padding: 0 14px 14px;">
	   <tr><td><h3>Troubleshooting</h3></td></tr>
	   <tr><td>1. I can't found the confirmation email in my inbox.<br/>Please check your spam or bulk folder.</td></tr>
	   <tr><td>&nbsp;</td></tr>
	   <tr><td>2. It's not there in the junk folder either.<br/>In a rare case, it took times to arrive. Please wait for 6 hours at most. If after 6 hours and still no confirmation email, please register again using the form below:</td></tr>
	   <tr><td>&nbsp;</td></tr>
	   <tr><td><?php pk_stt2_registration_form($form_name,'Register Again',$name,$email,$hide=0,$activate_again=2);?></td></tr>
	   <tr><td>&nbsp;</td></tr>
	   <tr><td>3. Still no confirmation email and I have already registered twice.<br/>Using the form above, try to register again using DIFFERENT EMAIL ADDRESS.</td></tr>
	   <tr><td>&nbsp;</td></tr>
	   <tr><td>4. I receive an error like this:<br />
			 <img src="http://exclusivewp.com/wp-content/uploads/2010/08/email-confirmation-error.gif"  style="margin-top:10px;border:0;"  /><br />		   
		   <br/>This error means that you have already subscribed but have not yet clicked on the link inside confirmation email. In order to  avoid any spam complain we don't send repeated confirmation emails.</td>
	   </tr>
	   <tr><td>&nbsp;</td></tr>
	   <tr><td>5. If you still got problems, please <a href="http://exclusivewp.com/contact/" target="_blank">contact us</a></strong> about it and we will get to you ASAP.</td></tr>
	 </table>
	 </center>		
	<p>&nbsp;</p>
	</div>
	<?php
}
/**
 * Register Plugin - Step 1
 */
function pk_stt2_register_1($form_name='frm1') {
	global $current_user;
	get_currentuserinfo();
	$name = $current_user->user_firstname;
	$email = $current_user->user_email;	
	?>
	 <center>
	 <table width="620" cellpadding="10" cellspacing="1" bgcolor="#ffffff" style="border:1px solid #e9e9e9; padding: 0 14px 14px;">
	  <tr><td align="center"><h3>Please register the plugin...</h3></td></tr>
	  <tr><td align="left">Registration is <strong>Free</strong> and only has to be done once. If you've register before or don't want to register, please click "No Thank You!" button.</td></tr>      
	  <tr><td>&nbsp;</td></tr>      
	  <tr><td align="left">In addition, you'll receive complimentary subscription to our Email Newsletter which will give you many news and tips about SEO. Of course, you can unsubscribe any time you want.</td></tr>      
	  <tr><td>&nbsp;</td></tr>
	  <tr><td align="center"><?php pk_stt2_registration_form($form_name,'Register',$name,$email);?></td></tr>
	  <tr><td>&nbsp;</td></tr>
	  <tr><td align="left">Disclaimer: Your contact information will be handled with the strictest confidence and will never be sold or shared with third parties.</td></td></tr>
	 </table>
	 </center>	
	<?php
}
/**
* == END OF PLUGIN REGISTRATION SECTION ==
**/
/**
* == ADMIN TAB SECTION ==
**/
/**
* print posts with no traffics
**/
function pk_stt2_admin_print_no_traffic($url=0){
?>
	<div class="postbox-container" style="width: 98%;">
		<div class="metabox-holder">
			<div class="meta-box-sortables ui-sortable">
				<div id="popular-search-terms" class="postbox">
					<div class="handlediv" title="Click to toggle">
					</div>
					<h3 class="hndle">
						<span>100 Posts Without Any Traffic from Search Engines: </span></h3>
					<div class="inside">
						<div class="frame list">
							<?php echo pk_stt2_admin_print_list_of_no_traffic($url); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php
}
/**
* print popular and recent search terms full stats
**/
function pk_stt2_admin_print_stats(){
?>
	<div class="postbox-container" style="width: 48%;">
		<div class="metabox-holder">
			<div class="meta-box-sortables ui-sortable">
				<div id="popular-search-terms" class="postbox">
					<div class="handlediv" title="Click to toggle">
					</div>
					<h3 class="hndle">
						<span><?php echo $_GET['count']; ?> Popular Search Terms:</span></h3>
					<div class="inside">
						<div class="frame list">
							<?php echo pk_stt2_admin_print_searchterms( 'popular' ); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="postbox-container" style="width: 48%;">
		<div class="metabox-holder">
			<div class="meta-box-sortables ui-sortable">
				<div id="recent-search-terms" class="postbox">
					<div class="handlediv" title="Click to toggle">
					</div>
					<h3 class="hndle">
						<span><?php echo $_GET['count']; ?> Recent Search Terms:</span></h3>
					<div class="inside">
						<div class="frame list">
							<?php echo pk_stt2_admin_print_searchterms( 'recent' ); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>	<?php
}
/**
* help page
**/
function pk_stt2_admin_help(){
?>
	<div class="postbox-container" style="width: 98%;">
		<div class="metabox-holder">
			<div class="meta-box-sortables ui-sortable">
				<div id="stt2-manual" class="postbox">
					<div class="handlediv" title="Click to toggle">
					</div>
					<h3 class="hndle">
						<span>Manual Usage Instructions:
						</span></h3>
					<div class="inside">
						<div class="frame">
							<ol>
								<li> To display incoming search terms to the article:
								<p>
									<span style="color:blue">         &lt;?php if(function_exists('stt_terms_list')) echo stt_terms_list() ;?&gt;
									</span>
								</p></li>
								<li> To display most popular search terms for all articles:
								<p>
									<span style="color:blue">         &lt;?php if(function_exists('stt_popular_terms')) echo stt_popular_terms(10) ;?&gt;
									</span>
								</p>
								<p>     If your theme supports widgets, it is better to use the "Popular Search Terms" widget.
								</p></li>
								<li> To display most recent search terms:
								<p>
									<span style="color:blue">         &lt;?php if(function_exists('stt_recent_terms')) echo stt_recent_terms(10) ;?&gt;
									</span>
								</p>
								<p>     If your theme supports widgets, it is better to use the "Recent Search Terms" widget.
								</p></li>
     <li> To display most popular search terms in category archive:
								<p>
									<span style="color:blue">         &lt;?php if(function_exists('stt_popular_terms_in_category')) echo stt_popular_terms_in_category(10) ;?&gt;
									</span>
								</p>
								<p>     If your theme supports widgets, it is better to use the "Popular Terms in Category" widget.
								</p></li>
<li> To display random search terms (not recommended dt extensive resources):
								<p>
									<span style="color:blue">         &lt;?php if(function_exists('stt_random_terms')) echo stt_random_terms(10) ;?&gt;
									</span>
								</p>
								<p>     If your theme supports widgets, it is better to use the "Random Search Terms" widget.
								</p></li>								
							</ol>
							<small>* Replace '10' with the number of search terms to display.</small>
						</div>
					</div>
				</div>
				<div id="stt2-faq" class="postbox">
					<div class="handlediv" title="Click to toggle">
					</div>
					<h3 class="hndle">
						<span>Frequently Asked Questions:
						</span></h3>
					<div class="inside">
						<div class="frame">
							<ol>
								<li> Do I still need to edit the template and add the plugin code manually if I choose to add the search terms list automatically after post content?
								<p>     No, the list of search terms will automatically be added right after the post content. 
								</p></li>
								<li> Where do I need to add the plugin code manually?
								<p>     You can put the plugin code into the current template that you use in file index.php or single.php and page.php. The most common place is under the post content or below the comment form.
								</p></li>
								<li> I prefer to display the search terms in the form of paragraphs, separated by commas, not in the form of a list like the plugin default format. How can I get results like that?
								<p>* Go to 'WordPress Admin &gt; Settings &gt; SEO searchTerms 2' menu,
									<br />* Empty columns 'Text and code After and Before the list',
									<br />* Empty columns 'Text and code before each keyword',
									<br />* And type `','` (a comma followed by a space) in the 'Text and code after each keyword',
									<br />* Save.
								</p></li>
								<li>I can't see any changes on the single post page. How do I know that this plugin works well?
								<p>This plugin will not display the list of search terms until there were visitors who come from search engines into the blog posts. Until then, no search terms will be displayed.
								</p>
								<p>So please wait until the plugin logs any visitors coming from search engines. Alternatively, you can search your own blog post from search engines to do a test.
								</p></li>
								<li>It seems the plugin won't work with my theme, what is wrong?
								<p>     Like many others SEO plugins, this plugin requires your theme to call wp_head() hook on the HTML header. Put &lt;?php wp_head(); ?&gt; code between your &lt;head&gt;...&lt;/head&gt; tag, usually on header.php file. Open the header file of WordPress Default Theme if you need any references.
								</p></li>
							</ol>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>	
<?php
}
/**
* upgrade database to support international character or v2
**/
function pk_stt2_admin_upgrade_db(){
?>
	<div class="postbox-container" style="width: 98%;">
		<div class="metabox-holder">
			<div class="meta-box-sortables ui-sortable">
				<div id="upgrade_db" class="postbox">
					<div class="handlediv" title="Click to toggle">
					</div>
					<h3 class="hndle"><span>Database Upgrade for Faster Performance and International Characters Support</span></h3>
					<div class="inside">
						<form method = "post">
						<p>This upgrade will update your current database structure to gain better performance and lower server resources. Backup of your current database will be available on <strong><?php global $wpdb; echo $wpdb->prefix.'stt2_meta_backup'; ?></strong> table.</p>

						<p>The possibility for the occurrence of error is very small, <span style="color:red"><i>but it is highly recommended to create a database backup before upgrading</i></span>. Detailed information on <a href="http://codex.wordpress.org/Backing_Up_Your_Database" target="_blank">how to backing up your WordPress database can be found here</a>.</p>
						<p class="submit">						
						  <input class="button-primary" type = "submit" name="upgrade_db_structure" value="Upgrade Now" />
						</p>						
					  </form>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php
}
/**
* donation tab
**/
function pk_stt2_admin_donate(){
?>
	<div style="text-align:center;">
	<form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_blank">
	<input name="cmd" value="_donations" type="hidden" />
	<input name="business" value="poer@exclusivewp.com" type="hidden" />
	<input name="item_name" value="Donation for SEO SearchTerms Tagging 2 Plugin" type="hidden" />
	<input name="item_number" value="wp-admin" type="hidden" />
	<select name="currency_code" value="USD">
	<option value="AUD" />AUD
	<option value="CAD" />CAD
	<option value="EUR" />EUR
	<option value="GBP" />GBP
	<option value="JPY" />JPY
	<option value="USD" selected />USD
	<option value="NZD" />NZD
	<option value="CHF" />CHF
	<option value="HKD" />HKD
	<option value="SGD" />SGD
	<option value="SEK" />SEK
	<option value="DKK" />DKK
	<option value="PLN" />PLN
	<option value="NOK" />NOK
	<option value="HUF" />HUF
	<option value="CZK" />CZK
	<option value="ILS" />ILS
	<option value="MXN" />MXN
	<option value="BRL" />BRL
	</select>
	<select name="amount" value="25">
	<option value="5"  />5
	<option value="10" />10
	<option value="15" />15
	<option value="20" />20
	<option value="25" selected />25
	<option value="50" />50
	</select>
	<input name="bn" value="PP-DonationsBF:btn_donateCC_LG.gif:NonHostedGuest" type="hidden" />
	<p>
	<input src="https://www.paypal.com/en_US/GB/i/btn/btn_donateCC_LG.gif" name="submit" alt="Donate with PayPal" style="border: medium none; background: none repeat scroll 0% 0% transparent;" border="0" type="image" />
	<img alt="" src="https://www.paypal.com/en_US/i/scr/pixel.gif" border="0" height="1" width="1" />
	</p>
	</form>
	</div>
	<?php
}
/**
* == END OF ADMIN TAB SECTION ==
**/
/**
* == PROMOTE OLD POST SECTION ==
**/
/**
* get 1 random post publish over 30 days ago but still doesn't have traffic yet, then update the publish date time into now, make it the most recent post.
**/
function pk_stt2_promote_old_post () {
	$old_post_ID = pk_stt2_db_get_id_post_wo_traffic();
	if ( !empty($old_post_ID) ) {
		pk_stt2_db_promote_single_post_wo_traffic( $old_post_ID );
		update_option('pk_stt2_last_promoted_id',$old_post_ID);
	} 
}
/**
* diplay last promoted post in admin area
**/
function pk_stt2_the_last_promoted_post() {
	$last_promoted_ID = get_option('pk_stt2_last_promoted_id');
	if ( $last_promoted_ID ) { 		
		$post_title = pk_stt2_db_get_last_promoted_post_title($last_promoted_ID);
		echo '<a href="'.get_permalink($last_promoted_ID).'" target="_blank">'.$post_title.'</a>'; 
	} else { 
		echo 'None'; 
	}
}
/**
 * display list of posts with no search engines traffic
 * */ 
function pk_stt2_admin_print_list_of_no_traffic($url=0){
	$searchterms = pk_stt2_db_get_posts_wo_traffic();
	
	if(!empty($searchterms)) {
		if ($url==0){			
			$toReturn .= "<ol>";
			foreach($searchterms as $term){		
				$permalink = get_permalink($term->ID);	
				$toReturn .= "<li><a href=\"$permalink\" target=\"_blank\">$term->post_title</a> 
				(<a href=\"post.php?post=$term->ID&action=edit\" target=\"_blank\">edit</a>)</li>";
			}
			$toReturn = trim($toReturn,', ');		
			$toReturn .= "</ol>";
		} else {
			$toReturn .= "<ul>";			
			foreach($searchterms as $term){		
				$permalink = get_permalink($term->ID);	
				$toReturn .= "<li>$permalink</li>";
			}
			$toReturn .= "</ul>";
		}
		return $toReturn;
    } else {
    	return false;
    }
}
/**
* == END OF PROMOTE POST SECTION ==
**/
/**
* == PHP 4 SECTION ==
**/
/**
* PHP 4 equivalent of PHP 5 str_ireplace function
**/
if( !function_exists('str_ireplace') ){
	function str_ireplace($search,$replace,$subject){
    $token = chr(1);
    $haystack = strtolower($subject);
    $needle = strtolower($search);
    while (($pos=strpos($haystack,$needle))!==FALSE){
      $subject = substr_replace($subject,$token,$pos,strlen($search));
      $haystack = substr_replace($haystack,$token,$pos,strlen($search));
    }
    $subject = str_replace($token,$replace,$subject);
    return $subject;
  }
}
/**
* PHP 4 equivalent of PHP 5 htmlspecialchars_decode function
**/
if ( !function_exists('htmlspecialchars_decode') ){
   function htmlspecialchars_decode($text){
       return strtr($text,array_flip(get_html_translation_table(HTML_SPECIALCHARS)));
   }
}
/**
* == END OF PHP 4 SECTION ==
**/
/**
* == SEARCH QUERY SECTION ==
**/
add_action('generate_rewrite_rules', 'pk_stt2_add_rewrite_rules');
function pk_stt2_add_rewrite_rules( $wp_rewrite ){
    $new_rules = array('^search/(.+)\$' => 'index.php?s=' .$wp_rewrite->preg_index(1));
    $wp_rewrite->rules = $new_rules + $wp_rewrite->rules;
}
// add_action('init', 'pk_stt2_flush_rewrite_rules');
function pk_stt2_flush_rewrite_rules(){
   global $wp_rewrite;
   $wp_rewrite->flush_rules();
}
add_action('parse_request', 'pk_stt2_filter_search_query');
function pk_stt2_filter_search_query(){
	global $wp;
	if (!empty($wp->query_vars['s'])){
		$wp->set_query_var('s', str_replace('-',' ',$wp->query_vars['s']));
	}	
}
/**
* == END OF SEARCH QUERY SECTION ==
**/
/**
* add widgets
**/
include_once ( 'widget.php' );
?>