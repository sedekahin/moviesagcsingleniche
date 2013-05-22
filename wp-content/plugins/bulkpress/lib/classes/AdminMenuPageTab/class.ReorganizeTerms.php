<?php
require_once dirname(__FILE__) . '/class.Abstract.php';

class JWBP_AdminMenuPageTab_ReorganizeTerms extends JWBP_AdminMenuPageTab_Abstract
{

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->id = 'terms-reorganize';
		$this->title = __('Reorganize Terms', 'bulkpress');
		
		// Construct
		parent::__construct();
	}
	
	/**
	 * Handle tab logic
	 */
	public function handle()
	{
		// Actions
		add_action('admin_enqueue_scripts', array(&$this, 'action_admin_enqueue_scripts'));
		
		// Taxonomies
		$taxonomies = get_taxonomies(array(
			'hierarchical' => true
		), 'objects');
		$taxonomy_current = (isset($_GET['taxonomy'])  && isset($taxonomies[$_GET['taxonomy']])) ? $taxonomies[$_GET['taxonomy']] : current($taxonomies);
		
		// Terms hierarchy
		if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST) && wp_verify_nonce($_POST['jwbp-terms-reorganize-nonce'], 'jwbp-terms-reorganize')) {
			$terms_parents = $_POST['jwbp-term-parent'];
			
			foreach ($terms_parents as $index => $term_parent) {
				wp_update_term($index, $taxonomy_current->name, array(
					'parent' => $term_parent
				));
			}
			
			// Remove hierarchy from cache
			delete_option($taxonomy_current->name . '_children');
			
			// Add message to notify user of completion
			$this->add_message(sprintf(__('%s hierarchy successfully updated.', 'bulkpress'), $taxonomy_current->labels->singular_name));
		}
	}
	
	/**
	 * Action: admin_enqueue_scripts
	 */
	public function action_admin_enqueue_scripts()
	{
		// Scripts
		wp_register_script('jwbp-admin-terms-reorganize', JWBP_URL . '/public/js/admin-terms-reorganize.js', array('jquery'));
		wp_enqueue_script('jwbp-admin-terms-reorganize');
	}
	
	/**
	 * Output tab contents
	 */
	public function display()
	{
		require_once JWBP_LIBRARY_PATH . '/classes/Walker/class.TermsHierarchy.php';
		
		// Taxonomies
		$taxonomies = get_taxonomies(array(
			'hierarchical' => true
		), 'objects');
		$taxonomy_current = (isset($_GET['taxonomy'])  && isset($taxonomies[$_GET['taxonomy']])) ? $taxonomies[$_GET['taxonomy']] : current($taxonomies);
		
		// List display types
		$listdisplaytypes = array(
			'comfortable' => __('Comfortable', 'bulkpress'),
			'cozy' => __('Cozy', 'bulkpress'),
			'compact' => __('Compact', 'bulkpress')
		);
		$listdisplaytype_current = (isset($_GET['listdisplaytype'])  && isset($listdisplaytypes[$_GET['listdisplaytype']])) ? $_GET['listdisplaytype'] : key($listdisplaytypes);
		?>
		
		<div class="wrap">
			<h2><?php echo $this->get_title(); ?></h2>
			<div class="updated" id="jwbp-notice-unsavedchanges"><p><?php _e('<strong>Note:</strong> You have unsaved changes. Click the &quot;Save changes&quot; button below to finalize your changes.', 'bulkpress'); ?></p></div>
			<ul class="jwbp-listdisplay">
				<?php foreach ($listdisplaytypes as $index => $listdisplaytype) : ?>
					<li<?php if ($index == $listdisplaytype_current) echo ' class="current"'; ?>>
						<a href="#" title="<?php echo esc_attr($listdisplaytype); ?>" id="jwbp-listdisplaytype-<?php echo $index; ?>"><?php echo $listdisplaytype; ?></a>
					</li>
					<?php $i++; ?>
				<?php endforeach; ?>
			</ul>
			<ul class="subsubsub">
				<?php $i = 0; ?>
				<?php foreach ($taxonomies as $index => $taxonomy) : ?>
					<li>
						<?php if ($i) echo ' | '; ?>
						<a href="<?php echo add_query_arg('taxonomy', $taxonomy->name); ?>" title="<?php echo esc_attr($taxonomy->labels->singular_name); ?>"<?php if ($taxonomy->name == $taxonomy_current->name) echo ' class="current"'; ?>><?php echo $taxonomy->labels->singular_name; ?></a>
					</li>
					<?php $i++; ?>
				<?php endforeach; ?>
			</ul>
			<div class="jwbp-clear"></div>
			<form action="" method="post" id="jwbp-termshierarchy-form">
				<?php wp_nonce_field('jwbp-terms-reorganize', 'jwbp-terms-reorganize-nonce'); ?>
				<p class="jwbp-submit-top">
					<?php submit_button(false, 'primary', 'jwbp-submit', false); ?>
				</p>
				<ul class="jwbp-termshierarchy jwbp-display-<?php echo $listdisplaytype_current; ?>">
					<?php wp_list_categories(array(
						'taxonomy' => $taxonomy_current->name,
						'show_option_none' => '',
						'show_option_all' => '',
						'hierarchical' => true,
						'hide_empty' => false,
						'title_li' => '',
						'walker' => new JWBP_Walker_TermsHierarchy()
					)); ?>
				</ul>
				<p class="jwbp-submit-bottom">
					<?php submit_button(false, 'primary', 'jwbp-submit', false); ?>
				</p>
			</form>
		</div>
		<?php
	}

}
?>