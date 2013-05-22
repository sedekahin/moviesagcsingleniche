<?php
class JWBP_AdminMenuPageTab_Abstract
{

	/**
	 * Tab ID, used as unique identification for a tab on a single menu page
	 *
	 * @var string
	 */
	protected $id;
	
	/**
	 * Tab title, used in the tab navigation as the link text
	 *
	 * @var string
	 */
	protected $title;
	
	/**
	 * Callback function for displaying the tab content
	 *
	 * @var string|array
	 */
	protected $callback;
	
	/**
	 * Callback function for handling the logic of the tab
	 *
	 * @var string|array
	 */
	protected $handle_callback;
	
	/**
	 * Message to display as admin notices
	 *
	 * @var array
	 */
	public $messages = array();
	
	/************************
	 * Main functionality
	 ***********************/
	
	/**
	 * Constructor
	 */
	public function __construct()
	{
		// Actions
		add_action('admin_notices', array(&$this, 'display_messages'));
	}
	
	/**
	 * Handle logic of the tab
	 */
	public function handle() {}
	
	/**
	 * Output tab contents
	 */
	public function display() {}
	
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
	 * Tab information
	 ***********************/
	
	/**
	 * Get the ID for this tab
	 *
	 * @return string Tab ID
	 */
	public function get_id()
	{
		return $this->id;
	}
	
	/**
	 * Get the title for this tab
	 *
	 * @return string Tab title
	 */
	public function get_title()
	{
		return $this->title;
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

}
?>