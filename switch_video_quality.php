<?php
/*
Plugin Name: Switch Video Quality
Description: Switch Video Quality adds quality switch functionality to the wordpress video player to let you choose between different resolutions of a (self-hosted) html5-compatible video.
Version: 1.3
Author: Timo Klemm (team-ok)
Text Domain: switch-video-quality
Domain Path: /lang
License: GPLv3, mep-feature-sourcechooser.js by John Dyer is originally licensed under the MIT license, Font Awesome font is licensed under the SIL OFL 1.1., Font-Awesome-SVG-PNG is licensed under the MIT license
*/

/************************************************************
************************Setup********************************
************************************************************/

register_activation_hook( __FILE__, 'prowp_install' );

function prowp_install() {
    global $wp_version;
 
    if ( version_compare( $wp_version, '3.6', '<' ) ) {
 
        wp_die( 'This plugin requires WordPress version 3.6 or higher.' );
 
    }
}
//Switch Video Quality Version Number
define( 'SVQ_VERSION', '1.3' );

add_action( 'load-post.php', 'switch_video_quality_settings' );
add_action( 'load-post-new.php', 'switch_video_quality_settings' );

function init_textdomain() {
	$domain = 'switch-video-quality';
    $locale = apply_filters('plugin_locale', get_locale(), $domain);
	$lang_dir = dirname( plugin_basename(__FILE__) ) . '/lang/';
	//look for mo-file in default wordpress language folder first, then (if no mo-file with the (filtered) locale is found, look in plugins dir)
	load_textdomain($domain, WP_LANG_DIR.'/plugins/'.$domain.'-'.$locale.'.mo');
	load_plugin_textdomain($domain, false, $lang_dir );
}
add_action('init', 'init_textdomain');

function switch_video_quality_settings() {
	add_action( 'add_meta_boxes', 'switch_video_quality_box' );
}
function switch_video_quality_box() {
	$post_types = get_post_types( array( 'public' => true ) );
	$excluded = apply_filters( 'svq_exclude_post_types', $excluded = array('attachment', 'revision', 'nav_menu_item') );
	foreach ( $post_types as $post_type ) {
		if ( in_array($post_type, $excluded) ){
			continue;
		}
		add_action( 'admin_enqueue_scripts', 'svq_admin_scripts' );
		add_meta_box( 'svq_settings', 'Switch Video Quality', 'print_svq_box', $post_type, 'normal', 'high');
	}
}
function svq_admin_scripts() {
	wp_enqueue_script( 'svq_admin_js', plugins_url( 'js/svq_admin.js', __FILE__ ), array('jquery-ui-sortable', 'jquery-ui-accordion', 'jquery'), SVQ_VERSION, true );
	wp_enqueue_style( 'svq_admin_css', plugins_url('css/svq_admin.css',  __FILE__), array(), SVQ_VERSION);
	wp_localize_script( 'svq_admin_js', 'svq_admin_l10n', array(
		'removeFields' => __('Remove Fields', 'switch-video-quality'),
		'reorderFields' => __('Reorder Fields', 'switch-video-quality'),
		'url' => __('URL', 'switch-video-quality'),
		'label' => __('Label', 'switch-video-quality'),
		'duration' => __('Duration', 'switch-video-quality'),
		'mmVideo' => __('Choose or upload video (multiple selection with cmd+click/ctrl+click)', 'switch-video-quality'),
		'mmImage' => __('Choose or upload image', 'switch-video-quality'),
		'urlError' => __('The url you have entered is invalid/unreachable or the file type is not supported by your browser.', 'switch-video-quality')
		) );
}

/************************************************************
******************HTML Output for Metaboxes******************
************************************************************/

