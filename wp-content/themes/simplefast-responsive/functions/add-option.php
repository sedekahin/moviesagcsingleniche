<?php 
function fastestwp_sidebars() {
register_sidebar(array('name'=>'Sidebar',
'before_widget' => '<div class="box">', 
'after_widget' => '</div>', 
'before_title' => '<h4>', 
'after_title' => '</h4>', 
));
}

add_action( 'widgets_init', 'fastestwp_sidebars' );
?>
<?php 
function fastestwp_breadcrumbs() {
 
  $showOnHome = 0;
  $delimiter = '&raquo;';
  $home = 'Home';
  $showCurrent = 1;
  $before = '';
  $after = '';
 
  global $post;
  $homeLink = home_url();
 
  if (is_home() || is_front_page()) {
 
    if ($showOnHome == 1) echo '<a href="' . $homeLink . '">' . $home . '</a>';
 
  } else {
 
    echo '<a href="' . $homeLink . '">' . $home . '</a> ' . $delimiter . ' ';
 
    if ( is_category() ) {
      global $wp_query;
      $cat_obj = $wp_query->get_queried_object();
      $thisCat = $cat_obj->term_id;
      $thisCat = get_category($thisCat);
      $parentCat = get_category($thisCat->parent);
      if ($thisCat->parent != 0) echo(get_category_parents($parentCat, TRUE, ' ' . $delimiter . ' '));
      echo $before . 'Archive by category "' . single_cat_title('', false) . '"' . $after;
 
    } elseif ( is_day() ) {
      echo '<a href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a> ' . $delimiter . ' ';
      echo '<a href="' . get_month_link(get_the_time('Y'),get_the_time('m')) . '">' . get_the_time('F') . '</a> ' . $delimiter . ' ';
      echo $before . get_the_time('d') . $after;
 
    } elseif ( is_month() ) {
      echo '<a href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a> ' . $delimiter . ' ';
      echo $before . get_the_time('F') . $after;
 
    } elseif ( is_year() ) {
      echo $before . get_the_time('Y') . $after;
 
    } elseif ( is_single() && !is_attachment() ) {
      if ( get_post_type() != 'post' ) {
        $post_type = get_post_type_object(get_post_type());
        $slug = $post_type->rewrite;
        echo '<a href="' . $homeLink . '/' . $slug['slug'] . '/">' . $post_type->labels->singular_name . '</a> ' . $delimiter . ' ';
        if ($showCurrent == 1) echo $before . get_the_title() . $after;
      } else {
        $cat = get_the_category(); $cat = $cat[0];
        echo get_category_parents($cat, TRUE, ' ' . $delimiter . ' ');
        if ($showCurrent == 1) echo $before . get_the_title() . $after;
      }
 
    } elseif ( !is_single() && !is_page() && get_post_type() != 'post' && !is_404() ) {
      $post_type = get_post_type_object(get_post_type());
      echo $before . $post_type->labels->singular_name . $after;
 
    } elseif ( is_attachment() ) {
      $parent = get_post($post->post_parent);
      $cat = get_the_category($parent->ID); $cat = $cat[0];
      echo get_category_parents($cat, TRUE, ' ' . $delimiter . ' ');
      echo '<a href="' . get_permalink($parent) . '">' . $parent->post_title . '</a> ' . $delimiter . ' ';
      if ($showCurrent == 1) echo $before . get_the_title() . $after;
 
    } elseif ( is_page() && !$post->post_parent ) {
      if ($showCurrent == 1) echo $before . get_the_title() . $after;
 
    } elseif ( is_page() && $post->post_parent ) {
      $parent_id  = $post->post_parent;
      $breadcrumbs = array();
      while ($parent_id) {
        $page = get_page($parent_id);
        $breadcrumbs[] = '<a href="' . get_permalink($page->ID) . '">' . get_the_title($page->ID) . '</a>';
        $parent_id  = $page->post_parent;
      }
      $breadcrumbs = array_reverse($breadcrumbs);
      foreach ($breadcrumbs as $crumb) echo $crumb . ' ' . $delimiter . ' ';
      if ($showCurrent == 1) echo $before . get_the_title() . $after;
 
    } elseif ( is_search() ) {
      echo $before . 'Search results for "' . get_search_query() . '"' . $after;
 
    } elseif ( is_tag() ) {
      echo $before . 'Posts tagged "' . single_tag_title('', false) . '"' . $after;
 
    } elseif ( is_author() ) {
       global $author;
      $userdata = get_userdata($author);
      echo $before . 'Articles posted by ' . $userdata->display_name . $after;
 
    } elseif ( is_404() ) {
      echo $before . 'Error 404' . $after;
    }
 
    if ( get_query_var('paged') ) {
      if ( is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() ) echo ' (';
      echo __('Page') . ' ' . get_query_var('paged');
      if ( is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() ) echo ')';
    }
 
    echo '';
 
  }
} 
?>
<?php 
function fastestwp_pagenavi($before = '', $after = '', $prelabel = '', $nxtlabel = '', $pages_to_show = 6, $always_show = false) {
	global $request, $posts_per_page, $wpdb, $paged;
	if(empty($prelabel)) {
		$prelabel  = '<strong>&laquo;</strong>';
	}
	if(empty($nxtlabel)) {
		$nxtlabel = '<strong>&raquo;</strong>';
	}
	$half_pages_to_show = round($pages_to_show/2);
	if (!is_single()) {
		if(!is_category()) {
			preg_match('#FROM\s(.*)\sORDER BY#siU', $request, $matches);
		} else {
			preg_match('#FROM\s(.*)\sGROUP BY#siU', $request, $matches);
		}
		$fromwhere = $matches[1];
		$numposts = $wpdb->get_var("SELECT COUNT(DISTINCT ID) FROM $fromwhere");
		$max_page = ceil($numposts /$posts_per_page);
		if(empty($paged)) {
			$paged = 1;
		}
		if($max_page > 1 || $always_show) {
			echo "$before <div class=\"wp-pagenavi\"><span class=\"pages\">Page $paged of $max_page:</span>";
			if ($paged >= ($pages_to_show-1)) {
				echo '<a href="'.get_pagenum_link().'">&laquo; First</a>&nbsp;';
			}
			previous_posts_link($prelabel);
			for($i = $paged - $half_pages_to_show; $i  <= $paged + $half_pages_to_show; $i++) {
				if ($i >= 1 && $i <= $max_page) {
					if($i == $paged) {
						echo "<strong class='current'>$i</strong>";
					} else {
						echo ' <a href="'.get_pagenum_link($i).'">'.$i.'</a> ';
					}
				}
			}
			next_posts_link($nxtlabel, $max_page);
			if (($paged+$half_pages_to_show) < ($max_page)) {
				echo '&nbsp;<a href="'.get_pagenum_link($max_page).'">Last &raquo;</a>';
			}
			echo "</div> $after";
		}
	}
}
?>
<?php
function fastestwp_excerpt($limit) {
  $excerpt = explode(' ', get_the_excerpt(), $limit);
  if (count($excerpt)>=$limit) {
    array_pop($excerpt);
    $excerpt = implode(" ",$excerpt).'';
  } else {
    $excerpt = implode(" ",$excerpt);
  }	
  $excerpt = preg_replace('`\[[^\]]*\]`','',$excerpt);
  return $excerpt;
}
?>
<?php
function fastestwp_first_image() {
  global $post, $posts;
  $first_img = '';
  ob_start();
  ob_end_clean();
  $output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches);
  $first_img = $matches [1] [0];

  if(empty($first_img)){ 
  	$img_dir = get_template_directory_uri();
    $first_img = $img_dir . '/images/thumb.gif';
  }
  return $first_img;
}
?>
<?php
function fastestwp_latest_post() { ?>
  <div class="box"><h4>Recent Post</h4><?php get_template_part( 'recentpost' ); ?></div>
<?php
}
wp_register_sidebar_widget(
    'fastestwp_latest_post_1', 
    'fastestwp latest post', 
    'fastestwp_latest_post',
    array( 
        'description' => 'Latest Post With Thumb Widget'
    )
);
?>
<?php
function fastestwp_random_post() { ?>
  <div class="box"><h4>Random Post</h4><?php get_template_part( 'related-sb' ); ?></div>
  <?php
}
wp_register_sidebar_widget(
    'fastestwp_random_post_2',
    'fastestwp random post',
    'fastestwp_random_post',
    array( 
        'description' => 'Random Post With Thumb Widget'
    )
);
?>
<?php
add_filter( 'the_category', 'add_nofollow_cat' ); 
function add_nofollow_cat( $text ) {
$text = str_replace('rel="category tag"', "", $text); return $text;
}
?>
<?php
if ( !function_exists('fastestwp_custom_styles') ) {
	function fastestwp_custom_styles() {
           $color = get_theme_option('color');
           $font_size = get_theme_option('font_size');
		   $google_font = get_theme_option('google_font');
		?>

<style>
a:link, a:visited {color:#<?php echo $color; ?>;}.link a{color:#<?php echo $color; ?>;}.tags a{color:#<?php echo $color; ?>;}h2{color:#<?php echo $color; ?>;}h3{color:#<?php echo $color; ?>;}
#sidebar a{color:#<?php echo $color; ?>;}h1 { font-family: '<?php echo str_replace('+', ' ',$google_font); ?>';font-size: <?php echo $font_size; ?>px;}
</style>
<?php
}
}
add_action('wp_head', 'fastestwp_custom_styles');
?>
<?php
function fastestwp_enqueue_scripts() {
    wp_deregister_script( 'jquery' );
	wp_enqueue_script( 'jquery-lib', 'http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js', 'jquery', '1.7.2', false );
	wp_enqueue_script( 'modernizr', get_template_directory_uri() . '/js/modernizr-2.5.3.min.js', 'jquery', '2.5.3', false );
	wp_enqueue_script( 'nav', get_template_directory_uri() . '/js/js-nav.js', 'jquery', '2.5.3', true );
    wp_enqueue_script( 'jquery' );
}    
add_action('wp_enqueue_scripts', 'fastestwp_enqueue_scripts');
?>
<?php
function fastestwp_footer_script() { ?>
<?php $footer_ads_act = get_theme_option('footer_ads_act1'); if(($footer_ads_act == '') || ($footer_ads_act == 'No')) { ?>
<a href="http://fastestwp.com" target="_blank">Adsense Themes</a><?php } else { ?><?php echo get_theme_option('footer_ads1'); ?><?php } ?>
<?php } 
add_action('wp_footer', 'fastestwp_footer_script');
remove_action('wp_head', 'wp_generator');
?>
<?php
function fastestwp_google_fonts() {
	wp_register_style( 'fastestwp-google_fonts', 'http://fonts.googleapis.com/css?family='.get_theme_option('google_font').'', false, 1.0, 'screen' );
	wp_enqueue_style( 'fastestwp-google_fonts' );
}add_action( 'wp_enqueue_scripts', 'fastestwp_google_fonts' );
?>
<?php
function fastestwp_admin_header_style() {
?>
	<style type="text/css">
	.appearance_page_custom-header #headimg {
		border: none;
	}
	#headimg h1 {
font-size:28px;font-family:Impact;font-weight:normal;float:left;text-align:left;padding-left:10px;float:left;background-position:left;padding-top:0px;clear:both;width:100%;text-transform:uppercase;
		word-wrap: break-word;
	}
	#headimg h1 a {
		border-bottom: none;
		color: #222;
		text-decoration: none;
	}
	#desc {
		font-size:14px;font-family:Verdana, Arial, Helvetica, sans-serif;padding-left:10px;
		width: 91.48936170212766%;
	}
		#headimg img {
		clear: both;
		float: left;
		max-width: 100%;
	}
	</style>
