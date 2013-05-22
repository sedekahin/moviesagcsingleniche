<?php
require_once dirname(__FILE__) . '/class.Abstract.php';

class JWBP_AdminMenuPageTab_AddPosts extends JWBP_AdminMenuPageTab_Abstract
{

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$this->id = 'posts-add';
		$this->title = __('Add Posts', 'bulkpress');
		
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
		
		// Add posts
		if ($_SERVER['REQUEST_METHOD'] != 'POST' || empty($_POST) || !wp_verify_nonce($_POST['jwbp-posts-add-nonce'], 'jwbp-posts-add')) {
			return;
		}
		
		// Get posttype to add posts for
		$posttype = false;
		
		if (isset($_POST['jwbp-addposts-posttype']) && $_POST['jwbp-addposts-posttype']) {
			$posttype = get_post_type_object($_POST['jwbp-addposts-posttype']);
		}
		
		if (!is_object($posttype)) {
			$this->messages[] = array(
				'type' => 'error',
				'content' => __('You have not specified a valid post type.', 'bulkpress')
			);
			
			$this->status = 'addposts_error';
			
			return;
		}
		
		// Whether to create parent posts that do not exist yet
		$create_inexistent_parents = (isset($_POST['jwbp-addposts-create-inexistent-parents']) && $_POST['jwbp-addposts-create-inexistent-parents']) ? true : false;
		
		// Existing posts
		$posttype_posts_query = new WP_Query(array(
			'post_type' => $posttype->name,
			'posts_per_page' => -1,
			'post_status' => 'any'
		));
		
		$posttype_posts = $posttype_posts_query->posts;
		
		// Terms data from form
		$posts_titles = explode("\n", $_POST['jwbp-addposts-posts-titles']);
		$posts_slugs = explode("\n", $_POST['jwbp-addposts-posts-slugs']);
		
		// Terms to add, organized via "post sets": each post set corresponds with a line from the textbox from the form, representing a hierarchy
		$postsets = array();
		
		// Hold post counts for post sets to change the order in which post sets are added later, so that top level posts get created before higher depth posts
		// Would probably not be necessary when "create inexistent parents" is checked
		$postsets_postcounts = array();
		
		// Loop through all posts and add them to the queue
		foreach ($posts_titles as $index => $title) {
			// Get and sanitize title and slug
			$title = trim($title);
			
			if (!$title) {
				continue;
			}
			
			$title = stripslashes($title);
			$slug = isset($posts_slugs[$index]) ? trim($posts_slugs[$index]) : false;
			
			// Get hierarchy
			$delimiter = '/';
			$parts = preg_split('~(?<!\\\)' . preg_quote($delimiter, '~') . '~', $title);
			
			foreach ($parts as $index2 => $part) {
				$parts[$index2] = stripslashes($part);
			}
			
			// For non-hierarchical posttypes we only use the "last" post
			if (!$posttype->hierarchical) {
				$parts = array($parts[count($parts) - 1]);
			}
			
			// Actually add the post set to the queue
			$postsets[$index] = array(
				'posts' => $parts,
				'slug' => $slug,
				'num_posts' => count($parts)
			);
			
			$postsets_postcounts[$index] = $postsets[$index]['num_posts'];
		}
		
		// Sort post sets based on total depth
		array_multisort($postsets_postcounts, SORT_ASC, SORT_NUMERIC, $postsets);
		
		// Main parent
		$parent_default = max(0, intval($_POST['jwbp-addposts-topparent']));
		
		// Counters
		$num_errors = 0;
		$num_posts_inserted = 0;
		