function svq_box_html($playlist_number, $cnt, $svq) { ?>
	<div class="svq_metabox">
		<div class="svq_metabox_header">
			<span class="svq_playlist_position svq_handle" title="<?php _e('Drag &amp; Drop playlist item', 'switch-video-quality'); ?>" data-position="<?php echo $playlist_number ?>"><?php echo (intval($playlist_number) < 10 ? '0'.$playlist_number : $playlist_number); ?></span>
			<span class="svq_accordion">
				<span class="dashicons dashicons-arrow-right"></span>
				<span class="svq_accordion_label"><?php if (isset($svq['svq_title'])) echo esc_html($svq['svq_title']); ?></span>
			</span>
			<span class="add_svq_item dashicons-plus-alt dashicons" title="<?php _e('Insert empty box after this one', 'switch-video-quality'); ?>"></span>
			<span class="clear_svq_item dashicons-editor-removeformatting dashicons" title="<?php _e('Remove all entries of this playlist item', 'switch-video-quality'); ?>"></span>
			<span class="remove_svq_item dashicons-dismiss dashicons" title="<?php _e('Remove playlist item completely', 'switch-video-quality'); ?>"></span>
		</div>
		<div class="svq_metabox_content inside">
			<div class="svq_title svq_input_wrap svq_clearfix">
				<p><?php _e('Set title', 'switch-video-quality'); ?></p>
				<div>
					<label><?php _e('Title', 'switch-video-quality'); ?>
					1 <input class="svq_title_1" type="text" name="svq[<?php echo $cnt ?>][svq_title]" value="<?php if (isset($svq['svq_title'])) echo esc_html($svq['svq_title']); ?>" size="30" />
					</label>
				</div>
				<div>
					<label><?php _e('Title', 'switch-video-quality'); ?>
					2 <input class="svq_title_2" type="text" name="svq[<?php echo $cnt ?>][svq_title2]" value="<?php if (isset($svq['svq_title2'])) echo esc_html($svq['svq_title2']); ?>" size="30" />
					</label>
				</div>
				<div>
					<label><?php _e('Title', 'switch-video-quality'); ?>
					3 <input class="svq_title_3" type="text" name="svq[<?php echo $cnt ?>][svq_title3]" value="<?php if (isset($svq['svq_title3'])) echo esc_html($svq['svq_title3']); ?>" size="30" />
					</label>
				</div>
			</div>
			<div class="svq_ext_link svq_input_wrap">
				<p><?php _e('Set external link', 'switch-video-quality'); ?></p>
				<div>
					<label><?php _e('URL', 'switch-video-quality'); ?>
						<input class="link_url_input" type="text" name="svq[<?php echo $cnt ?>][svq_ext_link][url]" value="<?php if (isset( $svq['svq_ext_link']['url'] ) ) echo esc_url($svq['svq_ext_link']['url']) ?>" size="50" />
					</label>
				</div>
				<div>
					<label><?php _e('Text', 'switch-video-quality'); ?>
						<input class="link_text_input" type="text" name="svq[<?php echo $cnt ?>][svq_ext_link][text]" value="<?php if (isset( $svq['svq_ext_link']['text'] ) ) echo esc_html($svq['svq_ext_link']['text']) ?>" size="30" />
					</label>
				</div>
			</div>
			<div class="svq_poster svq_input_wrap svq_clearfix">
				<p><?php _e('Set poster image', 'switch-video-quality'); ?></p>
				<div>
					<input type="button" class="button svq_button_image" value="<?php _e('Choose/upload image', 'switch-video-quality'); ?>" title="<?php _e('Open the media manager', 'switch-video-quality'); ?>" />
					<?php if (!empty( $svq['svq_poster'] ) ) { ?>
						<div class="svq_poster_thumb">
							<br />
							<img src="<?php echo esc_url($svq['svq_poster']); ?>">
						</div>
					<?php } ?>
					<br />
					<label><?php _e('URL', 'switch-video-quality'); ?>
						<input class="img_url_input" type="text" name="svq[<?php echo $cnt ?>][svq_poster]" value="<?php if (isset( $svq['svq_poster'] ) ) echo esc_url($svq['svq_poster']) ?>" size="100" />
					</label>
				</div>
			</div>
			<div class="svq_video svq_input_wrap svq_clearfix">
				<p><?php _e('Set video details', 'switch-video-quality'); ?></p>
				<input type="button" class="button svq_choose_vid" value="<?php _e('Choose/upload videos', 'switch-video-quality'); ?>" title="<?php _e('Open the media manager', 'switch-video-quality'); ?>" />
				<input type="button" class="button svq_vid_manual" value="<?php _e('Add input field', 'switch-video-quality'); ?>" title="<?php _e('Adds empty input field to set video url and label manually', 'switch-video-quality'); ?>" />
				<?php $i = 0;
					if (!empty( $svq['svq_video'] ) ) {
						foreach ($svq['svq_video'] as $svq_video) { ?>
							<div class="svq_video_qualities">
								<span class="clear_video_input" title="<?php _e('Remove fields', 'switch-video-quality'); ?>"></span>
								<br />
								<div>
									<label><?php _e('URL', 'switch-video-quality'); ?>
										<input class="video_url_input" type="text" name="svq[<?php echo $cnt ?>][svq_video][<?php echo $i; ?>][svq_url]" value="<?php echo (!empty($svq_video['svq_url']) ? esc_url($svq_video['svq_url']) : ''); ?>" size="80" />
									</label>
								</div>
								<div>
									<label><?php _e('Label', 'switch-video-quality'); ?>
										<input class="video_quality_label" type="text" name="svq[<?php echo $cnt ?>][svq_video][<?php echo $i; ?>][svq_label]" value="<?php echo (!empty($svq_video['svq_label']) ? esc_html($svq_video['svq_label']) : ''); ?>" size="5" />
									</label>
								</div>
								<div>
									<label><?php _e('Duration', 'switch-video-quality'); ?>
										<input class="video_quality_duration" type="text" name="svq[<?php echo $cnt ?>][svq_video][<?php echo $i; ?>][svq_length]" value="<?php echo (!empty($svq_video['svq_length']) ? $svq_video['svq_length'] : ''); ?>" size="8" />
									</label>
								</div>
								<input class="video_quality_mime" type="hidden" name="svq[<?php echo $cnt ?>][svq_video][<?php echo $i; ?>][svq_mime]" value="<?php echo (!empty($svq_video['svq_mime']) ? esc_attr($svq_video['svq_mime']) : ''); ?>" />
								<input class="video_quality_order" type="hidden" name="svq[<?php echo $cnt ?>][svq_video][<?php echo $i; ?>][svq_order]" value="<?php echo (!empty($svq_video['svq_order']) ? esc_attr($svq_video['svq_order']) : ''); ?>" />
							</div>
						<?php $i++;
						}
					} ?>
			</div>
		</div>
	</div>
<?php }

