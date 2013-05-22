<?php
/*
Plugin Name: Auto Ping Booster
Plugin URI: http://www.getacho.com
Description: A plugin that auto ping by <a href="http://www.getacho.com">Pro SEO Company</a>
Version: 0.1
Author: Samee Ullah Feroz
Author URI: http://www.samee.us
*/

add_action('simple_edit_form', 'ping_pps');
function ping_pps()  {
	$ping_url = "http://www.blogsearch.google.com/ping/RPC2";	//replace 'Ping URL' with provide Auto Ping Booster ping URL.
	
	echo '<script>document.post.trackback_url.defaultValue="' . $ping_url . '";</script>';
}

?>