<?php
require_once dirname(__FILE__) . '/class.Abstract.php';

class JWBP_AdminMenuPageTab_AddTerms extends JWBP_AdminMenuPageTab_Abstract
{

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->id = 'terms-add';
		$this->title = __('Add Terms', 'bulkpress');
		
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
		
		// Add terms
		if ($_SERVER['REQUEST_METHOD'] != 'POST' || empty($_POST) || !wp_verify_nonce($_POST['jwbp-terms-add-nonce'], 'jwbp-terms-add')) {
			return;
		}
		
		// Get taxonomy to add terms for
		$taxonomy = false;
		
		if (isset($_POST['jwbp-addterms-taxonomy']) && $_POST['jwbp-addterms-taxonomy']) {
			$taxonomy = get_taxonomy($_POST['jwbp-addterms-taxonomy']);
		}
		
		if (!is_object($taxonomy)) {
			$this->add_message(__('You have not specified a valid taxonomy.', 'bulkpress'), 'error');
			
			$this->status = 'addterms_error';
			
			return;
		}
		
		// Whether to create parent terms that do not exist yet
		$create_inexistent_parents = (isset($_POST['jwbp-addterms-create-inexistent-parents']) && $_POST['jwbp-addterms-create-inexistent-parents']) ? true : false;
		
		// Existing terms
		$taxonomy_terms = get_terms($taxonomy->name, array(
			'hide_empty' => false
		));
		
		// Terms data from form
		$terms_titles = explode("\n", $_POST['jwbp-addterms-terms-titles']);
		$terms_slugs = explode("\n", $_POST['jwbp-addterms-terms-slugs']);
		
		// Terms to add, organized via "term sets": each term set corresponds with a line from the textbox from the form, representing a hierarchy
		$termsets = array();
		
		// Hold term counts for term sets to change the order in which term sets are added later, so that top level terms get created before higher depth terms
		// Would probably not be necessary when "create inexistent parents" is checked
		$termsets_termcounts = array();
		
		// Loop through all terms and add them to the queue
		foreach ($terms_titles as $index => $title) {
			// Get and sanitize title and slug
			$title = trim($title);
			
			if (!$title) {
				continue;
			}
			
			$title = stripslashes($title);
			$slug = isset($terms_slugs[$index]) ? trim($terms_slugs[$index]) : false;
			
			// Get hierarchy
			$delimiter = '/';
			$parts = preg_split('~(?<!\\\)' . preg_quote($delimiter, '~') . '~', $title);
			
			foreach ($parts as $index2 => $part) {
				$parts[$index2] = stripslashes($part);
			}
			
			// For non-hierarchical taxonomies we only use the "last" term
			if (!$taxonomy->hierarchical) {
				$parts = array($parts[count($parts) - 1]);
			}
			
			// Actually add the term set to the queue
			$termsets[$index] = array(
				'terms' => $parts,
				'slug' => $slug,
				'num_terms' => count($parts)
			);
			
			$termsets_termcounts[$index] = $termsets[$index]['num_terms'];
		}
		
		// Sort term sets based on total depth
		array_multisort($termsets_termcounts, SORT_ASC, SORT_NUMERIC, $termsets);
		
		// Main parent
		$parent_default = $taxonomy->hierarchical ? max(0, intval($_POST['jwbp-addterms-topparent'])) : 0;
		
		// Counters
		$num_errors = 0;
		$num_terms_inserted = 0;
		