function print_svq_box( $post ) {
	wp_nonce_field( basename( __FILE__ ), 'svq_box_nonce' );
	$svq_stored_metadata = get_post_meta( $post->ID, 'svq_metadata', true );
	$svq_custom_html = get_post_meta( $post->ID, 'svq_custom_html', true ); 
	$svq_options = get_post_meta( $post->ID, 'svq_options', true);
	$svq_options['show_svq_infooverlay'] = (isset($svq_options['show_svq_infooverlay']) ? $svq_options['show_svq_infooverlay'] : 'off');
	$svq_options['svq_embed_active'] = (isset($svq_options['svq_embed_active']) ? $svq_options['svq_embed_active'] : false);
	$svq_options['svq_active'] = (isset($svq_options['svq_active']) ? $svq_options['svq_active'] : 'off');
	$svq_options['svq_sort_qualities'] = (isset($svq_options['svq_sort_qualities']) ? $svq_options['svq_sort_qualities'] : 'desc');
	?>

	<div id="svq_options" class="svq_input_wrap">
		<p><?php _e('Set general options', 'switch-video-quality'); ?></p>
		<div>
			<input id="svq_active_toggle" type="checkbox" name="svq_options[svq_active]" value="on" <?php checked( $svq_options['svq_active'], 'on'); ?> />
			<label for="svq_active_toggle"><?php _e('Turn on/off', 'switch-video-quality'); ?></label>
		</div>
		<br />
		<div>
			<input id="svq_infooverlay_toggle" type="checkbox" name="svq_options[show_svq_infooverlay]" value="on" <?php checked( $svq_options['show_svq_infooverlay'], 'on'); ?> />
			<label for="svq_infooverlay_toggle"><?php _e('Show info-overlay in the player', 'switch-video-quality'); ?></label>
		</div>
		<br />
		<div>
			<input id="svq_embed_active_toggle" type="checkbox" name="svq_options[svq_embed_active]" value="on" <?php checked( $svq_options['svq_embed_active'], 'on'); ?> />
			<label for="svq_embed_active_toggle"><?php _e('Enable iframe embed functionality', 'switch-video-quality'); ?></label>
		</div>
		<br />
		<div>
			<span><?php _e('Sort quality levels:', 'switch-video-quality'); ?></span>
			<label><?php _e('ascending', 'switch-video-quality'); ?>
			<input id="svq_sort_qualities_asc" type="radio" name="svq_options[svq_sort_qualities]" value="asc" <?php checked( $svq_options['svq_sort_qualities'], 'asc'); ?> />
			</label>
			<label><?php _e('descending', 'switch-video-quality'); ?>
			<input id="svq_sort_qualities_desc" type="radio" name="svq_options[svq_sort_qualities]" value="desc" <?php checked( $svq_options['svq_sort_qualities'], 'desc'); ?> />
			</label>
		</div>
	</div>
	<div id="svq_custom_html" class="svq_input_wrap">
			<label for="svq_custom_text"><?php _e('Custom HTML (inserted between Player and Playlist)', 'switch-video-quality'); ?></label>
			<br />
			<textarea id="svq_custom_text" cols="40" rows="3" name="svq_custom_html"><?php if (!empty($svq_custom_html)) echo $svq_custom_html; ?></textarea>
	</div>
	<div id="svq_metabox_container">
	<?php 
	$playlist_number = 1;
	$cnt = 0;
	if (!empty($svq_stored_metadata)) {
		foreach ($svq_stored_metadata as $svq) {
				svq_box_html($playlist_number, $cnt, $svq);
				$playlist_number++;
				$cnt++;
		}
	} else {
		$svq = array();
		svq_box_html($playlist_number, $cnt, $svq);
	}
	?>
	</div>
<?php }