		// Add the posts from the queue
		foreach ($postsets as $index => $postset) {
			$parent = $parent_default;
			
			// Loop through post hierarchy of this set
			foreach ($postset['posts'] as $index => $post) {
				$post_exists = false;
				
				// Check if the post already exists
				foreach ($posttype_posts as $index2 => $posttype_post) {
					if ($posttype_post->post_title == $post && (!$posttype->hierarchical || $posttype_post->post_parent == $parent)) {
						$post_exists = true;
						
						// Change the current parent as we go further into the hierarchy
						$parent = $posttype_post->ID;
						
						break;
					}
				}
				
				// Add the post if it doesn't exist
				if (!$post_exists) {
					// If we shouldn't create inexistent parent posts and we are not at the last post yet, don't insert the post and stop adding posts for this set
					if ($index < $postset['num_posts'] - 1 && !$create_inexistent_parents) {
						break;
					}
					else {
						$args = array(
							'post_type' => $posttype->name,
							'post_title' => $post,
							'post_status' => $_POST['jwbp-addposts-poststatus']
						);
						
						// Use custom slug for the last post if set
						if ($index == $postset['num_posts'] - 1 && $postset['slug']) {
							$args['post_name'] = $postset['slug'];
						}
						
						// Set parent for hierarchical posttypes
						if ($posttype->hierarchical) {
							$args['post_parent'] = $parent;
						}
						
						// Actually add post
						$inserted_post_id = wp_insert_post($args);
						
						if (is_wp_error($inserted_post_id)) {
							$num_errors++;
							
							$this->messages[] = array(
								'type' => 'error',
								'content' => sprintf(__('Error adding post &quot;%s&quot;.', 'bulkpress'), esc_html(implode('/', $postset['posts'])))
							);
							
							break;
						}
						else {
							$num_posts_inserted++;
							
							// Add post to list of existing posts
							$posttype_posts[] = get_post($inserted_post_id);
							
							// Change parent for when we are creating a parent post (if we are not at the last post in the post set yet)
							$parent = $inserted_post_id;
						}
					}
				}
			}
		}
		
		// Add message to notify user of completion
		$this->add_message(sprintf(_n('%d post successfully added.', '%d posts successfully added.', $num_posts_inserted, 'bulkpress'), $num_posts_inserted));
		
