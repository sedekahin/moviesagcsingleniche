<?php
class JWBP_Admin
{

	/**
	 * Initialize
	 * Mainly used for registering action and filter hooks
	 */
	public static function init()
	{
		// Actions
		add_action('admin_enqueue_scripts', array('JWBP_Admin', 'enqueue_scripts'));
	}
	
	/**
	 * Register and enqueue scripts
	 */
	public static function enqueue_scripts()
	{
		// Scripts
		wp_register_script('jwbp-admin-terms-add', JWBP_URL . '/public/js/admin-terms-add.js', array('jquery'));
		wp_register_script('jquery-nestedsortable', JWBP_URL . '/external/nestedsortable/jquery-nestedsortable.js', array('jquery', 'jquery-ui-core', 'jquery-ui-sortable', 'jquery-ui-widget', 'jquery-ui-mouse'));
		wp_register_script('jwbp-admin-terms-reorganize', JWBP_URL . '/public/js/admin-terms-reorganize.js', array('jquery', 'jquery-nestedsortable'));
		
		wp_register_script('jquery-linedtextarea', JWBP_URL . '/external/linedtextarea/jquery-linedtextarea.js', array('jquery'));
		wp_register_script('jwbp-admin', JWBP_URL . '/public/js/admin.js', array('jquery', 'jquery-linedtextarea'));
		wp_enqueue_script('jquery');
		wp_enqueue_script('jquery-linedtextarea');
		wp_enqueue_script('jwbp-admin');
		
		// Styles
		wp_register_style('jwbp-admin', JWBP_URL . '/public/css/admin.css');
		wp_register_style('jquery-linedtextarea', JWBP_URL . '/external/linedtextarea/jquery-linedtextarea.css');
		wp_enqueue_style('jwbp-admin');
		wp_enqueue_style('jquery-linedtextarea');
		
		// AJAX
		wp_localize_script('jwbp-admin', 'JWBP_Ajax', array(
			'ajaxurl' => admin_url('admin-ajax.php')
		));
	}

}

JWBP_Admin::init();
?>