<?php
/*
Plugin Name: BulkPress
Description: Create and manage (restructure hierarchy) vast amounts of categories, terms of custom taxonomies, posts, pages and posts of custom post types in the blink of an eye! The easy-to-use interface allows you to quickly create both hierarchical and non-hierarchical posts and terms by just speciying the title and optionally the slug, allowing you to quickly populate your website with content.
Version: 0.2.2.1
Author: Jesper van Engelen
Author URI: http://www.jepps.nl
License: GPLv2 or later
*/

// Plugin information
define('JWBP_VERSION', '0.2.2.1');

// Paths
define('JWBP_PATH', dirname(__FILE__));
define('JWBP_LIBRARY_PATH', JWBP_PATH . '/lib');
define('JWBP_URL', untrailingslashit(plugins_url('', __FILE__)));

// Library
require_once JWBP_LIBRARY_PATH . '/functions.php';
require_once JWBP_LIBRARY_PATH . '/ajax.php';

if (is_admin()) {
	require_once JWBP_LIBRARY_PATH . '/admin.php';
	require_once JWBP_LIBRARY_PATH . '/adminmenu.php';
}

// Localization
load_plugin_textdomain('bulkpress', false, dirname(plugin_basename(__FILE__)) . '/languages/');
?>