/************************************************************
****************Filter and save data*************************
************************************************************/

function omit_empty_items($input) {
    // If it is an element, then just return it
    if (!is_array($input)) {
      return trim($input);
    }
    $non_empty_items = array();
    $i = 0;
    foreach ($input as $key => $value) {
      // Ignore empty cells
    	if (!empty($value)) {
	      	// Use recursion to evaluate cells
	      	$omitted = omit_empty_items($value);
	      	if (!empty($omitted)) {
		      	if (is_numeric($key)) {
		      		// if the input array's key is a number
		      		$non_empty_items[$i] = $omitted;
		      		$i++;
		      	} else {
		      		// if the input array's key is a string
		      		$non_empty_items[$key] = $omitted;
		      	}
		    }
    	}
    }
    // Finally return the array without empty items and in gapless sequential order
    	return $non_empty_items;
}

function svq_meta_save( $post_id ) {
    // Checks save status
    $is_autosave = wp_is_post_autosave( $post_id );
    $is_revision = wp_is_post_revision( $post_id );
    $is_valid_nonce = ( isset( $_POST[ 'svq_box_nonce' ] ) && wp_verify_nonce( $_POST[ 'svq_box_nonce' ], basename( __FILE__ ) ) ) ? 'true' : 'false';
 
    // Exits script depending on save status
    if ( $is_autosave || $is_revision || !$is_valid_nonce ) {
        return;
    }
    // Check the user's permissions.
    if ( !current_user_can( 'edit_post', $post_id ) ) {
    	return;
    }
   	$svq_metadata = (!empty($_POST['svq'])) ? omit_empty_items($_POST['svq']) : '';
	(!empty($svq_metadata)) ? update_post_meta($post_id, 'svq_metadata', $svq_metadata) : delete_post_meta( $post_id, 'svq_metadata');
	(!empty($_POST['svq_custom_html'])) ? update_post_meta($post_id, 'svq_custom_html', $_POST['svq_custom_html']) : delete_post_meta( $post_id, 'svq_custom_html');
	(!empty($_POST['svq_options'])) ? update_post_meta($post_id, 'svq_options', $_POST['svq_options']) : delete_post_meta( $post_id, 'svq_options');
}
add_action( 'save_post', 'svq_meta_save' );


/************************************************************
*********************Iframe Embed Functionality***************
************************************************************/

add_filter('query_vars', 'svq_embed_query_vars');
function svq_embed_query_vars($vars){
	$vars[] = 'svq_embed_id';
	$vars[] = 'svq_index';
	$vars[] = 'svq_width';
	return $vars;
}

add_filter('template_include', 'svq_embeds_loader');
function svq_embeds_loader($template){

	//use embed template when query var svq_embed_id exists
	$svq_embed_id = get_query_var('svq_embed_id');

	if (!empty($svq_embed_id)){

		$default_path = plugin_dir_path( __FILE__ ) . 'template/'; // path to the plugin's template folder
		$template_name = 'svq_embeds.php';

		// Search template file in child-theme and theme directory first. Filename has to be 'svq_embeds.php'
		$template = locate_template( array($template_name) );

		//full template path (not url) can be overwritten with a filter
		$template = apply_filters( 'svq_locate_template', $template );

		// Get plugins template file when custom template can't be found
		if ( ! $template || ! file_exists($template) ){
			$template = $default_path . $template_name;
		}
	}
	return $template;
}

