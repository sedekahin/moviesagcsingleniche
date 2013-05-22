jQuery(function($) {
	// Synchronize scrolling across post titles and slug textareas
	$('#jwbp-addposts-posts-titles').scroll(function() {
		$('#jwbp-addposts-posts-slugs').scrollTop($(this).scrollTop());
		
		$('#jwbp-addposts-posts-slugs').css('background-position', '0 ' + (5 - $(this).scrollTop()) + 'px');
		$(this).css('background-position', '0 ' + (5 - $(this).scrollTop()) + 'px');
	});
	
	$('#jwbp-addposts-posts-slugs').scroll(function() {
		$('#jwbp-addposts-posts-titles').scrollTop($(this).scrollTop());
		
		$('#jwbp-addposts-posts-titles').css('background-position', '0 ' + (5 - $(this).scrollTop()) + 'px');
		$(this).css('background-position', '0 ' + (5 - $(this).scrollTop()) + 'px');
	});
	
	// Handle change in selected posttype
	$('#jwbp-addposts-posttype').change(function() {
		var el = this;
		
		var posttype = $(this).val();
		
		$(this).siblings('.ajax-loading').css('visibility', 'visible');
		
		$.post(JWBP_Ajax.ajaxurl, {
			action: 'jwbp_ajax_get_posttype',
			posttype: posttype
		}, function(data) {
			$(el).siblings('.ajax-loading').css('visibility', 'hidden');
			
			if (typeof data.error == 'undefined' || !data.error) {
				$('#jwbp-addposts-topparent').replaceWith(data.posts_select_html);
				
				if (data.posttype.hierarchical) {
					$('.jwbp-filter-hierarchical-1').show();
					$('.jwbp-filter-hierarchical-0').hide();
				}
				else {
					$('.jwbp-filter-hierarchical-1').hide();
					$('.jwbp-filter-hierarchical-0').show();
				}
			}
		}, 'json');
	});
});