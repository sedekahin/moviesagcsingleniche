<?php query_posts('posts_per_page=4'); if (have_posts()) : 	while (have_posts()) : the_post(); ?>
<div class="post-sidebar post-<?php echo $postCount ;?>"><?php get_template_part( 'thumb2' ); ?>
<div class="posttitle"><h3><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title(); ?>"><?php
$the_title = $post->post_title;  $getlength = strlen($the_title); $thelength = 35; echo substr($the_title, 0, $thelength); if ($getlength > $thelength) ; ?></a></h3></div>
<div style="clear: both"></div>
</div>	
<?php endwhile; endif; ?>
<?php wp_reset_query(); ?>