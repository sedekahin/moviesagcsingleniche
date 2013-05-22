<?php
/*
Plugin Name: StupidPie
Plugin URI: http://ninjaplugins.com/products/stupidpie
Description: StupidPie is a stupid content generator. RISK: your domain will be deindexed, your adsense account will be banned, your server will crash. Benefit: If done properly and you have a good facerank, it will generate tons of traffic. Should be done in massive amount of wordpress install. Blog which already have minimum 100/day from search engine is recomended. Please read README.txt for example usage.
Author: AjariAkuAdsene
Version: 1.6
Author URI: http://ninjaplugins.com
*/
define('SPP_PATH',  dirname(__FILE__));

add_action( 'admin_init',  'spp_enqueue_styles' );
add_action('admin_menu', 'spp_menus');


function spp_menus()
{
	add_menu_page( 'StupidPie', 'StupidPie', 'manage_options', 'spp', 'spp_show_doc', '', 4);
}

function spp_show_doc()
{
    include('view/docs.php');
}

function spp_enqueue_styles()
{
	wp_register_style('spp_bootstrap', plugins_url('bootstrap/css/bootstrap-wpadmin.css',__FILE__ ));
    wp_enqueue_style('spp_bootstrap');
    
    wp_register_style('spp_bootstrap_fix', plugins_url('bootstrap/css/bootstrap-wpadmin-fixes.css',__FILE__ ));
    wp_enqueue_style('spp_bootstrap_fix');
    
    wp_register_style('spp_style', plugins_url('style.css',__FILE__ ));
    wp_enqueue_style('spp_style');
    //wp_enqueue_script( 'spp_bootstrap_js_jquery', 'http://code.jquery.com/jquery-1.7.2.min.js' );
    wp_enqueue_script( 'spp_bootstrap_js', plugins_url('bootstrap/js/bootstrap.min.js', __FILE__) );
}

require_once('settings.php');

require_once('templates/h2o/h2o.php');

foreach (glob(SPP_PATH."/includes/*.php") as $filename) {
	require($filename); 
}

function spp($term = "", $template = 'default.html', $hack = ""){
    global $spp_settings;
    
    $result = new h2o(
        SPP_PATH."/templates/$template", 
        array(  
            'safeClass' => array('SimpleXMLElement','stdClass')
        ));
                
    return $result->render(array('term'=>$term, 'hack' => $hack, 'settings' => $spp_settings));
}

register_activation_hook(__FILE__,'spp_set_activation');
add_action('wp_head', 'spp_setinfo');
add_action('parse_query', 'spp_filter_query' );
add_action('init', 'spp_flush_rules');
add_action('generate_rewrite_rules', 'spp_add_rewrite_rules');
add_filter('query_vars', 'spp_query_vars');
add_filter('widget_text', 'do_shortcode');