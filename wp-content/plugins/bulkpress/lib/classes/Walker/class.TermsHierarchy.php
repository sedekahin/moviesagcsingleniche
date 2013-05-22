<?php
class JWBP_Walker_TermsHierarchy extends Walker_Category
{

	/**
	 * @see Walker::start_el()
	 * @since 2.1.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param object $term Term data object.
	 * @param int $depth Depth of category. Used for padding.
	 * @param array $args Uses 'selected' and 'show_count' keys, if they exist.
	 */
	function start_el(&$output, $term, $depth, $args, $id = 0)
	{
		ob_start();
		?>
		<li class="jwbp-normal depth-<?php echo $depth; ?>" data-term-id="<?php echo $term->term_id; ?>" data-term-depth="<?php echo $depth; ?>" data-term-parent="<?php echo $term->parent; ?>">
			<input type="hidden" name="jwbp-term-parent[<?php echo $term->term_id; ?>]" value="<?php echo $term->parent; ?>" class="jwbp-input-term-parent" />
			<div class="jwbp-container">
				<div class="jwbp-drag"></div>
				<div class="jwbp-content jwbp-content-normal">
					<a href="#" class="jwbp-title jwbp-term-title jwbp-expand" title="<?php esc_attr_e('Expand', 'bulkpress'); ?>"><?php echo $term->name; ?></a>
					<?php if (false) : ?>
						<div class="jwbp-item-overlay">
							<div class="jwbp-actions">
								<span class="edit"><a href="#">Edit</a> | </span>
								<span class="trash"><a href="#" class="submitdelete">Delete</a></span>
							</div>
						</div>
					<?php endif; ?>
				</div>
				<div class="jwbp-content jwbp-content-deleted">
					<span class="jwbp-title"><span class="jwbp-term-title"><?php echo $term->name; ?></span> - <em><?php _e('Deleted'); ?></em></span>
					<div class="jwbp-item-overlay">
						<div class="jwbp-actions">
							<span class="untrash"><a href="#">Restore</a></span>
						</div>
					</div>
				</div>
				<div class="jwbp-content jwbp-content-edit">
					<input type="text" value="<?php echo esc_attr($term->name); ?>" />
					<a href="#" class="button button-secondary jwbp-save">Save</a>
					<a href="#" title="<?php esc_attr_e('Cancel'); ?>" class="jwbp-cancel"><?php _e('Cancel'); ?></a>
				</div>
			</div>
		<?php
		$content = ob_get_contents();
		ob_end_clean();
		
		$output .= $content;
	}

	/**
	 * @see Walker::end_el()
	 * @since 2.1.0
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param object $page Not used.
	 * @param int $depth Depth of category. Not used.
	 * @param array $args Only uses 'list' for whether should append to output.
	 */
	function end_el( &$output, $page, $depth = 0, $args = array() ) {
		$output .= "</li>\n";
	}

}
?>