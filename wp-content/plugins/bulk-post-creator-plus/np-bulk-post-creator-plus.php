<?php
/*
Plugin Name: Bulk Post Creator Plus
Plugin URI: http://ninjaplugins.com/products/bulk-post-creator-plus/
Description: Creating mass post using list of post titles. Can be used to create draft or published post or page. Also support backdate and interval posting.
Version: 0.2
Author: NinjaPlugins
Author URI: http://ninjaplugins.com
*/

/*  Copyright 2010-2012 NinjaPlugins (email: buchin@dropsugar.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/


/*
0.2 Release Notes
* Backdate support & interval posting

0.1 Release Notes 
* Add option to select publish or draft post.
*/
// Add admin menu
// Create admin form (including nonces)
// Parse results of admin form
// Create a new post for each title

class NPBulkPostCreatorPlus {
	
	static $upgrade_message = 'Please upgrade to the current version of WordPress. Not only is it necessary for this plugin to work properly, but it will also help prevent hackers from getting into your blog through old security holes.';
	static $nonce_name = 'np-bulk-post-creator-plus-create-bulk-posts';
	
	

	static public function bulk_post_add_form() {
		echo '<div class="wrap">'.PHP_EOL;
		echo '<h2>Bulk Post Creator Plus</h2>'.PHP_EOL;
		echo '	
				<div class="metabox-holder has-right-sidebar" id="poststuff">
					<div class="inner-sidebar">
						<div style="position: relative;" class="meta-box-sortabless ui-sortable" id="side-sortables">
							<div class="postbox" id="sm_pnres">
								<h3 class="hndle"><span>About this Plugin:</span></h3>
								<div class="inside">
									<ul>
									<li><a href="http://ninjaplugins.com/products/bulk-post-creator-plus/" class="sm_button sm_pluginHome">Plugin Homepage</a></li>
									</ul>
								</div>
							</div>
						</div>
					</div>
					<div class="has-sidebar sm-padded">
						<div class="has-sidebar-content" id="post-body-content">
							<div class="meta-box-sortabless">
								
								<div class="postbox">
									<div title="Click to toggle" class="handlediv"> <br></div>
									<h3 class="hndle"> <span></span></h3>
									<div class="inside">
					'.PHP_EOL;
		if ( ! empty ($_POST['bulk_post_titles']) ) {
			self::create_posts($_POST['bulk_post_titles']);
		} else {
			self::display_form();
		}
		
		echo '</div></div></div></div></div></div></div>'.PHP_EOL;
		
	}
	
	private function display_form() {
		echo '<form method="post" action="">'.PHP_EOL;
		if ( function_exists('wp_nonce_field') ) {
			wp_nonce_field('np-bulk-post-creator-plus-create-bulk-posts');
			//wp_nonce_field(self::$nonce_name);
		} else {
			die ('<p>'.self::$upgrade_message.'</p>');
		}
		
		echo '<table style="text-align: left; padding: 10px 30px;">
			<tr valign="top">
				<th scope="row">Enter your lists of titles, one on each line</th>
				<td><textarea name="bulk_post_titles" cols="60" rows="20"></textarea></td>
			</tr>
			<tr valign="top">
				<th scope="row">Post Type</th>
				<td>
					<select name="bulk_post_type">
						<option value="post">Posts</option>
						<option value="page">Pages</option>
					</select>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">Post Status</th>
				<td>
					<select name="bulk_post_status">
						<option value="draft">Draft</option>
						<option value="publish">Published</option>
					</select>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">Start Posting On</th>
				<td>
					<select name="date[year]">
						<option value="2013">2013</option>
						<option value="2012">2012</option>
						<option value="2011">2011</option>
						<option value="2010">2010</option>
						<option value="2009">2009</option>
						<option value="2008">2008</option>
					</select>
					<select name="date[month]">
						<option value="01">January</option>
						<option value="02">February</option>
						<option value="03">March</option>
						<option value="04">April</option>
						<option value="05">May</option>
						<option value="06">June</option>
						<option value="07">July</option>
						<option value="08">August</option>
						<option value="09">September</option>
						<option value="10">October</option>
						<option value="11">November</option>
						<option value="12">December</option>
					</select>
					<select name="date[day]">
						<option value="01">1</option>
						<option value="02">2</option>
						<option value="03">3</option>
						<option value="04">4</option>
						<option value="05">5</option>
						<option value="06">6</option>
						<option value="07">7</option>
						<option value="08">8</option>
						<option value="09">9</option>
						<option value="10">10</option>
						<option value="11">11</option>
						<option value="12">12</option>
						<option value="13">13</option>
						<option value="14">14</option>
						<option value="15">15</option>
						<option value="16">16</option>
						<option value="17">17</option>
						<option value="18">18</option>
						<option value="19">19</option>
						<option value="20">20</option>
						<option value="21">21</option>
						<option value="22">22</option>
						<option value="23">23</option>
						<option value="24">24</option>
						<option value="25">25</option>
						<option value="26">26</option>
						<option value="27">27</option>
						<option value="28">28</option>
						<option value="29">29</option>
						<option value="30">30</option>
						<option value="31">31</option>
					</select>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">Post Every</th>
				<td>
					<input type="text" value="1" name="interval[value]" style="width:40px;">
					<select name="interval[type]">
						<option value="days">Day</option>
						<option value="hours">Hour</option>
					</select>				
				</td>
			</tr>
			</table>'.PHP_EOL;
		
		echo '<input type="hidden" name="action" value="update" />'.PHP_EOL;
		echo '<p class="submit">
			<input type="submit" class="button-primary" value="'.__('Create Now').'" />
			</p>'.PHP_EOL;
	}
	
