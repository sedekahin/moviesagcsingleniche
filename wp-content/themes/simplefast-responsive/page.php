<?php get_header(); ?>
<div style="clear: both"></div>
<div id="container">
<div id="contents">
<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
<article class="post">
<div class="posttitle"><h1><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title(); ?>"><?php the_title(); ?></a></h1></div>
<?php the_content(); ?></article>
<?php endwhile; ?><?php else : ?>
<article class="post"><h2>Not Found</h2>Sorry, but you are looking for something that isn't here.</article>
<?php endif; ?>
</div>
<?php get_sidebar(); ?>
<?php get_footer(); ?>