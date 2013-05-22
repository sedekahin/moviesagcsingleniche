<?php
require_once dirname(__FILE__) . '/class.Abstract.php';

class JWBP_AdminMenuPage_Terms extends JWBP_AdminMenuPage_Abstract
{

	/**
	 * Constructor
	 */
	public function __construct()
	{
		// Menu item settings
		$this->id = 'bulkpress-terms';
		$this->parent_id = 'bulkpress';
		$this->page_title = __('BulkPress: Terms', 'bulkpress');
		$this->menu_title = __('Terms');
		$this->capability = 'manage_options';
		
		// Construct
		parent::__construct();
	}

}
?>