	private function create_posts($titles = null) {
		check_admin_referer('np-bulk-post-creator-plus-create-bulk-posts');
		//check_admin_referer(self::$nonce_name);
		if ( ! empty($titles)) :
			$titles = explode(PHP_EOL, $titles);
			echo '<ul>'.PHP_EOL;
			foreach ( $titles as $key => $title ) {
				$title = trim($title);
				if ('post' == $_POST['bulk_post_type']) {
					if ($new_draft_id = self::create_post($title, $key)) {
						echo '<li>Created <a href="post.php?action=edit&post='.$new_draft_id.'">'.$title.'</a>'.PHP_EOL;
					}
				} else {
					if ($new_draft_id = self::create_post($title, $key)) {
						echo '<li>Created <a href="page.php?action=edit&post='.$new_draft_id.'">'.$title.'</a>'.PHP_EOL;
					}
				}
			}
			echo '<ul>'.PHP_EOL;
			if ('post' == $_POST['bulk_post_type']) {
				echo '<p>All done! <a href="edit.php">See all posts &raquo;</a></p>'.PHP_EOL;
			} else {
				echo '<p>All done! <a href="edit.php?post_type=page">See all pages &raquo;</a></p>'.PHP_EOL;
			}
			
		endif;
	}
	
	private function create_post($title = null, $key) {
		$params = $_POST;
		$base_date = mktime(0, 0, 0, (int)$params['date']['month'], (int)$params['date']['day'], (int)$params['date']['year']);
		$post_interval = '+'.($params['interval']['value']*$key).' '.$params['interval']['type'];

		$post_time = strtotime($post_interval, $base_date);
		$post_time = date('Y-m-d H:i:s', $post_time);


		if ( ! empty($title)) {
			global $wpdb;
			
			$new_draft_post = array(
			  'post_content' => '',
			  'post_status' => $params['bulk_post_status'],
			  'post_title' => $title,
			  'post_type' => $params['bulk_post_type'],
			  'post_date' => $post_time,
			);
			
			if ( $new_draft_id = wp_insert_post( $new_draft_post ) ) {
				return $new_draft_id;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
	
	static public function set_plugin_meta($links, $file) {
		$plugin = plugin_basename(__FILE__);

		// create link
		if ($file == $plugin) {
			return array_merge(
				$links,
				array( sprintf( '<a href="edit.php?page=%s">%s</a>', $plugin, __('Settings') ) )
			);
			$settings_link = '<a href="options-general.php?page=custom-field-template.php">' . __('Settings') . '</a>';
			$links = array_merge( array($settings_link), $links);
		}
		return $links;
	}
	
	static public function add_plugin_menu() {
		add_posts_page( 'Bulk Post Creator Plus', 'Create Bulk Posts', 'edit_posts', 'bulk-post-creator-plus/np-bulk-post-creator-plus.php', array('NPBulkPostCreatorPlus','bulk_post_add_form'));
	}
}

$np_bulk_post_creator = new NPBulkPostCreatorPlus();

add_filter( 'plugin_row_meta', array('NPBulkPostCreatorPlus','set_plugin_meta'), 10, 2 );
add_action( 'admin_menu', array('NPBulkPostCreatorPlus','add_plugin_menu') );