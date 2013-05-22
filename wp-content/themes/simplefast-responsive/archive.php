<?php get_header(); ?>
<div style="clear: both"></div>
<div id="container">
<div id="contents">
<h1><?php if(is_category()) { ?> <?php single_cat_title(''); ?>
    <?php } elseif (is_day()) { ?><?php the_time('F jS, Y'); ?>
	<?php } elseif (is_month()) { ?> <?php the_time('F, Y'); ?>
	<?php } elseif (is_tag()) { ?> <?php single_tag_title(''); ?>
	<?php } elseif (is_year()) { ?> <?php the_time('Y'); ?>
	<?php } elseif (is_author()) { ?> Author
	<?php } elseif (isset($_GET['paged']) && !empty($_GET['paged'])) { ?> Blog Archives
	    <?php }  if ( get_query_var('paged') ) { echo ' ('; echo __('page') . ' ' . get_query_var('paged');   echo ')';  } ?></h1>
	
<?php get_template_part( 'loop' ); ?>	
</div>
<?php get_sidebar(); ?>
<?php get_footer(); ?>