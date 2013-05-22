<!DOCTYPE html>
<!--[if lt IE 9]>
<script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->
<html <?php language_attributes(); ?>>
<head>
<meta charset="utf-8">
<title> <?php if ( is_home() ) { ?><?php bloginfo('name'); ?> - <?php bloginfo('description'); } else 
{ ?><?php  wp_title(''); ?> - <?php bloginfo('name'); } ?></title>
<link href="<?php bloginfo('stylesheet_url'); ?>" rel="stylesheet" type="text/css" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="<?php echo get_template_directory_uri(); ?>/media.css" rel="stylesheet" type="text/css">
<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> RSS Feed" href="<?php bloginfo('rss2_url'); ?>" />
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
<link rel="Shortcut Icon" href="<?php echo get_template_directory_uri();?>/images/favicon.ico" type="image/x-icon" />
<?php wp_head(); ?>
</head>
<body <?php body_class(''); ?>> 
<div id="wrap">
<header id="header">
<a href="<?php echo home_url() ; ?>"><?php $header_image = get_header_image();
if ( ! empty( $header_image ) ) : ?>
<img src="<?php header_image(); ?>" width="<?php echo HEADER_IMAGE_WIDTH; ?>" height="<?php echo HEADER_IMAGE_HEIGHT; ?>" alt="<?php bloginfo('name'); ?>" title="<?php bloginfo('name'); ?>"/></a><?php else : ?><div class="logo"><?php bloginfo('name'); ?></a></div><div style="clear: both"></div><div class="desc"><?php bloginfo('description'); ?></div><?php endif;?>
</header>
<nav id="nav"><?php wp_nav_menu( array( 'theme_location' => 'main-menu', 'menu_class' => 'dropdown' , 'fallback_cb' => '' ) ); ?></nav>

<div style="clear: both"></div>
<div id="breadchumb">
<?php if (is_home()) { ?> <a href="<?php echo home_url(); ?>">home <?php print '&raquo;';?></a> <?php bloginfo('description');?><?php  if ( get_query_var('paged') ) { echo ' ('; echo __('page') . ' ' . get_query_var('paged');   echo ')';  } ?>
<?php } else {?><?php if (function_exists('fastestwp_breadcrumbs')) fastestwp_breadcrumbs(); ?>
<?php }?>
</div>
<?php
if (fastestwp_mobile()) { ?>
<?php $adsense_for_mobile = get_theme_option('mobile_ads_act1'); if(($adsense_for_mobile == '') || ($adsense_for_mobile == 'No')) { ?><?php } else { ?><div id="ads"><?php echo get_theme_option('mobile_ads1'); ?></div><?php } ?>
<?php } else { ?>
<div id="ads">
<?php $header_ads_act = get_theme_option('home_ads_act1'); if(($header_ads_act == '') || ($header_ads_act == 'No')) { ?><?php } else { ?><?php echo get_theme_option('home_ads1'); ?><?php } ?>
</div><?php }?>