<?php
/**
 * Template Name: Embeds
 *
 * @author Timo Klemm
 */
?>
<!DOCTYPE html>
<html class="svq-embed">
<head>
<title><?php bloginfo('name'); ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1" />
</head>
<?php $currentID = get_queried_object_id(); ?>
<body id="body" <?php post_class( 'svq-embed', $currentID ); ?>> 
<?php
//trigger video shortcode callback
echo wp_video_shortcode( array() );

wp_footer(); 
?> 
</body>
</html>
