<?php
$themename = "Simple Fast";
$shortname = str_replace(' ', '_', strtolower($themename));

function get_theme_option($option)
{
	global $shortname;
	return stripslashes(get_option($shortname . '_' . $option));
}

function get_theme_settings($option)
{
	return stripslashes(get_option($option));
}
$google_font = array("Arial","Bilbo","Henny+Penny","Tangerine","Abril+Fatface","Abel","Aclonica","Aldrich","Anonymous+Pro","Jolly+Lodger", "Emilys+Candy","Averia+Libre", "Lovers+Quarrel","Ribeye+Marrow", "Italiana",
"Judson", "Gorditas","Sirin+Stencil","Dynalight","Henny+Penny","Glass+Antiqua","Princess+Sofia",
"Bree+Serif","Comfortaa","Chau+Philomene+One","Russo+One","Oleo+Script","Cantata+One",
"Antic+Didone","Handlee","Iceberg","Tienne","Imprima","Sancreek","Italianno","Nova+Square","Oxygen","Days+One","Exo","Metamorphous",
"Poiret+One","Economica","Ruge Boogie","Holtwood+One+SC","Niconne","Rosarivo","Just+Another+Hand",
"Buenard","Lustria","Great+Vibes","Averia+Gruesa+Libre","Homenaje","Short+Stack","Trochut",);
$font_size = array("14","16","18","22","24","28","32","36","48","52","60","72");
$options = array (
array(	"name" => "1 - Colours Option","type" => "heading", ),
array(	"name" => "Theme Color",
"id" => $shortname."_color","std" => "323CC9","type" => "colorjs", ),
array(	"name" => "Font Size", "id" => $shortname."_font_size", "type" => "select", "std" => "18",
"options" => $font_size),
array(	"name" => "Choose Font , detail please visit google.com/webfonts", "id" => $shortname."_google_font", "type" => "select", "std" => "Arial",
"options" => $google_font),
array(	"name" => "</div></div>","type" => "close",),

array(	"name" => " 2 - Adsense For Mobile  setting", "type" => "heading", ),
array(	"name" => "Display Adsense For Mobile ? ","id" => $shortname."_mobile_ads_act1", "type" => "select",  "std" => "No",
"options" => array("No", "Yes")),
array(	"name" => "Input  Adsense For Mobile ",	"id" => $shortname."_mobile_ads1",  "type" => "textarea",  "std" => "", ),
array(	"name" => "</div></div>","type" => "close", ),

array(	"name" => " 3 - Ads 728 x 90  setting", "type" => "heading", ),
array(	"name" => "Display ads 728 x 90 ? ","id" => $shortname."_home_ads_act1", "type" => "select",  "std" => "No",
"options" => array("No", "Yes")),
array(	"name" => "Input  Ads size 728 x 90 ",	"id" => $shortname."_home_ads1",  "type" => "textarea",  "std" => "", ),
array(	"name" => "</div></div>","type" => "close", ),

array(	"name" => " 4 - Ads 336x 280 setting", "type" => "heading", ),
array(	"name" => "Display ads ? ","id" => $shortname."_home_ads_act2", "type" => "select",  "std" => "No",
"options" => array("No", "Yes")),
array(	"name" => "display advertisement text ? ", "id" => $shortname."_ads_act2", "type" => "select",    "std" => "No",
"options" => array("No", "Yes")),
array(	"name" => "Input  Ads code ",	"id" => $shortname."_home_ads2",  "type" => "textarea",  "std" => "", ),
array(	"name" => "</div></div>","type" => "close", ),

		
array(	"name" => " 5- Analytic setting ( Histats or google analytic )","type" => "heading",),
array(	"name" => "add your stat code ? ","id" => $shortname."_footer_ads_act1","type" => "select","std" => "No",
"options" => array("No", "Yes")),			
array(	"name" => "Input your stat code","id" => $shortname."_footer_ads1","type" => "textarea","std" => "", ),
array(	"name" => "</div></div>","type" => "close",),

);

function fastestwp_add_admin() {
        global $themename, $shortname, $options;
 
        if ( isset ( $_GET['page'] ) && ( $_GET['page'] == basename(__FILE__) ) ) {
 
                if ( isset ($_REQUEST['action']) && ( 'save' == $_REQUEST['action'] ) ) {
 
                        foreach ( $options as $value ) {
                                if ( array_key_exists('id', $value) ) {
                                        if ( isset( $_REQUEST[ $value['id'] ] ) ) {
                                                update_option( esc_html($value['id']), $_REQUEST[ $value['id'] ]  );
                                        } else {
                                                delete_option( $value['id'] );
                                        }
                                }
                        }
                        header("Location: admin.php?page=".basename(__FILE__)."&saved=true");
                }
 
                else if ( isset ($_REQUEST['action']) && ( 'reset' == $_REQUEST['action'] ) ) {
 
                        foreach ($options as $value) {
                                if ( array_key_exists('id', $value) ) {
                                        delete_option( $value['id'] );
                                }
                        }
                        header("Location: admin.php?page=".basename(__FILE__)."&reset=true");
                }
        }
 
        add_theme_page($themename." Options", $themename." Options", 'administrator', basename(__FILE__), 'fastestwp_admin');
        }
