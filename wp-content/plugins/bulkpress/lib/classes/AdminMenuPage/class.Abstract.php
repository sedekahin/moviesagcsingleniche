<?php
class JWBP_AdminMenuPage_Abstract
{

	/**
	 * Menu page ID, used as menu_slug in add_menu_page or add_submenu_page
	 *
	 * @var string
	 */
	protected $id;
	
	/**
	 * Parent menu page ID, used as parent_slug in add_submenu_page if supplied
	 *
	 * @var string
	 */
	protected $parent_id;
	
	/**
	 * Menu page title
	 *
	 * @var string
	 */
	protected $page_title;
	
	/**
	 * Menu item title
	 *
	 * @var string
	 */
	protected $menu_title;
	
	/**
	 * Capability required to access the page
	 *
	 * @var string
	 */
	protected $capability;
	
	/**
	 * Callback function for displaying the page content
	 *
	 * @var string|array
	 */
	protected $callback;
	
	/**
	 * Menu hook, set when registering the menu
	 *
	 * @var string
	 */
	protected $hookname;
	
	/**
	 * Callback function for handling the logic of the page
	 *
	 * @var string|array
	 */
	protected $handle_callback;
	
	/**
	 * List of tab objects that are used for this page
	 *
	 * @var array
	 */
	protected $tabs = array();
	
	/**
	 * Current tab ID
	 *
	 * @var string
	 */
	protected $current_tab;
	
	/**
	 * Message to display as admin notices
	 *
	 * @var array
	 */
	public $messages = array();
	
	/**
	 * Constructor
	 */
	public function __construct()
	{
		// Actions
		add_action('admin_menu', array(&$this, 'register_menu'));
	}
	
	/************************
	 * Main functionality
	 ***********************/
	
	/**
	 * Register the menu to WordPress
	 */
	public function register_menu()
	{
		$callback = $this->get_callback();
		
		// Add menu item
		if ($this->parent_id) {
			$this->hookname = add_submenu_page($this->parent_id, $this->page_title, $this->menu_title, $this->capability, $this->id, $callback);
		}
		else {
			$this->hookname = add_menu_page($this->page_title, $this->menu_title, $this->capability, $this->id, $callback);
		}
		
		// Hook into loading of this page for handling the logic of this page
		add_action('load-' . $this->hookname, $this->get_handle_callback());
		add_action('load-' . $this->hookname, array($this, 'handle_current_tab'));
	}
	
	/**
	 * Handle logic of the page
	 */
	public function handle() {}
	
	/**
	 * Check current tab and store its id
	 */
	public function handle_current_tab()
	{
		if (!$this->is_tabbed()) {
			return;
		}
		
		$this->current_tab = false;
		
		// Get current tab
		if (isset($_GET['tab'])) {
			$tab = $this->get_tab($_GET['tab']);
			
			if ($tab !== false) {
				$this->current_tab = $tab->get_id();
			}
		}
		
		if ($this->current_tab === false) {
			// Use first tab as default current tab
			reset($this->tabs);
			
			$this->current_tab = current($this->tabs)->get_id();
		}
		
		// Handle tab logic
		call_user_func($this->get_tab($this->get_current_tab())->get_handle_callback());
	}
	
	/**
	 * Output the page contents
	 */
	public function display()
	{
		?>
		<h1><?php echo $this->page_title; ?></h1>
		<?php $this->nav_tabs(); ?>
		<div class="wrap">
			<?php call_user_func($this->get_tab($this->get_current_tab())->get_callback()); ?>
		</div>
		<?php
	}
	
	/**
	 * Output the tab nagiation
	 */
	public function nav_tabs()
	{
		if ($this->is_tabbed()) {
			$current_tab = $this->get_current_tab();
			?>
			<h2 class="nav-tab-wrapper">
				<?php foreach ($this->tabs as $index => $tab) : ?>
					<a href="<?php echo add_query_arg('tab', $tab->get_id()); ?>" class="nav-tab<?php if ($current_tab == $tab->get_id()) echo ' nav-tab-active'; ?>"><?php echo $tab->get_title(); ?></a>
				<?php endforeach; ?>
			</h2>
			<?php
		}
	}
	
	/************************
	 * Messages
	 ***********************/
	
	/**
	 * Add a message to display as an admin notice
	 *
	 * @param string $message Message content
	 * @param string $type Optional. Message type, either "updated" (default) or "error", used as CSS class for the message element
	 */
	public function add_message($message, $type = 'updated')
	{
		$this->messages[] = array(
			'content' => $message,
			'type' => $type
		);
	}

	/**
	 * Display messages
	 */
	public function display_messages()
	{
		foreach ($this->messages as $index => $message) {
			?>
			<div class="<?php echo esc_attr($message['type']); ?>"><?php echo wpautop($message['content']); ?></div>
			<?php
		}
	}
	
	/************************
	 * Tabs
	 ***********************/
	
	/**
	 * Add a tab to this menu page
	 *
	 * @param JWBP_AdminMenuPageTab_Abstract $tab Tab object
	 */
	public function add_tab(JWBP_AdminMenuPageTab_Abstract $tab)
	{
		if (!$tab->get_id() || !$tab->get_title()) {
			return;
		}
		
		$this->tabs[$tab->get_id()] = $tab;
	}
	
	/************************
	 * Menu information
	 ***********************/
	
	/**
	 * Get the ID for this menu page
	 *
	 * @return string Menu page ID
	 */
	public function get_id()
	{
		return $this->id;
	}
	
	/**
	 * Get the current tab id
	 * Only call this after the current tab has been set
	 *
	 * @return string|bool Current tab ID on success, false on failure
	 */
	public function get_current_tab()
	{
		return $this->current_tab ? $this->current_tab : false;
	}
	
	/**
	 * Get a tab by its id
	 *
	 * @param string $tabid Tab ID of tab to fetch
	 * @return JWBP_AdminMenuPageTab_Abstract|bool Returns the tab object on success, false otherwise
	 */
	public function get_tab($tabid)
	{
		return isset($this->tabs[$tabid]) ? $this->tabs[$tabid] : false;
	}
	
	/**
	 * Get the callback to use for displaying this menu item
	 *
	 * @return string|array Callback used
	 */
	public function get_callback()
	{
		return $this->callback ? $this->callback : array($this, 'display');
	}
	
	/**
	 * Get the callback to use for handling the logic of this menu item
	 *
	 * @return string|array Callback used
	 */
	public function get_handle_callback()
	{
		return $this->handle_callback ? $this->handle_callback : array($this, 'handle');
	}
	
	/**
	 * Get whether the current menu page has tabs enabled
	 *
	 * @return bool Returns true when tabs are enabled, false otherwise
	 */
	public function is_tabbed()
	{
		return (is_array($this->tabs) && !empty($this->tabs)) ? true : false;
	}

}
?>