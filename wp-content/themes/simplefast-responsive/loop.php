<?php if (have_posts()) : while (have_posts()) : the_post();  ?>
<article class="post" id="post-<?php the_ID(); ?>"> 
<?php
if (fastestwp_mobile()) { ?>
<?php } else { ?>
<?php get_template_part( 'thumb' ); ?>
<?php }?>
<h2><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title(); ?>"><?php the_title(); ?></a></h2>
<p><?php echo fastestwp_excerpt(20); ?></p>
 <section class="tags"><?php the_date(); ?> | <?php the_category(', ') ?> </section>  
</article>
<?php endwhile; ?>
<?php get_template_part( 'ads' ); ?>
<?php get_template_part( 'navigator' ); ?>
<?php else : ?>
<article class="post"><h2>Your Search Result:</h2></article>
<?php endif; ?>