<?php
require_once dirname(__FILE__) . '/class.Abstract.php';

class JWBP_AdminMenuPage_BulkPress extends JWBP_AdminMenuPage_Abstract
{

	/**
	 * Constructor
	 */
	public function __construct()
	{
		// Menu item settings
		$this->id = 'bulkpress';
		$this->page_title = __('BulkPress', 'bulkpress');
		$this->menu_title = __('BulkPress');
		$this->capability = 'manage_options';
		
		// Construct
		parent::__construct();
	}
	
	/**
	 * Output the menu page contents
	 */
	public function display()
	{
		?>
		<div class="wrap">
			<h2><?php _e('BulkPress', 'bulkpress'); ?></h2>
			<p><?php _e('At this point, the plugin only supports the creation of multiple terms and posts, and re-organizing terms (changing the term hierarchy). However, we are actively developing this plugin and will be adding many new features such as creating users in bulk in the near future!', 'bulkpress'); ?></p>
		</div>
		<?php
	}

}
?>