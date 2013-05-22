<?php get_header(); ?>
<div id="container">
<div id="contents">
<?php if (have_posts()) : while (have_posts()) : the_post();  ?>
<article class="post">
<section class="post-single"> <h1><?php the_title(); ?></h1>
<div class="tags"><?php edit_post_link('Edit', '', ''); ?> <?php the_time('l, F jS Y.') ?>  &#124; <?php the_category(', ') ?> </div></section>  
<?php the_content(); ?>	
<?php echo spp(single_post_title( '', false )) ;?>
<?php get_template_part( 'ads' ); ?>
<div style="clear: both"></div>
<section class="tags"><?php the_tags('tags: ',', ',''); ?></section><section class="social">
<div class="facebook">
<iframe src="http://www.facebook.com/plugins/like.php?href=<?php echo urlencode(get_permalink($post->ID)); ?>&amp;layout=button_count&amp;show_faces=false&amp;
width=50&amp;action=like&amp;colorscheme=light" scrolling="no" frameborder="0" allowTransparency="true" style="border:none; overflow:hidden; width:50px; height:60px;">
</iframe>
</div>
<div class="twitter">
<script src="http://platform.twitter.com/widgets.js" type="text/javascript"></script>
<a href="http://twitter.com/share?url=<?php echo urlencode(get_permalink($post->ID)); ?>&count=horizontal" class="twitter-share-button">Tweet</a></div>
<g:plusone size="medium" href="<?php the_permalink(); ?>"></g:plusone>
<div style="clear: both"></div>
</section>
</article>
<?php endwhile; ?>
<section class="related"><?php get_template_part( 'related' ); ?></section>
<?php else : ?>
<article class="post"><h2>Not Found</h2>Sorry, but you are looking for something that isn't here.</article>
<?php endif; ?>
</div>
<?php get_sidebar(); ?>
<?php get_footer(); ?>