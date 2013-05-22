<?php if (is_single()) { ?>
<div id="post-navigator-single">
<div class="alignleft"><?php previous_post_link('&laquo;&nbsp;%link') ?></div>
<div class="alignright"><?php next_post_link('%link&nbsp;&raquo;') ?></div>
<div class="clearfix"></div>
</div>

<?php } else if (is_page()) { ?>

<div id="post-navigator">
<?php wp_link_pages('<strong>Page</strong> ', '', 'number'); ?>
<div class="clearfix"></div>
</div>

<?php } else if (is_tag()) { ?>

<div id="post-navigator">
<div class="alignright"><?php next_posts_link('Older Entries &laquo; '); ?></div>
<div class="alignleft"><?php previous_posts_link('&raquo; Newer Entries'); ?></div>
<div class="clearfix"></div>
</div>

<?php } else { ?>

<div id="post-navigator">
<?php if (function_exists('fastestwp_pagenavi')) : ?>
<?php fastestwp_pagenavi(); ?>
<?php else : ?>
<div class="alignleft"><?php posts_nav_link('',__('&laquo; Newer Posts'),'') ?></div>
<div class="alignright"><?php posts_nav_link('','',__('Older Posts &raquo;')) ?></div>
<?php endif; ?>
<div class="clearfix"></div>
</div>

<?php } ?>