add_action('admin_init', 'fastestwp_add_css');
function fastestwp_add_css() {
$file_dir= get_template_directory_uri();
wp_enqueue_style("functions", $file_dir."/functions/function.css", false, "1.0", "all");
}
add_action('admin_head', 'fastestwp_admin_js');
function fastestwp_admin_js(){
if ( isset ( $_GET['page'] ) == basename(__FILE__) ) { ?>
	<script type="text/javascript" src="<?php echo get_template_directory_uri() ?>/js/jscolor.js"></script>
<?php }
}
add_action('admin_menu', 'fastestwp_add_admin');
function fastestwp_admin() {

    global $themename, $shortname, $options;

    if ( !empty ($_REQUEST['saved']) ) echo '<div id="message" class="updated fade"><p><strong>'.$themename.' settings saved.</strong></p></div>';
    if ( isset ( $_REQUEST['reset']) ) echo '<div id="message" class="updated fade"><p><strong>'.$themename.' settings reset.</strong></p></div>';
    
?>
<?php echo "<div id=\"function\"> ";?>
<h4><?php echo "$themename"; ?> Premium Wordpress Themes</h4>

<div class="opening">
<p>Themes Designed By <a href="http://fastestwp.com" target="_blank" rel="nofollow"><b>fastestwp.com</b></a> |  <a href="http://fastestwp.com/support" target="_blank" rel="nofollow"><b>support</b> </a> | <a href="http://fastestwp.com/themes" target="_blank" rel="nofollow"><b>fastest wordpress themes</b></a></p>
</div>

<form action="" method="post">

<?php foreach ($options as $value) { ?>

<?php switch ( $value['type'] ) { case 'heading': ?>

<div class="get-option">

<h2><?php echo $value['name']; ?></h2>

<div class="option-save">
<?php break;

case 'colorjs':
?>

<div class="description"><?php echo $value['name']; ?></div>
	<input style="width:200px;" name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>" value="<?php if ( get_option( $value['id'] ) != "") { echo stripslashes(get_option( $value['id'] )); } else { echo stripslashes($value['std']); } ?>" class="color" />
	<br/>
<?php
break;
case 'text':
?>

<div class="description"><?php echo $value['name']; ?></div>
<p><input name="<?php echo $value['id']; ?>" class="myfield" id="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>" value="<?php if (

get_option( $value['id'] ) != "") { echo get_option( $value['id'] ); } else { echo $value['std']; } ?>" /></p>

<?php
break;
case 'select':
?>

<div class="description"><?php echo $value['name']; ?></div>
<p><select name="<?php echo $value['id']; ?>" class="myselect" id="<?php echo $value['id']; ?>">
<?php foreach ($value['options'] as $option) { ?>
<option<?php if ( get_option( $value['id'] ) == $option) { echo ' selected="selected"'; } elseif ($option == $value['std']) { echo ' selected="selected"'; } ?>><?php echo $option; ?></option>
<?php } ?>
</select>
</p>

<?php
break;
case 'textarea':
$valuex = $value['id'];
$valuey = stripslashes($valuex);
$video_code = get_option($valuey);
?>

<div class="description"><?php echo $value['name']; ?></div>
<p><textarea name="<?php echo $valuey; ?>" class="mytext" cols="40%" rows="8" /><?php if ( get_option($valuey) != "") { echo stripslashes($video_code); }

else { echo $value['std']; } ?></textarea></p>

<?php
break;
case 'close':
?>

<div class="clearfix"></div>
</div><!-- OPTION SAVE END -->

<div class="clearfix"></div>
</div><!-- GET OPTION END -->

<?php
break;
default;
?>
<?php
break; } ?>

<?php } ?>

<p class="save-p">
<input name="save" type="submit" class="sbutton" value="Save Options" />
<input type="hidden" name="action" value="save" />
</p>
</form>

<form method="post">
<p class="save-p">
<input name="reset" type="submit" class="rbutton" value="Reset Options" />
<input type="hidden" name="action" value="reset" />
</p>
</form>

</div>
<?php } 
?>