		// Add the terms from the queue
		foreach ($termsets as $index => $termset) {
			$parent = $parent_default;
			
			// Loop through term hierarchy of this set
			foreach ($termset['terms'] as $index => $term) {
				$term_exists = false;
				
				// Check if the term already exists
				foreach ($taxonomy_terms as $index2 => $taxonomy_term) {
					if ($taxonomy_term->name == $term && (!$taxonomy->hierarchical || $taxonomy_term->parent == $parent)) {
						$term_exists = true;
						
						// Change the current parent as we go further into the hierarchy
						$parent = $taxonomy_term->term_id;
						
						break;
					}
				}
				
				// Add the term if it doesn't exist
				if (!$term_exists) {
					// If we shouldn't create inexistent parent terms and we are not at the last term yet, don't insert the term and stop adding terms for this set
					if ($index < $termset['num_terms'] - 1 && !$create_inexistent_parents) {
						break;
					}
					else {
						$args = array();
						
						// Use custom slug for the last term if set
						if ($index == $termset['num_terms'] - 1 && $termset['slug']) {
							$args['slug'] = $termset['slug'];
						}
						
						// Set parent for hierarchical taxonomies
						if ($taxonomy->hierarchical) {
							$args['parent'] = $parent;
						}
						
						// Actually add term
						$inserted_term = wp_insert_term($term, $taxonomy->name, $args);
						
						if (is_wp_error($inserted_term)) {
							$num_errors++;
							
							$this->add_message(sprintf(__('Error adding term &quot;%s&quot;.', 'bulkpress'), esc_html(implode('/', $termset['terms']))), 'error');
							
							break;
						}
						else {
							$num_terms_inserted++;
							
							// Add term to list of existing terms
							$taxonomy_terms[] = get_term($inserted_term['term_id'], $taxonomy->name);
							
							// Change parent for when we are creating a parent term (if we are not at the last term in the term set yet)
							$parent = $inserted_term['term_id'];
						}
					}
				}
			}
		}
		
		// Remove hierarchy from cache
		delete_option($taxonomy->name . '_children');
		
		// Add message to notify user of completion
		$this->add_message(sprintf(_n('%d term successfully added.', '%d terms successfully added.', $num_terms_inserted, 'bulkpress'), $num_terms_inserted));
		
