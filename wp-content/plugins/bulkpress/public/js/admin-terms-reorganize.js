/**
 * Add or remove expand handlers for terms hierarchy items
 */
function jwbp_handle_expandable()
{
	jQuery(function($) {
		$('.jwbp-termshierarchy li').each(function() {
			var term_id = parseInt($(this).attr('data-term-id'));
			var term_depth = parseInt($(this).attr('data-term-depth'));
			var term_parent = parseInt($(this).attr('data-term-parent'));
			
			if ($(this).find('li').length) {
				if (!$(this).find('> .jwbp-container > .jwbp-expand').length) {
					$(this).children('.jwbp-container').prepend('<a href="#" class="jwbp-expand"><span></span></a>');
				}
			}
			else {
				$(this).find('> .jwbp-container > .jwbp-expand').remove();
			}
			
			$(this).find('.jwbp-expand').unbind('click').click(function() {
				$(this).closest('li').toggleClass('mjs-nestedSortable-collapsed').toggleClass('mjs-nestedSortable-expanded');
				
				return false;
			});
		});
	});
}

/**
 * Delete an item from the terms hierarchy
 *
 * @param element el List item element for the item to delete
 */
function jwbp_item_delete(el)
{
	el.addClass('jwbp-deleted');
	el.find('li').addClass('jwbp-deleted');
	
	el.removeClass('jwbp-normal');
	el.find('li').removeClass('jwbp-normal');
	
	el.removeClass('jwbp-editing');
	el.find('li').removeClass('jwbp-editing');
}

/**
 * Restore an item from the terms hierarchy
 *
 * @param element el List item element for the item to restore
 */
function jwbp_item_restore(el)
{
	el.addClass('jwbp-normal');
	el.find('li').addClass('jwbp-normal');
	
	el.removeClass('jwbp-deleted');
	el.find('li').removeClass('jwbp-deleted');
	
	el.removeClass('jwbp-editing');
	el.find('li').removeClass('jwbp-editing');
}

/**
 * Delete an item from the terms hierarchy
 *
 * @param element el List item element for the item to delete
 */
function jwbp_item_edit(el)
{
	el.addClass('jwbp-editing');
	el.removeClass('jwbp-deleted');
	el.removeClass('jwbp-normal');
}

jQuery(function($) {
	// Expand handlers
	jwbp_handle_expandable();
	
	// List display type
	$('.jwbp-listdisplay a').click(function() {
		var dashindex = $(this).attr('id').lastIndexOf('-');
		var displaytype = $(this).attr('id').substring(dashindex + 1);
		
		$('.jwbp-termshierarchy').each(function() {
			$(this).attr('class', $(this).attr('class').replace(/jwbp\-display\-[a-z0-9]+/, 'jwbp-display-' + displaytype));
		});
		
		$(this).parents('li').addClass('current').siblings().removeClass('current');
		
		return false;
	});
	
	// Delete item
	$('.jwbp-item-overlay a.submitdelete').click(function() {
		jwbp_item_delete($(this).closest('li'));
		
		return false;
	});
	
	// Restore item
	$('.jwbp-item-overlay .untrash a').click(function() {
		jwbp_item_restore($(this).closest('li'));
		
		return false;
	});
	
	// Edit item
	$('.jwbp-item-overlay .edit a').click(function() {
		jwbp_item_edit($(this).closest('li'));
		
		return false;
	});
	
	// Save item
	$('.jwbp-content-edit .jwbp-save').click(function() {
		$(this).closest('li').children('.jwbp-container').find('.jwbp-term-title').html($(this).siblings('input').val());
		
		jwbp_item_restore($(this).closest('li'));
		
		return false;
	});
	
	// Cancel item edit
	$('.jwbp-content-edit .jwbp-cancel').click(function() {
		$(this).siblings('input').val($(this).closest('li').find('.jwbp-term-title').html());
		
		jwbp_item_restore($(this).closest('li'));
		
		return false;
	});
	
	// Save changed
	$('#jwbp-termshierarchy-form').submit(function() {
		$(this).find('li').each(function() {
			var parentid = 0;
			
			if ($(this).parents('li').length) {
				parentid = parseInt($(this).parent().closest('li').attr('data-term-id'));
			}
			
			$('input[name="jwbp-term-parent[' + $(this).attr('data-term-id') + ']"]').val(parentid);
		});
		
		return true;
	});
	
	// Sortable terms hierarchy
	$('.jwbp-termshierarchy').nestedSortable({
		listType: 'ul',
		forcePlaceholderSize: true,
		handle: '> .jwbp-container .jwbp-drag',
		helper:	'clone',
		items: 'li',
		opacity: .6,
		placeholder: 'jwbp-placeholder',
		revert: 250,
		tabSize: 25,
		tolerance: 'pointer',
		toleranceElement: '> div',
		isTree: true,
		expandOnHover: 700,
		startCollapsed: true,
		update: function() {
			jwbp_handle_expandable();
			
			$('#jwbp-notice-unsavedchanges').show();
		}
	});
});