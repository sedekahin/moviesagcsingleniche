<aside id="sidebar"><div style="clear: both"></div>
<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('Sidebar') ) : ?>	
<div class="box"><h4>Categories</h4>
<ul><?php wp_list_categories('&title_li='); ?></ul>
</div>
<?php endif; ?>	
</aside>