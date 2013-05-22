<?php
/*
Plugin Name: The SEO Rich Snippets
Plugin URI: http://www.searchbyle.com/blog/seo/the-seo-rich-snippets-wordpress-plugin.html
Description: The SEO Rich Snippets for home page review website. Get higher click through rate by displaying star rating in Google search results.
Version: 1.0
Author: Vũ Lê
Author URI: http://www.facebook.com/vulexyz
License: GPL2
*/
define('SNIPPET_DIR', dirname(__FILE__));
register_activation_hook(__FILE__,'trs_installer');
add_action('wp_footer', 'add_footer_snippets');
function trs_installer() {
	if (get_option('snippet_version') != '1.0') {
		add_option('snippet_post_support',false); 
		add_option('snippet_home_name',get_option('blogname'));
		add_option('snippet_home_address','511/67 Huynh Van Banh');
		add_option('snippet_home_local','Ho Chi Minh');
		add_option('snippet_home_region','VN');
		add_option('snippet_home_url',get_option('siteurl'));
		add_option('snippet_home_reviewer',rand(10,100).' customers');
		add_option('snippet_home_value',4);
		add_option('snippet_home_best',5);
		add_option('snippet_version','1.0');
	}
}
function add_footer_snippets() {
	$__rich_name = get_option('snippet_home_name');
	$__rich_address = get_option('snippet_home_address');
	$__rich_local = get_option('snippet_home_local');
	$__rich_region = get_option('snippet_home_region');
	$__rich_url = get_option('snippet_home_url');
	$__rich_reviewer = get_option('snippet_home_reviewer');
	$__rich_value = get_option('snippet_home_value');
	$__rich_best = get_option('snippet_home_best');
	?>
    <div id="RichSnippets">
        <div itemscope="" itemtype="http://data-vocabulary.org/Review">
            <span itemprop="itemreviewed" itemscope="" itemtype="http://data-vocabulary.org/Organization">
                <span itemprop="name"><?php echo $__rich_name;?></span>
                <?php _e('located at','tsrtext');?>  
                <span itemprop="address" itemscope="" itemtype="http://data-vocabulary.org/Address">
                    <span itemprop="street-address"><?php echo $__rich_address;?></span>
                    , 
                    <span itemprop="locality"><?php echo $__rich_local;?></span>, 
                    <span itemprop="region"><?php echo $__rich_region;?></span>
                </span>
                <span style="display:none;">
                    <a href="<?php echo $__rich_url;?>" itemprop="url"><?php echo $__rich_url;?></a>
                </span>
            </span>
            . <?php _e('Reviewed by','tsrtext');?> 
            <span itemprop="reviewer"><?php echo $__rich_reviewer;?></span>
            <?php _e('rated:','tsrtext');?> 
            <span itemprop="rating" itemscope="" itemtype="http://data-vocabulary.org/Rating">
                <span itemprop="value"><?php echo $__rich_value;?></span>
                /
                <span itemprop="best"><?php echo $__rich_best;?></span>
            </span>
        </div>  
    </div>
    <style>
		#RichSnippets { text-align:center; margin:auto;}
	</style>
    <?
}
if (is_admin() || strstr($_SERVER['PHP_SELF'], 'wp-admin/')) {
	require_once(SNIPPET_DIR . "/admin.inc.php");
}
?>