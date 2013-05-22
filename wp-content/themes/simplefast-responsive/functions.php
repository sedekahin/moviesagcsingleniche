<?php
define( 'FASTESTWP_BASE_DIR', TEMPLATEPATH . '/' );
define( 'FASTESTWP_BASE_URL', get_template_directory_uri() . '/' );
if( !isset( $content_width ) )
	$content_width = 960;
if( !function_exists('fastestwp_theme_setup') ) {
	function fastestwp_theme_setup() {
		add_theme_support('post-thumbnails');
        register_nav_menus( array( 'main-menu' => __( 'Main Navigation' ) ) );
	}
}
add_action( 'after_setup_theme', 'fastestwp_theme_setup' );
include( FASTESTWP_BASE_DIR . 'functions/option.php' );
include( FASTESTWP_BASE_DIR . 'functions/add-option.php' );
include( FASTESTWP_BASE_DIR . 'functions/pagination.php' );
include( FASTESTWP_BASE_DIR . 'functions/fastestwp.php' );
?>