/************************************************************
****************HTML and JSON Frontend Output****************
************************************************************/

add_filter( 'wp_video_shortcode_override', 'svq_video_shortcode_output', 10, 4 );
function svq_video_shortcode_output($output, $attr, $content, $instance){
	global $content_width;
	//check if current query is an embed (= url with 'svq_embed_id' query var)
	$svq_post_id = get_query_var('svq_embed_id');
	$svq_index = 0;
	if ( !empty($svq_post_id) ){
		$svq_index = get_query_var('svq_index');
		$svq_width = get_query_var('svq_width');
		$is_svq_embed = true;
	} else {
		$svq_post_id = get_the_ID();
		$is_svq_embed = false;
	}

	$svq_options = get_post_meta( $svq_post_id, 'svq_options', true );
	$svq_options['svq_embed_active'] = (isset($svq_options['svq_embed_active']) ? $svq_options['svq_embed_active'] : false);
	$svq_options['svq_embed_active'] = apply_filters( 'svq_embed_setting_overwrite', $svq_options['svq_embed_active'], $svq_post_id );

	if (isset($svq_options['svq_active']) ) {
		// exit if embed template is used, but embed functionality is not enabled for the queried post
		if ( $is_svq_embed && !$svq_options['svq_embed_active'] ){
			return false;
		}
		static $svq_instance = 0;
		static $def_bg;
		static $def_qual;
		static $embed_js = 0;
		static $playlist_js = 0;
		static $info_js = 0;

		$svq_instance++;
		$svq_stored_metadata = get_post_meta( $svq_post_id, 'svq_metadata', true );
		if ($is_svq_embed){
			$svq_stored_metadata = array($svq_index => $svq_stored_metadata[$svq_index]);
		}
		$svq_custom_html = get_post_meta( $svq_post_id, 'svq_custom_html', true );
		if ($svq_instance === 1){
			$def_bg = esc_url( apply_filters( 'svq_default_background', plugins_url('img/svq_def_bg.png',  __FILE__) ) );
			$def_qual = apply_filters( 'svq_default_quality', null );
		}
		//generate embed urls
		$embed_urls = array();
		if ( !$is_svq_embed && $svq_options['svq_embed_active'] ){
			$base_url = esc_url(trailingslashit( home_url(apply_filters( 'svq_embed_page_name', '' ) ) ) );
			for ($i=0; $i < count($svq_stored_metadata); $i++) { 
				//push embed url into array
				$embed_urls[] = add_query_arg( array('svq_embed_id' => $svq_post_id, 'svq_index' => $i ), $base_url );
			}
		}

		$defaults_atts = array(
			'src'      => '',
			'poster'   => '',
			'loop'     => '',
			'autoplay' => '',
			'preload'  => 'metadata',
			'width'    => 640,
			'height'   => '',
			'class'    => '',
		);
		$atts = shortcode_atts( $defaults_atts, $attr, 'video' );

		if ( !empty($svq_stored_metadata[$svq_index]['svq_poster']) ) {
			$atts['poster'] = $svq_stored_metadata[$svq_index]['svq_poster'];
		}
		if (!empty($svq_width)){
			$atts['width'] = $svq_width;
		}
		// if the height attribute is missing, set it automatically (assuming the video has a 16:9 aspect ratio)
		if ( empty($atts['height']) ){
			$atts['height'] = round($atts['width'] * 0.5625);
		}

		// if the video is bigger than the theme
		if ( !$is_svq_embed && !empty( $content_width ) && $atts['width'] > $content_width ) {
			$atts['height'] = round( ( $atts['height'] * $content_width ) / $atts['width'] );
			$atts['width'] = $content_width;
		}
		// find the quality that is closest to the player's height, but not smaller than it (to avoid upscaling)
		// and set it as default
		foreach ($svq_stored_metadata as &$svq){
			$reference = ($def_qual !== null ? absint($def_qual) : $atts['height']);
  			$init_qual = null;
  			$init_index = 0;
  			if ( !empty($svq['svq_video']) ){
				foreach ($svq['svq_video'] as $key => $quality){
					//skip if sorting criteria is missing (for some reason)
					//should actually reflect the height of the video file
					if ( empty($quality['svq_order']) ){
						continue;
					}
					// set the first file as initial quality
					if ($init_qual === null
						// or if the initial file's is smaller than the player's height and the next file is greater than it, set the next file as initial quality
						|| ( $init_qual < $reference && $reference <= $quality['svq_order'] )
						// or if the next file's height is closer to and not smaller than the player's height, set it as initial quality
						|| ( abs( $reference - $init_qual ) > abs( $quality['svq_order'] - $reference ) && $quality['svq_order'] >= $reference ) 
					) {
		    			$init_qual = $quality['svq_order'];
	        			$init_index = $key;
		    		}
				}
				if ($init_index > 0){
		  			$out = array_splice($svq['svq_video'], $init_index, 1);
		  			array_splice($svq['svq_video'], 0, 0, $out);
		  		}
		  	}
		}
		unset($svq);

		$atts['class'] = rtrim( 'svq wp-video-shortcode ' . $atts['class'] );

		$html_atts = array(
			'class'    => $atts['class'],
			'id'       => sprintf( 'video-%d-%d', $svq_post_id, $instance ),
			'width'    => absint( $atts['width'] ),
			'height'   => absint( $atts['height'] ),
			'poster'   => esc_url( $atts['poster'] ),
			'loop'     => wp_validate_boolean( $atts['loop'] ),
			'autoplay' => wp_validate_boolean( $atts['autoplay'] ),
			'preload'  => $atts['preload']
		);

		// These ones should just be omitted altogether if they are blank
		foreach ( array( 'poster', 'loop', 'autoplay') as $a ) {
			if ( empty( $html_atts[$a] ) ) {
				unset( $html_atts[$a] );
			}
		}

		$attr_strings = array();
		foreach ( $html_atts as $k => $v ) {
			$attr_strings[] = $k . '="' . esc_attr( $v ) . '"';
		}
		$html = '';
		if ( 1 === $instance ) {
			$html .= "<!--[if lt IE 9]><script>document.createElement('video');</script><![endif]-->\n";
		}
		$html .= sprintf( '<video %s controls="controls" data-svqIndex="">', join( ' ', $attr_strings ) );

		// use first quality item data for video shown at pageload
		$first_item_qualities = ( isset($svq_stored_metadata[$svq_index]['svq_video']) ? $svq_stored_metadata[$svq_index]['svq_video'] : '');
		if ( !empty($first_item_qualities) ){
			foreach ($first_item_qualities as $quality) {
				if ( !empty($quality['svq_url']) ){
					if ( empty($quality['svq_mime']) ){
						$type = wp_check_filetype( $quality['svq_url'], wp_get_mime_types() );
						$quality['svq_mime'] = $type['type'];
					}
					$html .= '<source type="' . $quality['svq_mime'] . '" src="' . esc_url($quality['svq_url']) . '" title="' . $quality['svq_label'] . '" data-order="' . $quality['svq_order'] . '">';
				}
			}
		}
		$html .= '</video>';

		if (!$is_svq_embed && count($svq_stored_metadata) > 1) {
			
			//custom html
			$html .= (!empty($svq_custom_html)) ? '<div class="svq_custom_html">' . $svq_custom_html . '</div>' : '';

			// playlist
			$html .= '<div class="svq_playlist svq_clearfix">';
			$html .= '<ul>';
			foreach ($svq_stored_metadata as $svq) {
				$html .= '<li class="svq_playlist-item">';
				$poster = (!empty($svq['svq_poster'])) ? $svq['svq_poster'] : $def_bg;
				$html .= '<span class="svq_playlist_item_poster"><img class="svq_poster_image" src="' . esc_url($poster) . '"';
				$html .= (!empty($svq['svq_title'])) ? ' title="' . $svq['svq_title'] : '';
				$html .= '"></span>';
				$html .= '<div>';
				$html .= (!empty($svq['svq_title'])) ? '<span class="svq_playlist_item_title">' . $svq['svq_title'] . '</span>' : '';
				$html .= (!empty($svq['svq_title2'])) ? '<span class="svq_playlist_item_title2">' . $svq['svq_title2'] . '</span>' : '';
				$html .= (!empty($svq['svq_title3'])) ? '<span class="svq_playlist_item_title3">' . $svq['svq_title3'] . '</span>' : '';
				$html .= (!empty($svq['svq_ext_link']['url'])) ? '<span class="svq_playlist_item_ext_link"><a href="' . $svq['svq_ext_link']['url'] . '" target="_blank">' . ( !empty( $svq['svq_ext_link']['text'] ) ? $svq['svq_ext_link']['text'] : $svq['svq_ext_link']['url'] ) . '</a></span>' : '';
				$html .= (!empty($svq['svq_video'][0]['svq_length'])) ? '<span class="svq_playlist_item_duration">' . $svq['svq_video'][0]['svq_length'] . '</span>' : '';
				$html .= '</div></li>';
			}
			$html .= '</ul>';
			$html .= '</div>';
		}

		//enqueue frontend scripts
		wp_enqueue_style( 'wp-mediaelement' );
		wp_enqueue_script( 'wp-mediaelement' );
		wp_enqueue_script( 'mejs_sourcechooser', plugins_url( 'js/mep-feature-sourcechooser.js', __FILE__ ), array('mediaelement'), SVQ_VERSION, true );
		wp_enqueue_style( 'svq_css', plugins_url('css/svq.css',  __FILE__), array('wp-mediaelement'), SVQ_VERSION);	
		if (isset($svq_options['show_svq_infooverlay']) ) {
			wp_enqueue_script( 'mejs_svq_infooverlay', plugins_url( 'js/mep-feature-svq_infooverlay.js', __FILE__ ), array('mediaelement'), SVQ_VERSION, true );
			$info_js++;
		}
		if (!$is_svq_embed){ //show playlist and embed code overlay if the video is not embedded in an iframe
			if (count($svq_stored_metadata) > 1){
				wp_enqueue_script( 'mejs_svq_playlist', plugins_url( 'js/mep-feature-svq_playlist.js', __FILE__ ), array('mediaelement'), SVQ_VERSION, true );
				$playlist_js++;
			}
			if ($svq_options['svq_embed_active']) {
				wp_enqueue_script( 'mejs_svq_embed', plugins_url( 'js/mep-feature-svq_embed.js', __FILE__ ), array('mediaelement'), SVQ_VERSION, true );
				$embed_js++;
			}
		}
		// register mejs features and add translatable text (only once even when function is called multiple times)
		if ($svq_instance === 1){
			wp_localize_script( 'wp-mediaelement', '_wpmejsSettings',
				array(
					'features' => array('playpause','progress','volume', 'fullscreen', 'sourcechooser', 'svqinfooverlay', 'svqplaylist', 'svqembed')
				)
			);
			wp_localize_script( 'mejs_sourcechooser', '_svqSettings',
				array(
					'svq_switch_hover' => __('Switch quality', 'switch-video-quality')
				)
			);
		}
		if ($info_js === 1){
			wp_localize_script( 'mejs_svq_infooverlay', '_svqInfoSettings',
				array(
					'svqIndex' => $svq_index,
					'svq_play' => __('Play', 'switch-video-quality'),
					'svq_pause' => __('Pause', 'switch-video-quality')
				)
			);
		}
		if ($playlist_js === 1){
			wp_localize_script( 'mejs_svq_playlist', '_svqPlaylistSettings',
				array(
					'svq_noVideo' => __("Sorry, the video can't be found.", 'switch-video-quality')
				)
			);
		}
		if ($embed_js === 1){
			wp_localize_script( 'mejs_svq_embed', '_svqEmbedSettings',
				array(
					'svq_embed_title' => __('Embed this video', 'switch-video-quality'),
					'svq_close_embed_title' => __('Close', 'switch-video-quality'),
					'svq_embed_code_title' => __('Copy embed code', 'switch-video-quality'),
					'svq_embed_code_label' => __('Copy embed code:', 'switch-video-quality'),
					'svq_embed_custom_size_label' => __('Set custom size:', 'switch-video-quality')
				)
			);
		}
		// convert metadata to json for use with javascript
		$html .= '<script type="text/javascript">';
		if ($svq_instance === 1){
			$html .= "var svq_playlist_data = [];\n";
			$html .= "var svq_options = [];\n";
			$html .= "var svq_embed_urls = [];\n";
		}
		if ( !$is_svq_embed ){
			//push data as json into array
			$html .= sprintf( "svq_embed_urls.push(%s);\n", json_encode($embed_urls) );
		}
		$html .= sprintf( "svq_playlist_data.push(%s);\n", json_encode($svq_stored_metadata) );
		$html .= sprintf( "svq_options.push(%s);\n", json_encode($svq_options) );
		$html .= '</script>';
		$output = sprintf( '<div style="width: %dpx" class="wp-video">%s</div>', $atts['width'], $html );
	}
	return $output;
}