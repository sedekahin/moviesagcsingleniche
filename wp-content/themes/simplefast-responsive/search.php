<?php get_header(); ?>
<div style="clear: both"></div>
<div id="container">
<div id="contents">
<?php echo spp(get_search_query());?>

<?php get_template_part( 'loop' ); ?>	
<div style="clear: both"></div>
</div>
<?php get_sidebar(); ?>
<?php get_footer(); ?>