<?php get_header(); ?>
<div style="clear: both"></div>
<div id="container">
<div id="contentimage">
<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
 <div class="post" id="post-<?php the_ID(); ?>">
<div class="posttitle"><h1><?php the_title(); ?></h1></div>
<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
<?php if ( wp_attachment_is_image() ) :	$attachments = array_values( get_children( array( 'post_parent' => $post->post_parent, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => 'ASC', 'orderby' => 'menu_order ID' ) ) ); ?>
<?php 	foreach ( $attachments as $k => $attachment ) { if ( $attachment->ID == $post->ID ) break; 	} $k++;
if ( count( $attachments ) > 1 ) { if ( isset( $attachments[ $k ] ) ) $next_attachment_url = get_attachment_link( $attachments[ $k ]->ID );  else $next_attachment_url = get_attachment_link( $attachments[ 0 ]->ID ); }  else { $next_attachment_url = wp_get_attachment_url(); }
?>
<div class="image"><a href="<?php echo $next_attachment_url; ?>" title="<?php echo esc_attr( get_the_title() ); ?>" rel="attachment">
<?php $fast_width  = apply_filters( 'wdt', 453 );  $fast_height = apply_filters( 'hgt', 453 );
echo wp_get_attachment_image( $post->ID, array( $fast_width, $fast_height ) ); 	?></a><?php if ( !empty( $post->post_excerpt ) ) the_excerpt(); ?>
<?php the_content(); ?>	<?php else : ?><?php endif; ?></div>
<?php endwhile; ?>
<div class="picnav">
<?php $post_parent = get_post($post->ID, ARRAY_A); $parent = $post_parent['post_parent'];
$attachments = get_children("post_parent=$parent&post_type=attachment&post_mime_type=image&orderby=menu_order ASC, ID ASC");
foreach($attachments as $id => $attachment) : ?><?php 	echo wp_get_attachment_link($id, 'thumbnail', true); ?><?php endforeach; ?></div>
<?php else : ?>
<article class="post"><h2>Not Found</h2>Sorry, but you are looking for something that isn't here.</article>
<?php endif; ?>
</div></div></div>
<?php get_footer(); ?>