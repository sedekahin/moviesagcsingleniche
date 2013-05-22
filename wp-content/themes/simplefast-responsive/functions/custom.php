<?php
require_once( dirname(__FILE__) . '../../../../wp-config.php');
require_once( dirname(__FILE__) . '/functions.php');
header("Content-type: text/css");
global $options;
foreach ($options as $value) {
if (get_option( $value['id'] ) === FALSE) { $$value['id'] = $value['std']; } else { $$value['id'] = get_option( $value['id'] ); } }
?>
a:link, a:visited {color:#<?php echo $tetap_semangat_color; ?>;}
.link a{color:#<?php echo $tetap_semangat_color; ?>;}
.tags a{color:#<?php echo $tetap_semangat_color; ?>;}
h2{color:#<?php echo $tetap_semangat_color; ?>;}
h3{color:#<?php echo $tetap_semangat_color; ?>;}
#sidebar a{color:#<?php echo $tetap_semangat_color; ?>;}