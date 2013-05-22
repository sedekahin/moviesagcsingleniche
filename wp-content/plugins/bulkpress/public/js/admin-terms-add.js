jQuery(function($) {
	// Synchronize scrolling across term titles and slug textareas
	$('#jwbp-addterms-terms-titles').scroll(function() {
		$('#jwbp-addterms-terms-slugs').scrollTop($(this).scrollTop());
		
		$('#jwbp-addterms-terms-slugs').css('background-position', '0 ' + (5 - $(this).scrollTop()) + 'px');
		$(this).css('background-position', '0 ' + (5 - $(this).scrollTop()) + 'px');
	});
	
	$('#jwbp-addterms-terms-slugs').scroll(function() {
		$('#jwbp-addterms-terms-titles').scrollTop($(this).scrollTop());
		
		$('#jwbp-addterms-terms-titles').css('background-position', '0 ' + (5 - $(this).scrollTop()) + 'px');
		$(this).css('background-position', '0 ' + (5 - $(this).scrollTop()) + 'px');
	});
	
	// Handle change in selected taxonomy
	$('#jwbp-addterms-taxonomy').change(function() {
		var el = this;
		
		var taxonomy = $(this).val();
		
		$(this).siblings('.ajax-loading').css('visibility', 'visible');
		
		$.post(JWBP_Ajax.ajaxurl, {
			action: 'jwbp_ajax_get_taxonomy',
			taxonomy: taxonomy
		}, function(data) {
			$(el).siblings('.ajax-loading').css('visibility', 'hidden');
			
			if (typeof data.error == 'undefined' || !data.error) {
				$('#jwbp-addterms-topparent').replaceWith(data.terms_select_html);
				
				if (data.taxonomy.hierarchical) {
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
	
	// Fill add terms textboxes with example data
	$('.jwbp-addterms-fill-example').click(function() {
		$('#jwbp-addterms-terms-titles').val(
			  'Cities/Amsterdam\n'
			+ 'Cities/London\n'
			+ 'Cities/Paris\n'
			+ 'People/A\n'
			+ 'People/B\n'
			+ 'People/C\n'
			+ 'People/D\n'
			+ 'People/C/B/F\n'
			+ 'Great\n'
			+ 'Capitals/Africa/South Africa\n'
			+ 'Capitals/Africa/Egypt\n'
			+ 'Fantastic\n'
			+ 'Awesome\n'
			+ 'Hardware/Audio\\/video/Top Products\n'
			+ 'Hardware/Computers\n'
			+ 'Brilliant\n'
			+ $('#jwbp-addterms-terms-titles').val());
		
		$('#jwbp-addterms-terms-slugs').val(
			  'amsterdam\n'
			+ 'london\n'
			+ 'Paris\n'
			+ 'letter-a\n'
			+ 'letter-bb\n'
			+ '\n'
			+ '\n'
			+ 'F\n'
			+ 'good\n'
			+ 'south-africa\n'
			+ 'egypt\n'
			+ 'fantastico\n'
			+ '\n'
			+ 'top-products\n'
			+ 'pcs\n'
			+ 'brilliant\n'
			+ $('#jwbp-addterms-terms-slugs').val());
		
		return false;
	});
});