<?php get_header(); ?>
<div style="clear: both"></div>
<div id="container">
<div id="contents">
<h1><?php bloginfo('name'); ?> - <?php bloginfo('description'); ?> <?php  if ( get_query_var('paged') ) { echo ' ('; echo _e('page') . ' ' . get_query_var('paged');   echo ')';  } ?></h1>
<?php get_template_part( 'loop' ); ?>
</div>
<?php get_sidebar(); ?>
<?php get_footer(); ?>