<?php
}
?>
<?php
if ( function_exists( 'get_custom_header' ) )
		add_theme_support( 'custom-background' );
	else
		add_custom_background(); 
 $header_args = array(
 'flex-height' => true,
 'height' => 90,
 'flex-width' => true,
 'width' => 728,
 'default-image' => '%s/images/logo.png',
 'admin-head-callback' => 'fastestwp_admin_header_style',
 );
 if ( function_exists( 'get_custom_header' ) ) {
		add_theme_support( 'custom-header', $header_args );
 } else 
	 {
		define( 'HEADER_TEXTCOLOR',    $header_args['default-text-color'] );
		define( 'HEADER_IMAGE',        $header_args['default-image'] );
		define( 'HEADER_IMAGE_WIDTH',  $header_args['width'] );
		define( 'HEADER_IMAGE_HEIGHT', $header_args['height'] );
		add_custom_image_header( $header_args['wp-head-callback'], $header_args['admin-head-callback'], $header_args['admin-preview-callback'] );
	}
 $defaults = array(
'default-image' => '',
'random-default' => false,
'width' => 0,
'height' => 0,
'flex-height' => false,
'flex-width' => false,
'default-text-color' => '',
'header-text' => true,
'uploads' => true,
'wp-head-callback' => '',
'admin-head-callback' => '',
'admin-preview-callback' => '',
);
?>
<?php
function fastestwp_googleplusone() {
echo '<script type="text/javascript" src="http://apis.google.com/js/plusone.js"></script>';
}
add_action('wp_footer', 'fastestwp_googleplusone');
?>