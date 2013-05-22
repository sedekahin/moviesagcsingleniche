<aside class="thumb">
<a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php
if(has_post_thumbnail()) {
	the_post_thumbnail();
} else { ?>
<img src="<?php echo bloginfo('template_url'); ?>/scripts/timthumb.php?src=<?php echo fastestwp_first_image(); ?>&amp;w=150&amp;h=100&amp;zc=2&amp;q=80" alt="<?php the_title(); ?>" width="150" height="100"/>
<?php }
?></a>
</aside>