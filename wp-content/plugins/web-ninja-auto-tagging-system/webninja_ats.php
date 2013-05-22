<?php
/*
Plugin Name: Web Ninja Auto Tagging System
Plugin URI: http://josh-fowler.com/?page_id=70
Description: This plugin will automatically make tags with the tagthe.net and yahoo yql services when you save or update a post. Also has the option for retagging all post.
Version: 1.0.4
Author: Josh Fowler
Author URI: http://josh-fowler.com
*/

/*  Copyright 2010  Josh Fowler (http://josh-fowler.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

define('wbatsversion', '1.0.2', true);

$wbats_options = get_option('webninja_ats_options'); 

function wbats_set_option($option_name, $option_value) {
	$wbats_options = get_option('webninja_ats_options');
	$wbats_options[$option_name] = $option_value;
	update_option('webninja_ats_options', $wbats_options);
}

function wbats_get_option($option_name) {
	$wbats_options = get_option('webninja_ats_options'); 
	if (!$wbats_options || !array_key_exists($option_name, $wbats_options)) {
		$wbats_default_options=array();
		$wbats_default_options['enable_yahoo']      = true;  
		$wbats_default_options['enable_tagthenet']  = true;  
		$wbats_default_options['yahoo_num']     	= '5';  
		$wbats_default_options['tagthenet_num']     = '5';  
		$wbats_default_options['fsock_timeout']     = 10; 
		$wbats_default_options['remove_tags']       = ''; 
		$wbats_default_options['add_tags']       	= ''; 
		$wbats_default_options['append']      		= false; 
		add_option('webninja_ats_options', $wbats_default_options, 'Settings for Web Ninja Auto Tag System plugin');
		$result = $wbats_default_options[$option_name];
	} else {
		$result = $wbats_options[$option_name];
	}
	return $result;
}

function wbats_admin() {
  if (function_exists('add_options_page')) {
    add_options_page('Web Ninja Auto Tag System', 
                     'Web Ninja ATS', 
                     8, 
                     basename(__FILE__), 
                     'wbats_options');
  }
}

function wbats_options() {
  global $wpdb;
  if (isset($_POST['retag'])) {
	set_time_limit(0);
	$sql = "SELECT id 
			FROM {$wpdb->posts}
			WHERE post_status='publish' AND post_type='post' 
			ORDER BY post_modified_gmt DESC;";
	$posts = $wpdb->get_results($sql);
	$updated = 0;
	foreach($posts as $post){
		$object = get_post($post->id);
		if ( $object != false && $object != null ) {
			wbats_tag_posts($object->ID);
			$updated++;
		}		
	}
    ?><div class="updated"><p><strong><?php _e($updated.' posts re-tagged.', 'wbats')?></strong></p></div><?php
  }
  if (isset($_POST['tag_notags'])) {
	set_time_limit(0);
	$sql = "SELECT id 
			FROM {$wpdb->posts}
			WHERE post_status='publish' AND post_type='post' 
			ORDER BY post_modified_gmt DESC;";
	$posts = $wpdb->get_results($sql);
	$updated = 0;
	foreach($posts as $post){
		$object = get_post($post->id);
		$posttags = get_the_tags($post->id);
		if ( $object != false && $object != null && !$posttags) {
			wbats_tag_posts($object->ID);
			$updated++;
		}		
	}
    ?><div class="updated"><p><strong><?php _e($updated.' posts with no tags are now tagged.', 'wbats')?></strong></p></div><?php
  }
  if (isset($_POST['default_settings'])) {
    $wbats_factory_options = array();
    update_option('webninja_ats_options', $wbats_factory_options);
    ?><div class="updated"><p><strong><?php _e('Default settings set.', 'wbats')?></strong></p></div><?php
  }
  if (isset($_POST['info_update'])) {
    ?><div class="updated"><p><strong><?php 
    $wbats_options = get_option('webninja_ats_options');
	$wbats_options['enable_yahoo']      = $_POST['enable_yahoo'];  
	$wbats_options['enable_tagthenet']  = $_POST['enable_tagthenet'];  
	$wbats_options['yahoo_num']     	= $_POST['yahoo_num'];  
	$wbats_options['tagthenet_num']     = $_POST['tagthenet_num'];  
	$wbats_options['fsock_timeout']     = $_POST['fsock_timeout'];  
	$wbats_options['remove_tags']       = $_POST['remove_tags'];  
	$wbats_options['add_tags']       	= $_POST['add_tags'];  
	$wbats_options['append']       		= $_POST['append'];  
    update_option('webninja_ats_options', $wbats_options);
    _e('Options saved', 'wbats')
    ?></strong></p></div><?php
	} 
	?>
    <?php if (isset($_POST['retag_now'])) {
	 _e('<div class="updated"><p><strong>Re-Tagging Done.</strong></p></div>', 'wbats');
} ?>

<div class=wrap style="width:820px">
    <h2>Web Ninja Auto Tagging System</h2>
<div style="float:right; width:390px; border:1px #DEDEDD dashed; background-color:#FEFAE7; padding:10px 10px 10px 10px">
<b>Description:</b> This plugin will automatically make tags with the tagthe.net and yahoo yql services when you save or update a post. Also has the option for re-tagging post.<br />
<Br />
<b>Homepage:</b> <a href="http://josh-fowler.com/?page_id=230" target="_blank">Web Ninja Auto Tagging System</a><br />
<Br />
<b>Support:</b> <a href="http://josh-fowler.com/forum/" target="_blank">Web Ninja Forums</a><br />
<br />
<b>Developed by:</b> <a href="http://josh-fowler.com/" target="_blank">Josh Fowler</a><br />
<br />
<b>Like the plugin? Then "Like" The Web Ninja!</b>
<iframe src="http://www.facebook.com/plugins/like.php?href=http%3A%2F%2Fwww.facebook.com%2Fpages%2FThe-Web-Ninja%2F160118787364131&amp;layout=standard&amp;show_faces=false&amp;width=375&amp;action=like&amp;colorscheme=light&amp;height=35" scrolling="no" frameborder="0" style="border:none; overflow:hidden; width:375px; height:35px;" allowTransparency="true"></iframe>
<br />
<b>Thanks:</b> I wanted to say thanks for using my plugin and if you have any suggestions for new features head over to the Support Forum and just drop me a little note. You never know, it could be on the next version. 
</div>
<div style="float:left; width:400px">
  <form method="post">
  <script language="javascript" type="text/javascript">
  var tooltip=function(){
 var id = 'tt';
 var top = 3;
 var left = 3;
 var maxw = 500;
 var speed = 10;
 var timer = 20;
 var endalpha = 95;
 var alpha = 0;
 var tt,t,c,b,h;
 var ie = document.all ? true : false;
 return{
  show:function(v,w){
   if(tt == null){
    tt = document.createElement('div');
    tt.setAttribute('id',id);
    t = document.createElement('div');
    t.setAttribute('id',id + 'top');
    c = document.createElement('div');
    c.setAttribute('id',id + 'cont');
    b = document.createElement('div');
    b.setAttribute('id',id + 'bot');
    tt.appendChild(t);
    tt.appendChild(c);
    tt.appendChild(b);
    document.body.appendChild(tt);
    tt.style.opacity = 0;
    tt.style.filter = 'alpha(opacity=0)';
    document.onmousemove = this.pos;
   }
   tt.style.display = 'block';
   c.innerHTML = v;
   tt.style.width = w ? w + 'px' : 'auto';
   if(!w && ie){
    t.style.display = 'none';
    b.style.display = 'none';
    tt.style.width = tt.offsetWidth;
    t.style.display = 'block';
    b.style.display = 'block';
   }
  if(tt.offsetWidth > maxw){tt.style.width = maxw + 'px'}
  h = parseInt(tt.offsetHeight) + top;
  clearInterval(tt.timer);
  tt.timer = setInterval(function(){tooltip.fade(1)},timer);
  },
  pos:function(e){
   var u = ie ? event.clientY + document.documentElement.scrollTop : e.pageY;
   var l = ie ? event.clientX + document.documentElement.scrollLeft : e.pageX;
   tt.style.top = (u - h) + 'px';
   tt.style.left = (l + left) + 'px';
  },
  fade:function(d){
   var a = alpha;
   if((a != endalpha && d == 1) || (a != 0 && d == -1)){
    var i = speed;
   if(endalpha - a < speed && d == 1){
    i = endalpha - a;
   }else if(alpha < speed && d == -1){
     i = a;
   }
   alpha = a + (i * d);
   tt.style.opacity = alpha * .01;
   tt.style.filter = 'alpha(opacity=' + alpha + ')';
  }else{
    clearInterval(tt.timer);
     if(d == -1){tt.style.display = 'none'}
  }
 },
 hide:function(){
  clearInterval(tt.timer);
   tt.timer = setInterval(function(){tooltip.fade(-1)},timer);
  }
 };
}();
</script>
<style type="text/css">
#tt {
 position:absolute;
 display:block;
 }
 #tttop {
 display:block;
 height:5px;
 margin-left:5px;
 overflow:hidden;
 }
 #ttcont {
 display:block;
 padding:2px 12px 3px 7px;
 margin-left:5px;
 background:#666;
 color:#fff;
 }
#ttbot {
display:block;
height:5px;
margin-left:5px;
overflow:hidden;
}
th {
	text-align:right;
}
</style>
<br>
<br />
    <fieldset class="options" name="general">
      <h3>Option Settings</h3>
      <table width="300px" cellspacing="5" cellpadding="5" class="editform">
        <tr>
          <th nowrap valign="top">Check Yahoo: <span onmouseover="tooltip.show('Check this to enable checking for suggested tags through the Yahoo term extraction YQL and the number of tags you want added to your post from Yahoo.', 400);" onmouseout="tooltip.hide();" style="color:#00F; cursor:pointer">[?]</span></th>
          <td><input type="checkbox" name="enable_yahoo" id="enable_yahoo" value="true" <?php if (wbats_get_option('enable_yahoo')) echo "checked"; ?> />
            # of tags: <input name="yahoo_num" type="text" id="yahoo_num" value="<?php echo wbats_get_option('yahoo_num'); ?>" size="10" /></td>
		</tr>
        <tr>
          <th nowrap valign="top">Check tagthe.net: <span onmouseover="tooltip.show('Check this to enable checking for suggested tags through the tagthe.net API and the number of tags you want added to your post from tagthe.net.', 400);" onmouseout="tooltip.hide();" style="color:#00F; cursor:pointer">[?]</span></th>
          <td><input type="checkbox" name="enable_tagthenet" id="enable_tagthenet" value="true" <?php if (wbats_get_option('enable_tagthenet')) echo "checked"; ?> />
            # of tags: <input name="tagthenet_num" type="text" id="tagthenet_num" value="<?php echo wbats_get_option('tagthenet_num'); ?>" size="10" /></td>
		</tr>
        <tr>
          <th nowrap valign="top">Append Tags: <span onmouseover="tooltip.show('Check this to append suggested tags to the ones that already exist on the post.', 400);" onmouseout="tooltip.hide();" style="color:#00F; cursor:pointer">[?]</span></th>
          <td><input type="checkbox" name="append" id="append" value="true" <?php if (wbats_get_option('append')) echo "checked"; ?> /></td>
		</tr>
        <tr>
          <th nowrap valign="top">Remove Tags: <span onmouseover="tooltip.show('These are the tags you want removed from the suggested tags. (Comma separated)', 400);" onmouseout="tooltip.hide();" style="color:#00F; cursor:pointer">[?]</span></th>
          <td><input name="remove_tags" type="text" id="remove_tags" value="<?php echo wbats_get_option('remove_tags'); ?>" size="30" /></td>
		</tr>
        <tr>
          <th nowrap valign="top">Add Tags: <span onmouseover="tooltip.show('These are the tags you want added to every post. (Comma separated)', 400);" onmouseout="tooltip.hide();" style="color:#00F; cursor:pointer">[?]</span></th>
          <td><input name="add_tags" type="text" id="add_tags" value="<?php echo wbats_get_option('add_tags'); ?>" size="30" /></td>
		</tr>
        <tr>
          <th nowrap valign="top">API Timeout: <span onmouseover="tooltip.show('Sometimes one of the APIs may be down or slow. This is the time (in seconds) it will wait till it stops trying.', 400);" onmouseout="tooltip.hide();" style="color:#00F; cursor:pointer">[?]</span></th>
          <td><input name="fsock_timeout" type="text" id="fsock_timeout" value="<?php echo wbats_get_option('fsock_timeout'); ?>" size="10" /></td>
		</tr>
      </table>
    </fieldset>
    <div class="submit">
      <input type="submit" name="info_update" class="button-primary" value="<?php _e('Save Options', 'wbats') ?>" /> 
      <input type="submit" name="default_settings" class="button-primary" value="<?php _e('Default Settings', 'wbga') ?>" />

	  </div>
      <br />
      
      <h3>Re-Tagging</h3>
          <b>*Warning:</b> Depending on how many posts it has to do this could take a while. Also, this will only tag published posts.<br>
          <br />
          <input type="submit" name="tag_notags" class="button-primary" value="<?php _e('Tag Post with no tags', 'wbats') ?>" /> <input type="submit" name="retag" class="button-primary" value="<?php _e('Re-Tag all Posts', 'wbats') ?>" /><Br>

  </form>
</div>
</div><?php
}

function wbats_init() {
  load_plugin_textdomain('wbats');
}

function wbats_shutdown() {

}


function wbats_yql_ttn_json_decode($json) 
{  
    $comment = false; 
    $out = '$x='; 
    for ($i=0; $i<strlen($json); $i++) 
    { 
        if (!$comment) 
        { 
            if ($json[$i] == '{' || $json[$i] == '[')        $out .= ' array('; 
            else if ($json[$i] == '}' || $json[$i] == ']')    $out .= ')'; 
            else if ($json[$i] == ':')    $out .= '=>'; 
            else                         $out .= $json[$i];            
        } 
        else $out .= $json[$i]; 
        if ($json[$i] == '"')    $comment = !$comment; 
    } 
	if (strlen($json) > 1) {
    	eval($out . ';'); 
    	return $x;
	} else {
    	return '';
	}
}  

function wbats_post_request($url, $referer, $_data) {
	$data = array();    
	while(list($n,$v) = each($_data)){
		$data[] = "$n=$v";
	}    
	$data = implode('&', $data);
	$url = parse_url($url);
	if ($url['scheme'] != 'http') { 
		die('Only HTTP request are supported !');
	}
	$host = $url['host'];
	$path = $url['path'];
	if ($fp = @fsockopen($host, 80, $errno, $errstr, wbats_get_option('fsock_timeout'))){
		fputs($fp, "POST $path HTTP/1.1\r\n");
		fputs($fp, "Host: $host\r\n");
		fputs($fp, "Referer: $referer\r\n");
		fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");
		fputs($fp, "Content-length: ". strlen($data) ."\r\n");
		fputs($fp, "Connection: close\r\n\r\n");
		fputs($fp, $data);
		$result = ''; 
		while(!feof($fp)) {
			$result .= fgets($fp, 128);
		}
		fclose($fp);
	 }
	$result = explode("\r\n\r\n", $result, 2);
	$content = isset($result[1]) ? $result[1] : '';
	return $content;
}

function wbats_forbidden_tag($forbidden,$tag){
	if (is_array($forbidden) && !empty($forbidden)){
		foreach($forbidden as $forbid){
			if ($forbid !='')
				if(strpos(strtolower($tag), strtolower($forbid))!==false)
					return true;
		}
	}
	return false;
}

function wbats_trim_tags(&$item,$q){
	$item=trim($item);
}
	
function wbats_tag_yahoo($content,$num,$remove_tags){
	$senddata=array('q'=>'select%20*%20from%20search.termextract%20where%20context%3D%22'.urlencode(utf8_decode(addslashes(strip_tags($content)))).'%22',
		'format'=>'json',
		'diagnostics'=>'false'
		);
	$data = wbats_post_request('http://query.yahooapis.com/v1/public/yql', get_bloginfo('url'),$senddata);
	$ret='';
	if($json=wbats_yql_ttn_json_decode($data)){
		$i=0;
		if (is_array($json['query']['results']['Result']) && !empty($json['query']['results']['Result'])){
			foreach($json['query']['results']['Result'] as $kw){
				if ($i>=$num) break;
				if (!wbats_forbidden_tag($remove_tags,$kw)) {
					$ret.=$kw.', ';
					$i++;
				}
			}
		}
	}
	return substr($ret,0,-2);
}

function wbats_tag_the_net($content,$num,$remove_tags){
	$senddata=array('text'=>urlencode(utf8_decode(strip_tags($content))),
		'count'=>$num*3,
		'view'=>'json'
		);
	$data=wbats_post_request('http://tagthe.net/api/', get_bloginfo('url'), $senddata);
	$data=explode("\r\n",$data);
	$data=array_slice($data,1);
	$data=array_slice($data,0,-3);
	$data=implode("\r\n",$data);
	$ret='';
	if($json=wbats_yql_ttn_json_decode($data)){
		$i=0;
		if (is_array($json['memes'][0]['dimensions']['topic']) && !empty($json['memes'][0]['dimensions']['topic'])){
			foreach($json['memes'][0]['dimensions']['topic'] as $topic){
				if ($i>=$num) break;
				if (!wbats_forbidden_tag($remove_tags,$topic)){
					$ret.=$topic.', ';
					$i++;
				}
			}
		}
	}
	return substr($ret,0,-2);
}

function wbats_tag_posts($postid){
	global $wp_filter;
	$yahoo_num = wbats_get_option('yahoo_num');
	$tagthenet_num = wbats_get_option('tagthenet_num');
	$yahoo_enabled = wbats_get_option('enable_yahoo');
	$tagthenet_enabled = wbats_get_option('enable_tagthenet');
	$append = wbats_get_option('append');
	$remove_tags = explode(',',strtolower(wbats_get_option('remove_tags')));
	$add_tags = strtolower(wbats_get_option('add_tags'));
	array_walk($remove_tags,'wbats_trim_tags');

	$post=get_post($postid, ARRAY_A);
	$content=$post['post_title']." ".$post['post_content'];
	if ($yahoo_enabled) { $yahoo_tags = wbats_tag_yahoo($content,$yahoo_num,$remove_tags); }
	if ($tagthenet_enabled) { $tagthenet_tags = wbats_tag_the_net($content,$tagthenet_num,$remove_tags); }
	$keywords = '';
	if ($yahoo_enabled) { $keywords .= $yahoo_tags; }
	if ($yahoo_enabled && $tagthenet_enabled) { $keywords .= ', '; }
	if ($tagthenet_enabled) { $keywords .= $tagthenet_tags; }
	if (($yahoo_enabled || $tagthenet_enabled) && $add_tags != '') { $keywords .= ', '; }
	if ($add_tags != '') { $keywords .= $add_tags; }
	remove_action('wp_insert_post','wbats_tag_posts');
	wp_set_post_tags( $postid,$keywords, $append );
}
	
add_action('admin_menu', 'wbats_admin');
add_action('init', 'wbats_init');
add_action('wp_insert_post','wbats_tag_posts',1);
add_action('shutdown', 'wbats_shutdown');

?>