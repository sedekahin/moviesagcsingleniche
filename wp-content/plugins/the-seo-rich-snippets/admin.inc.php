<?php
/*
The SEO Rich Snippets
Admin Settings
*/
add_action('admin_menu', 'snippet_adminmenu');

function snippet_adminmenu() {
	if (function_exists('current_user_can')) {
		if (current_user_can('manage_options')) {
			$snippet_is_admin = true;
		}
	} else {
		global $user_ID;
		if (user_can_edit_user($user_ID, 0)) {
			$snippet_is_admin = true;
		}
	}

	if ((function_exists('add_options_page'))&&($snippet_is_admin)) {
		add_options_page(__("SEO Rich Snippets", 'the_seo_rich_snippets'), __("SEO Rich Snippets", 'the_seo_rich_snippets'), 9, 'snippets_options', 'snippets_options');
	}
}
function snippets_options() {
	if ($_POST['submit']):
		update_option('snippet_home_name',$_POST['home_name']);
		update_option('snippet_home_address',$_POST['home_address']);
		update_option('snippet_home_local',$_POST['home_local']);
		update_option('snippet_home_region',$_POST['home_region']);
		update_option('snippet_home_url',$_POST['home_url']);
		update_option('snippet_home_reviewer',$_POST['home_reviewer']);
		update_option('snippet_home_value',$_POST['home_value']);
		update_option('snippet_home_best',$_POST['home_best']);
	endif;
?>
<div class="wrap">
    <h2>The SEO Rich Snippets</h2>
    <p>
    The SEO Rich Snippets for home page review website. Get higher click through rate by displaying star rating in Google search results.
    </p>
    <p>
    If you like this plugin and find it useful, help keep this plugin free and actively developed by clicking the donate button. Also, don't forget to follow me on <a href="http://twitter.com/vulhoang" target="_blank">Twitter</a>.
    </p>
    <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
    <input type="hidden" name="cmd" value="_s-xclick">
    <input type="hidden" name="hosted_button_id" value="GXGN59QGMCGCQ">
    <input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donate_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
    <img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
    </form>
    <div class="form-wrap">
    <form id="addtag" method="post" action="" class="validate">
        <table class="form-table">
            <tr valign="top" class="form-required">
                <th scope="row"><label for="home_name"><?php _e('Site Title','tsrtext') ?></label></th>
                <td><input name="home_name" type="text" id="home_name" value="<?php echo get_option('snippet_home_name');?>" class="regular-text" aria-required="true" /></td>
            </tr>
            <tr valign="top" class="form-required">
                <th scope="row"><label for="home_address"><?php _e('Address','tsrtext') ?></label></th>
                <td><input name="home_address" type="text" id="home_address" value="<?php echo get_option('snippet_home_address');?>" class="regular-text" aria-required="true" /></td>
            </tr>
            <tr valign="top" class="form-required">
                <th scope="row"><label for="home_local"><?php _e('Locality','tsrtext') ?></label></th>
                <td><input name="home_local" type="text" id="home_local" value="<?php echo get_option('snippet_home_local');?>" class="regular-text" aria-required="true" /></td>
            </tr>
            <tr valign="top" class="form-required">
                <th scope="row"><label for="home_region"><?php _e('Region','tsrtext') ?></label></th>
                <td><input name="home_region" type="text" id="home_region" value="<?php echo get_option('snippet_home_region');?>" class="regular-text" aria-required="true" /></td>
            </tr>
            <tr valign="top" class="form-required">
                <th scope="row"><label for="home_url"><?php _e('URL','tsrtext') ?></label></th>
                <td><input name="home_url" type="text" id="home_url" value="<?php echo get_option('snippet_home_url');?>" class="regular-text" aria-required="true" /></td>
            </tr>
            <tr valign="top" class="form-required">
                <th scope="row"><label for="home_reviewer"><?php _e('Reviewer','tsrtext') ?></label></th>
                <td><input name="home_reviewer" type="text" id="home_reviewer" value="<?php echo get_option('snippet_home_reviewer');?>" class="regular-text" aria-required="true" /></td>
            </tr>
            <tr valign="top" class="form-required">
                <th scope="row"><label for="home_value"><?php _e('Value / Best','tsrtext') ?></label></th>
                <td><select name="home_value" id="home_value" aria-required="true">
				<?php for ($i = 1; $i <= 10;$i+= 0.1) {
					if ($i == get_option('snippet_home_value')) {
						echo '<option value="'.$i.'" selected="selected">'.number_format($i,1).'</option>';
					} else {
						echo '<option value="'.$i.'">'.number_format($i,1).'</option>';
					}
				} ?>
				</select>
                <select name="home_best" id="home_best" aria-required="true">
					<option value="5" <?php if (get_option('snippet_home_best')==5) {?>selected="selected"<?php }?>>5.0</option>
                    <option value="10" <?php if (get_option('snippet_home_best')==10) {?>selected="selected"<?php }?>>10.0</option>
				</select></td>
            </tr>
            <!--
            <tr valign="top" class="form-required">
                <th scope="row"><label for="home_value"><?php _e('Article Support','tsrtext') ?></label></th>
                <td><fieldset><legend class="screen-reader-text"><span><?php _e('Articles') ?></span></legend><label for="users_can_register">
<input name="snippet_post_support" type="checkbox" id="snippet_post_support" value="1" <?php checked('1', get_option('snippet_post_support')); ?> />
<?php _e('The SEO Rich Snippets support the articles') ?></label></fieldset></td>
            </tr>    
            -->   
			<tr valign="top" class="form-required">

                <th scope="row"><label for="home_value"><?php _e('Rich Snippets Testing Tool','tsrtext') ?></label></th>

                <td><a href="http://www.google.com/webmasters/tools/richsnippets?url=<?php echo urlencode(get_option('siteurl'));?>" title="Check that Google can correctly parse your structured data markup and display it in search results." target="_blank">Check that Google can correctly parse your structured data markup and display it in search results.</a></td>

            </tr>                  
        </table>
        <?php submit_button(); ?>
	</form>
    </div>
</div>
<?
}
?>