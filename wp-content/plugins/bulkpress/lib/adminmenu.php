<?php
class JWBP_AdminMenu
{

	/**
	 * Initialize
	 * Mainly used for registering action and filter hooks
	 */
	public function init()
	{
		// Pages
		require_once JWBP_LIBRARY_PATH . '/classes/AdminMenuPage/class.BulkPress.php';
		require_once JWBP_LIBRARY_PATH . '/classes/AdminMenuPage/class.Terms.php';
		require_once JWBP_LIBRARY_PATH . '/classes/AdminMenuPage/class.Posts.php';
		
		// Tabs
		require_once JWBP_LIBRARY_PATH . '/classes/AdminMenuPageTab/class.AddTerms.php';
		require_once JWBP_LIBRARY_PATH . '/classes/AdminMenuPageTab/class.ReorganizeTerms.php';
		require_once JWBP_LIBRARY_PATH . '/classes/AdminMenuPageTab/class.AddPosts.php';
		
		// Actions
		add_action('plugins_loaded', array('JWBP_AdminMenu', 'menu'));
	}
	
	/**
	 * Add menu pages and tabs
	 */
	public function menu()
	{
		$page = new JWBP_AdminMenuPage_BulkPress();
		
		$page = new JWBP_AdminMenuPage_Terms();
		$page->add_tab(new JWBP_AdminMenuPageTab_AddTerms());
		$page->add_tab(new JWBP_AdminMenuPageTab_ReorganizeTerms());
		
		$page = new JWBP_AdminMenuPage_Posts();
		$page->add_tab(new JWBP_AdminMenuPageTab_AddPosts());
	}

}

JWBP_AdminMenu::init();
?>