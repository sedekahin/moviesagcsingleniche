<?php get_header(); ?>
<div style="clear: both"></div>
<div id="container">
<div id="contents">
<h1><?php the_search_query();?> </h1>
<?php get_template_part( 'loop' ); ?>	
<div style="clear: both"></div>
</div>
<?php get_sidebar(); ?>
<?php get_footer(); ?>