		// Change page status
		$this->status = 'addposts_success';
	}
	
	/**
	 * Action: admin_enqueue_scripts
	 */
	public function action_admin_enqueue_scripts()
	{
		// Scripts
		wp_register_script('jwbp-admin-posts-add', JWBP_URL . '/public/js/admin-posts-add.js', array('jquery'));
		wp_enqueue_script('jwbp-admin-posts-add');
	}
	
	/**
	 * Output tab contents
	 */
	public function display()
	{
		$posttypes = get_post_types(array(), 'objects');
		$posttype_current = (isset($_POST['jwbp-addposts-posttype'])  && isset($posttypes[$_POST['jwbp-addposts-posttype']])) ? $posttypes[$_POST['jwbp-addposts-posttype']] : current($posttypes);
		
		$poststati = get_post_stati(array(), 'objects');
		$poststatus_current = (isset($_POST['jwbp-addposts-poststatus'])  && isset($poststati[$_POST['jwbp-addposts-poststatus']])) ? $poststati[$_POST['jwbp-addposts-poststatus']] : current($poststati);
		
		if ($this->status == 'addposts_error') {
			$posts_titles_current = stripslashes($_POST['jwbp-addposts-posts-titles']);
			$posts_slugs_current = stripslashes($_POST['jwbp-addposts-posts-slugs']);
		}
		?>
		<div class="wrap">
			<h2><?php echo $this->get_title(); ?></h2>
			
			<form action="" method="post">
				<?php wp_nonce_field('jwbp-posts-add', 'jwbp-posts-add-nonce'); ?>
				<table class="form-table">
					<tbody>
						<tr>
							<th><label for="jwbp-addposts-posttype"><?php _e('Post type', 'bulkpress'); ?></label></th>
							<td>
								<select name="jwbp-addposts-posttype" id="jwbp-addposts-posttype">
									<?php foreach ($posttypes as $index => $posttype) : ?>
										<option value="<?php echo esc_attr($index); ?>" <?php selected($posttype_current->name, $posttype->name); ?>><?php echo $posttype->labels->singular_name; ?></option>
									<?php endforeach; ?>
								</select>
								<img src="<?php echo esc_url(admin_url('images/wpspin_light.gif')); ?>" class="ajax-loading" alt="" />
							</td>
						</tr>
						<tr>
							<th><label for="jwbp-addposts-poststatus"><?php _e('Post status', 'bulkpress'); ?></label></th>
							<td>
								<select name="jwbp-addposts-poststatus" id="jwbp-addposts-poststatus">
									<?php foreach ($poststati as $index => $poststatus) : ?>
										<option value="<?php echo esc_attr($index); ?>" <?php selected($poststatus_current->name, $poststatus->name); ?>><?php echo $poststatus->label; ?></option>
									<?php endforeach; ?>
								</select>
								<img src="<?php echo esc_url(admin_url('images/wpspin_light.gif')); ?>" class="ajax-loading" alt="" />
							</td>
						</tr>
						<tr class="jwbp-addposts-posts">
							<th><label for="jwbp-addposts-posts-titles"><?php _e('Posts', 'bulkpress'); ?></label></th>
							<td>
								<div id="jwbp-addposts-posts-titles-container">
									<textarea name="jwbp-addposts-posts-titles" id="jwbp-addposts-posts-titles" class="jwbp-lined"><?php echo esc_html($posts_titles_current); ?></textarea>
									<div class="jwbp-clear"></div>
									<div class="description">
										<h3><?php _e('Titles', 'bulkpress'); ?></h3>
										<p><?php _e('Enter the post titles you want to add in the left textbox, separating different posts by newlines. You can assign posts to parent posts by entering the full post path, separating different post titles by slashes (<code>/</code>).', 'bulkpress'); ?></p>
										<p><?php _e('If you need a slash in your actual post title, you can escape it by prepending it with a backslash. For example, &quot;Audio/Video&quot; will add a post &quot;Audio&quot; with a child post &quot;Video&quot;, whilst &quot;Audio\/Video&quot; will add a post &quot;Audio/Video&quot;.', 'bulkpress'); ?></p>
									</div>
								</div>
								<div id="jwbp-addposts-posts-slugs-container">
									<textarea name="jwbp-addposts-posts-slugs" id="jwbp-addposts-posts-slugs" class="jwbp-lined"><?php echo esc_html($posts_slugs_current); ?></textarea>
									<div class="jwbp-clear"></div>
									<div class="description">
										<h3><?php _e('Slugs', 'bulkpress'); ?></h3>
										<p><?php _e('Slugs will be automatically generated if they are not manually set, but it is also possible to enter specific slugs for each post. You can do this by putting the slugs in the right textbox on the lines of that textbox corresponding with the lines in the left textbox (for the post paths). Please note that the slug entered is used for the last post in the hierarchy, separating slugs with slashes will not have the effect of adding slugs for multiple posts.', 'bulkpress'); ?></p>
									</div>
								</div>
							</td>
						</tr>
						<tr class="jwbp-filter-hierarchical-1<?php if (!$posttype_current->hierarchical) echo ' jwbp-hidden'; ?>">
							<th><label for="jwbp-addposts-topparent"><?php _e('Add to parent', 'bulkpress'); ?></label></th>
							<td>
								<?php if ($posttype_current->hierarchical) : ?>
									<?php wp_dropdown_pages(array(
										'show_option_none' => __('No parent', 'bulkpress'),
										'post_type' => $posttype_current->name,
										'name' => 'jwbp-addposts-topparent',
										'id' => 'jwbp-addposts-topparent',
										'selected' => $_POST['jwbp-addposts-topparent'],
										'post_status' => array('publish', 'draft')
									)); ?>
								<?php else : ?>
									<div id="jwbp-addposts-topparent"></div>
								<?php endif; ?>
							</td>
						</tr>
						<tr class="jwbp-filter-hierarchical-1<?php if (!$posttype_current->hierarchical) echo ' jwbp-hidden'; ?>">
							<th><label for="jwbp-addposts-create-inexistent-parents"><?php _e('Create inexistent parent posts', 'bulkpress'); ?></label></th>
							<td>
								<input type="checkbox" name="jwbp-addposts-create-inexistent-parents" id="jwbp-addposts-create-inexistent-parents" value="1" <?php checked($_POST['jwbp-addposts-create-inexistent-parents']); ?> />
								<span class="description">
									<?php _e('When specifying post parents by using the slash (<code>/</code>) symbol, you can use this option to create parent posts that do not exist yet.', 'bulkpress'); ?>
								</span>
								<p class="description">
									<?php _e('For example, if you enter <code>Cities/Leiden</code> and the post &quot;Cities&quot; does not exist yet, leaving the box unchecked will skip the post (thus adding neither &quot;Cities&quot; nor &quot;Leiden&quot;) whilst checking the box will create both &quot;Cities&quot; and &quot;Leiden&quot;.', 'bulkpress'); ?>
								</p>
							</td>
						</tr>
					</tbody>
				</table>
				<?php submit_button(__('Add posts', 'bulkpress')); ?>
			</form>
		</div>
		<?php
	}

}
?>