		// Change page status
		$this->status = 'addterms_success';
	}
	
	/**
	 * Action: admin_enqueue_scripts
	 */
	public function action_admin_enqueue_scripts()
	{
		// Scripts
		wp_register_script('jwbp-admin-terms-add', JWBP_URL . '/public/js/admin-terms-add.js', array('jquery'));
		wp_enqueue_script('jwbp-admin-terms-add');
	}
	
	/**
	 * Output tab contents
	 */
	public function display()
	{
		$taxonomies = get_taxonomies(array(), 'objects');
		$taxonomy_current = (isset($_POST['jwbp-addterms-taxonomy'])  && isset($taxonomies[$_POST['jwbp-addterms-taxonomy']])) ? $taxonomies[$_POST['jwbp-addterms-taxonomy']] : current($taxonomies);
		
		if ($this->status == 'addterms_error') {
			$terms_titles_current = stripslashes($_POST['jwbp-addterms-terms-titles']);
			$terms_slugs_current = stripslashes($_POST['jwbp-addterms-terms-slugs']);
		}
		?>
		<div class="wrap">
			<h2><?php echo $this->get_title(); ?></h2>
			
			<form action="" method="post">
				<?php wp_nonce_field('jwbp-terms-add', 'jwbp-terms-add-nonce'); ?>
				<table class="form-table">
					<tbody>
						<tr>
							<th><label for="jwbp-addterms-taxonomy"><?php _e('Taxonomy', 'bulkpress'); ?></label></th>
							<td>
								<select name="jwbp-addterms-taxonomy" id="jwbp-addterms-taxonomy">
									<?php foreach ($taxonomies as $index => $taxonomy) : ?>
										<option value="<?php echo esc_attr($index); ?>" <?php selected($taxonomy_current->name, $taxonomy->name); ?>><?php echo $taxonomy->labels->singular_name; ?></option>
									<?php endforeach; ?>
								</select>
								<img src="<?php echo esc_url(admin_url('images/wpspin_light.gif')); ?>" class="ajax-loading" alt="" />
							</td>
						</tr>
						<tr class="jwbp-addterms-terms">
							<th><label for="jwbp-addterms-terms-titles"><?php _e('Terms', 'bulkpress'); ?></label></th>
							<td>
								<div id="jwbp-addterms-terms-titles-container">
									<textarea name="jwbp-addterms-terms-titles" id="jwbp-addterms-terms-titles" class="jwbp-lined"><?php echo esc_html($terms_titles_current); ?></textarea>
									<div class="jwbp-clear"></div>
									<div class="description">
										<h3><?php _e('Terms', 'bulkpress'); ?></h3>
										<p><?php _e('Enter the terms you want to add in the left textbox, separating different terms by newlines. You can assign categories to parent terms by entering the full term path, separating different term names by slashes (<code>/</code>).', 'bulkpress'); ?></p>
										<p><?php _e('If you need a slash in your actual term name, you can escape it by prepending it with a backslash. For example, &quot;Audio/Video&quot; will add a term &quot;Audio&quot; with a child term &quot;Video&quot;, whilst &quot;Audio\/Video&quot; will add a term &quot;Audio/Video&quot;.', 'bulkpress'); ?></p>
									</div>
								</div>
								<div id="jwbp-addterms-terms-slugs-container">
									<textarea name="jwbp-addterms-terms-slugs" id="jwbp-addterms-terms-slugs" class="jwbp-lined"><?php echo esc_html($terms_slugs_current); ?></textarea>
									<div class="jwbp-clear"></div>
									<div class="description">
										<h3><?php _e('Slugs', 'bulkpress'); ?></h3>
										<p><?php _e('Slugs will be automatically generated if they are not manually set, but it is also possible to enter specific slugs for each term. You can do this by putting the slugs in the right textbox on the lines of that textbox corresponding with the lines in the left textbox (for the term paths). Please note that the slug entered is used for the last term in the hierarchy, separating slugs with slashes will not have the effect of adding slugs for multiple terms.', 'bulkpress'); ?></p>
									</div>
								</div>
								<div class="description jwbp-filter-hierarchical-0<?php if ($taxonomy_current->hierarchical) echo ' jwbp-hidden'; ?>">
									<?php _e('<strong>Note:</strong> This taxonomy is non-hierarchical, meaning there can be no parent-child relationship between terms. Term hierarchy indicated by the slash (<code>/</code>) symbol will be ignored. This means, for example, that &quot;Cities/Leiden&quot; will ignore &quot;Cities&quot; and only add &quot;Leiden&quot;.', 'bulkpress'); ?>
								</div>
								<div class="description jwbp-example">
									<?php printf(__('You can add example data to the text boxes by %sclicking here%s. The example data will be prepended to the current data.', 'bulkpress'), '<a href="#" class="jwbp-addterms-fill-example">', '</a>'); ?>
								</div>
							</td>
						</tr>
						<tr class="jwbp-filter-hierarchical-1<?php if (!$taxonomy_current->hierarchical) echo ' jwbp-hidden'; ?>">
							<th><label for="jwbp-addterms-topparent"><?php _e('Add to parent', 'bulkpress'); ?></label></th>
							<td>
								<?php if ($taxonomy_current->hierarchical) : ?>
									<?php wp_dropdown_categories(array(
										'show_option_none' => __('No parent', 'bulkpress'),
										'orderby' => 'name',
										'order' => 'ASC',
										'hide_empty' => false,
										'taxonomy' => $taxonomy_current->name,
										'name' => 'jwbp-addterms-topparent',
										'id' => 'jwbp-addterms-topparent',
										'selected' => $_POST['jwbp-addterms-topparent'],
										'hierarchical' => $taxonomy_current->hierarchical
									)); ?>
								<?php else : ?>
									<div id="jwbp-addposts-topparent"></div>
								<?php endif; ?>
							</td>
						</tr>
						<tr class="jwbp-filter-hierarchical-1<?php if (!$taxonomy_current->hierarchical) echo ' jwbp-hidden'; ?>">
							<th><label for="jwbp-addterms-create-inexistent-parents"><?php _e('Create inexistent parent terms', 'bulkpress'); ?></label></th>
							<td>
								<input type="checkbox" name="jwbp-addterms-create-inexistent-parents" id="jwbp-addterms-create-inexistent-parents" value="1" <?php checked($_POST['jwbp-addterms-create-inexistent-parents']); ?> />
								<span class="description">
									<?php _e('When specifying term parents by using the slash (<code>/</code>) symbol, you can use this option to create parent terms that do not exist yet.', 'bulkpress'); ?>
								</span>
								<p class="description">
									<?php _e('For example, if you enter <code>Cities/Leiden</code> and the term &quot;Cities&quot; does not exist yet, leaving the box unchecked will skip the term (thus adding neither &quot;Cities&quot; nor &quot;Leiden&quot;) whilst checking the box will create both &quot;Cities&quot; and &quot;Leiden&quot;.', 'bulkpress'); ?>
								</p>
							</td>
						</tr>
					</tbody>
				</table>
				<?php submit_button(__('Add terms', 'bulkpress')); ?>
			</form>
		</div>
		<?php
	}

}
?>