<?php
/*
Plugin Name: Switch Video Quality
Description: Switch Video Quality adds quality switch functionality to the wordpress video player to let you choose between different resolutions of a (self-hosted) html5-compatible video.
Version: 1.6000
Author: Timo Klemm (team-ok)
Text Domain: switch-video-quality
Domain Path: /lang
License: GPLv3, mep-feature-sourcechooser.js by John Dyer is originally licensed under the MIT license, Font Awesome font is licensed under the SIL OFL 1.1., Font-Awesome-SVG-PNG is licensed under the MIT license
*/

register_activation_hook( __FILE__, 'prowp_install' );

function prowp_install() {
    global $wp_version;
 
    if ( version_compare( $wp_version, '3.6', '<' ) ) {
 
        wp_die( 'This plugin requires WordPress version 3.6 or higher.' );
 
    }
}

//Switch Video Quality Version Number
define( 'SVQ_VERSION', '1.6000' );

// SVQ Plugin Path
define( 'SVQ_PATH', plugin_dir_path( __FILE__ ) );

// SVQ Plugin URL 
define( 'SVQ_URL', plugin_dir_url( __FILE__ ) );


// includes
require_once SVQ_PATH . 'lib/svq_cpt.php';

require_once SVQ_PATH . 'lib/svq_admin.php';

require_once SVQ_PATH . 'lib/svq_save.php';

require_once SVQ_PATH . 'lib/svq_embeds.php';

require_once SVQ_PATH . 'lib/svq_html.php';



// load textdomain
function init_textdomain() {
	$domain = 'switch-video-quality';
    $locale = apply_filters('plugin_locale', get_locale(), $domain);
	$lang_dir = SVQ_PATH . '/lang/';
	//look for mo-file in default wordpress language folder first, then (if no mo-file with the (filtered) locale is found, look in plugins dir)
	load_textdomain($domain, WP_LANG_DIR.'/plugins/'.$domain.'-'.$locale.'.mo');
	load_plugin_textdomain($domain, false, $lang_dir );
}
add_action('init', 'init_textdomain');



// enqueue scripts
add_action( 'admin_enqueue_scripts', 'svq_admin_scripts' );
function svq_admin_scripts() {
	$post_type = get_post_type();
	$excluded = apply_filters( 'svq_exclude_post_types', $excluded = array('attachment', 'revision', 'nav_menu_item') );
	if ( in_array( $post_type, $excluded ) ){
		return;
	}
	wp_enqueue_media();
	wp_enqueue_script( 'svq_admin_js', SVQ_URL . 'js/svq_admin.js', array('jquery-ui-sortable', 'jquery-ui-accordion', 'jquery'), SVQ_VERSION, true );
	wp_enqueue_style( 'svq_admin_css', SVQ_URL . 'css/svq_admin.css', array(), SVQ_VERSION);
	wp_localize_script( 'svq_admin_js', 'svq_admin_l10n', array(
		'removeFields' => __('Remove Fields', 'switch-video-quality'),
		'reorderFields' => __('Reorder Fields', 'switch-video-quality'),
		'url' => __('URL', 'switch-video-quality'),
		'label' => __('Label', 'switch-video-quality'),
		'lang' => __('Language Tag', 'switch-video-quality'),
		'languageTags' => __('Find the right language tag', 'switch-video-quality'),
		'duration' => __('Duration', 'switch-video-quality'),
		'mmVideo' => __('Choose or upload video (multiple selection with cmd+click/ctrl+click)', 'switch-video-quality'),
		'mmImage' => __('Choose or upload image', 'switch-video-quality'),
		'mmSubtitle' => __('Choose or upload subtitle (multiple selection with cmd+click/ctrl+click)', 'switch-video-quality'),
		'urlError' => __('The url you have entered is invalid/unreachable or the file type is not supported by your browser.', 'switch-video-quality')
		) );
}


// Add metaboxes
add_action( 'load-post.php', 'switch_video_quality_settings' );
add_action( 'load-post-new.php', 'switch_video_quality_settings' );


// Filter and save data
add_action( 'save_post', 'svq_meta_save' );


// Iframe Embed Functionality
add_filter('query_vars', 'svq_embed_query_vars');
add_filter('template_include', 'svq_embeds_loader');


// SVQ Post Type
add_action( 'init', 'register_cpt_svq_video' );


// HTML and JSON Frontend Output
add_filter( 'wp_video_shortcode_override', 'svq_video_shortcode_output', 99, 4 );