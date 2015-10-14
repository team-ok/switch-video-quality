<?php
/*
Plugin Name: Switch Video Quality
Description: Switch Video Quality adds quality switch functionality to the wordpress video player (choose between different resolutions of a self-hosted html5-compatible video).
Version: 1.1
Author: Timo Klemm (team-ok)
License: GPLv3, mep-feature-sourcechooser.js by John Dyer is originally licensed under the MIT license.
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
add_action( 'load-post.php', 'switch_video_quality_settings' );
add_action( 'load-post-new.php', 'switch_video_quality_settings' );

function init_textdomain() {
	$lang_dir = basename(dirname(__FILE__)) . '/lang/';
	load_plugin_textdomain( 'switch-video-quality', false, $lang_dir );
}
add_action('plugins_loaded', 'init_textdomain');

function switch_video_quality_settings() {
	add_action( 'add_meta_boxes', 'switch_video_quality_box' );
}
function switch_video_quality_box() {
	$post_types = get_post_types( array( 'public' => true ) );
	foreach ( $post_types as $post_type ) {
		if ( 'attachment' == $post_type || 'revision' == $post_type || 'nav_menu_item' == $post_type) {
		continue;
		}
		add_action( 'admin_enqueue_scripts', 'svq_admin_scripts' );
		add_meta_box( 'svq_settings', 'Switch Video Quality', 'print_svq_box', $post_type, 'normal', 'high');
	}
}
function svq_admin_scripts() {
	wp_enqueue_script( 'svq_admin_js', plugins_url( 'js/svq_admin.js', __FILE__ ), array('jquery-ui-sortable', 'jquery'), false, true );
	wp_enqueue_style( 'svq_admin_css', plugins_url('css/svq_admin.css',  __FILE__));
	wp_localize_script( 'svq_admin_js', 'svq_admin_l10n', array(
		'playlistPosition' => __('Playlist Position', 'switch-video-quality'),
		'removeFields' => __('Remove Fields', 'switch-video-quality'),
		'url' => __('URL', 'switch-video-quality'),
		'label' => __('Label', 'switch-video-quality'),
		'mimeType' => __('MIME-Type', 'switch-video-quality'),
		'select' => __('Select', 'switch-video-quality'),
		'duration' => __('Duration', 'switch-video-quality'),
		'order' => __('Height (Sorting)', 'switch-video-quality'),
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
			<span class="svq_playlist_position svq_handle" title="<?php _e('Drag &amp; Drop playlist item', 'switch-video-quality'); ?>" data-position="<?php echo $playlist_number ?>"><?php _e('Playlist Position', 'switch-video-quality'); ?> <?php echo $playlist_number; ?></span>
			<span class="add_svq_item dashicons-plus-alt dashicons" title="<?php _e('Insert empty box after this one', 'switch-video-quality'); ?>"></span>
			<span class="clear_svq_item dashicons-editor-removeformatting dashicons" title="<?php _e('Remove all entries of this playlist item', 'switch-video-quality'); ?>"></span>
			<span class="remove_svq_item dashicons-dismiss dashicons" title="<?php _e('Remove playlist item completely', 'switch-video-quality'); ?>"></span>
			<span class="svq_scrolltotop dashicons dashicons-admin-collapse" title="<?php _e('Scroll to top', 'switch-video-quality'); ?>"></span>
		</div>
		<div class="svq_title svq_clearfix">
			<p><?php _e('Set title', 'switch-video-quality'); ?></p>
			<div>
				<label><?php _e('Title', 'switch-video-quality'); ?>
				1 <input type="text" name="svq[<?php echo $cnt ?>][svq_title]" value="<?php if (isset($svq['svq_title'])) echo $svq['svq_title']; ?>" size="30" />
				</label>
			</div>
			<div>
				<label><?php _e('Title', 'switch-video-quality'); ?>
				2 <input type="text" name="svq[<?php echo $cnt ?>][svq_title2]" value="<?php if (isset($svq['svq_title2'])) echo $svq['svq_title2']; ?>" size="30" />
				</label>
			</div>
			<div>
				<label><?php _e('Title', 'switch-video-quality'); ?>
				3 <input type="text" name="svq[<?php echo $cnt ?>][svq_title3]" value="<?php if (isset($svq['svq_title3'])) echo $svq['svq_title3']; ?>" size="30" />
				</label>
			</div>
		</div>
		<div class="svq_poster svq_clearfix">
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
		<div class="svq_video svq_clearfix">
			<p><?php _e('Set video details', 'switch-video-quality'); ?></p>
			<input type="button" class="button svq_choose_vid" value="<?php _e('Choose/upload videos', 'switch-video-quality'); ?>" title="<?php _e('Open the media manager', 'switch-video-quality'); ?>" />
			<input type="button" class="button svq_vid_manual" value="<?php _e('Add input field', 'switch-video-quality'); ?>" title="<?php _e('Adds empty input field to set video url and label manually', 'switch-video-quality'); ?>" />
			<?php $i = 0;
				if (!empty( $svq['svq_video'] ) ) {
					foreach ($svq['svq_video'] as $svq_video) { ?>
						<div class="svq_video_qualities">
							<span class="clear_video_input" title="<?php _e('Remove fields', 'switch-video-quality'); ?>"></span>
							<div>
								<label><?php _e('URL', 'switch-video-quality'); ?>
									<input class="video_url_input" type="text" name="svq[<?php echo $cnt ?>][svq_video][<?php echo $i; ?>][svq_url]" value="<?php echo (!empty($svq_video['svq_url']) ? esc_url($svq_video['svq_url']) : ''); ?>" size="80" />
								</label>
							</div>
							<div>
								<label><?php _e('Label', 'switch-video-quality'); ?>
									<input class="video_quality_label" type="text" name="svq[<?php echo $cnt ?>][svq_video][<?php echo $i; ?>][svq_label]" value="<?php echo (!empty($svq_video['svq_label']) ? $svq_video['svq_label'] : ''); ?>" size="5" />
								</label>
							</div>
							<div>
								<label><?php _e('MIME-Type', 'switch-video-quality'); ?>
									<select class="video_quality_mime" required name="svq[<?php echo $cnt ?>][svq_video][<?php echo $i; ?>][svq_mime]">
										<option value=""><?php _e('Select', 'switch-video-quality'); ?></option>
										<option value="video/mp4" <?php selected($svq_video['svq_mime'], 'video/mp4'); ?>>video/mp4</option>
										<option value="video/webm" <?php selected($svq_video['svq_mime'], 'video/webm'); ?>>video/webm</option>
										<option value="video/ogg" <?php selected($svq_video['svq_mime'], 'video/ogg'); ?>>video/ogg</option>
									</select>
								</label>
							</div>
							<div>
								<label><?php _e('Duration', 'switch-video-quality'); ?>
									<input class="video_quality_duration" type="text" name="svq[<?php echo $cnt ?>][svq_video][<?php echo $i; ?>][svq_length]" value="<?php echo (!empty($svq_video['svq_length']) ? $svq_video['svq_length'] : ''); ?>" size="8" />
								</label>
							</div>
							<div>
								<label><?php _e('Height (Sorting)', 'switch-video-quality'); ?>
									<input class="video_quality_order" type="text" name="svq[<?php echo $cnt ?>][svq_video][<?php echo $i; ?>][svq_order]" value="<?php echo (!empty($svq_video['svq_order']) ? $svq_video['svq_order'] : ''); ?>" size="5" />
								</label>
							</div>
						</div>
					<?php $i++;
					}
				} ?>
		</div>
	</div>
<?php }

function print_svq_box( $post ) {
	wp_nonce_field( basename( __FILE__ ), 'svq_box_nonce' );
	$svq_stored_metadata = get_post_meta( $post->ID, 'svq_metadata', true );
	$svq_custom_html = get_post_meta( $post->ID, 'svq_custom_html', true ); 
	$svq_options = get_post_meta( $post->ID, 'svq_options', true);
	$svq_options['show_svq_infooverlay'] = (isset($svq_options['show_svq_infooverlay']) ? $svq_options['show_svq_infooverlay'] : 'off');
	$svq_options['svq_active'] = (isset($svq_options['svq_active']) ? $svq_options['svq_active'] : 'off');
	$svq_options['svq_sort_qualities'] = (isset($svq_options['svq_sort_qualities']) ? $svq_options['svq_sort_qualities'] : 'desc');
	?>

	<div id="svq_options">
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
			<span><?php _e('Sort quality levels:', 'switch-video-quality'); ?></span>
			<label><?php _e('ascending', 'switch-video-quality'); ?>
			<input id="svq_sort_qualities_asc" type="radio" name="svq_options[svq_sort_qualities]" value="asc" <?php checked( $svq_options['svq_sort_qualities'], 'asc'); ?> />
			</label>
			<label><?php _e('descending', 'switch-video-quality'); ?>
			<input id="svq_sort_qualities_desc" type="radio" name="svq_options[svq_sort_qualities]" value="desc" <?php checked( $svq_options['svq_sort_qualities'], 'desc'); ?> />
			</label>
		</div>
	</div>
	<div id="svq_custom_html">
			<label for="svq_custom_text"><?php _e('Custom HTML (inserted between Player and Playlist)', 'switch-video-quality'); ?></label>
			<br />
			<textarea id="svq_custom_text" cols="40" rows="3" name="svq_custom_html"><?php if (!empty($svq_custom_html)) echo $svq_custom_html; ?></textarea>
	</div>
	<div class="svq_sortable ui-sortable">
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
    // If it is an element, then just return it sanitized
    if (!is_array($input)) {
      return sanitize_text_field($input);
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
****************HTML and JSON Frontend Output****************
************************************************************/

function extend_video_shortcode($out, $pairs, $atts) {
	$svq_stored_metadata = get_post_meta( get_the_ID(), 'svq_metadata', true );
	$svq_options = get_post_meta( get_the_ID(), 'svq_options', true );

	if (isset($svq_options['svq_active']) && !empty($svq_stored_metadata[0]['svq_video'][0]['svq_url'])) {
		$out['src'] = $svq_stored_metadata[0]['svq_video'][0]['svq_url'];
		if (!empty($svq_stored_metadata[0]['svq_poster'])) {
			$out['poster'] = $svq_stored_metadata[0]['svq_poster'];
		}
	}
	return $out;
}
function wp_video_shortcode_filter($output, $atts) {
	$svq_stored_metadata = get_post_meta( get_the_ID(), 'svq_metadata', true );
	$svq_custom_html = get_post_meta( get_the_ID(), 'svq_custom_html', true );
	$svq_options = get_post_meta( get_the_ID(), 'svq_options', true );

	if (isset($svq_options['svq_active']) && !empty($svq_stored_metadata[0]['svq_video'][0]['svq_url'])) {
		wp_enqueue_script( 'mejs_sourcechooser', plugins_url( 'js/mep-feature-sourcechooser.js', __FILE__ ), array('mediaelement'), false, true );
		wp_enqueue_style( 'svq_css', plugins_url('css/svq.css',  __FILE__));	
		wp_enqueue_script( 'mejs_svq_infooverlay', plugins_url( 'js/mep-feature-svq_infooverlay.js', __FILE__ ), array('mediaelement'), false, true );
		wp_enqueue_script( 'mejs_svq_playlist', plugins_url( 'js/mep-feature-svq_playlist.js', __FILE__ ), array('mediaelement'), false, true );
		
		// plug-in mejs-sourcechooser and infooverlay
		wp_localize_script( 'wp-mediaelement', '_wpmejsSettings', array(
		'features' => array('playpause','progress','volume', 'fullscreen', 'sourcechooser', 'svqinfooverlay', 'svqplaylist'),
		'svq_switch_hover' => __('Switch quality', 'switch-video-quality'),
		'svq_play' => __('Play', 'switch-video-quality'),
		'svq_pause' => __('Pause', 'switch-video-quality')
		) );
		$html = '';
		$first_item_qualities = $svq_stored_metadata[0]['svq_video'];
		// use first quality item data for video shown at pageload
		foreach ($first_item_qualities as $quality) {
			$html .= '<source type="' . $quality['svq_mime'] . '" src="' . esc_url($quality['svq_url']) . '" title="' . $quality['svq_label'] . '" data-order="' . $quality['svq_order'] . '">';
		}
		$html .= '</video>';

		// playlist
		if (count($svq_stored_metadata) > 1) {
			
		$html .= (!empty($svq_custom_html)) ? '<div class="svq_custom_html">' . $svq_custom_html . '</div>' : '';
		$html .= '<div class="svq_playlist svq_clearfix">';
		$html .= '<ul>';
		foreach ($svq_stored_metadata as $key => $svq) {
			$html .= '<li class="svq_playlist-item">';
			$poster = (!empty($svq['svq_poster'])) ? $svq['svq_poster'] : plugins_url('img/playlist_def_bg.png',  __FILE__);
			$html .= '<span class="svq_playlist_item_poster"><img src="' . esc_url($poster) . '"';
			$html .= (!empty($svq['svq_title'])) ? ' title="' . $svq['svq_title'] : '';
			$html .= '"></span>';
			$html .= '<div>';
			$html .= (!empty($svq['svq_title'])) ? '<span class="svq_playlist_item_title">' . $svq['svq_title'] . '</span>' : '';
			$html .= (!empty($svq['svq_title2'])) ? '<span class="svq_playlist_item_title2">' . $svq['svq_title2'] . '</span>' : '';
			$html .= (!empty($svq['svq_title3'])) ? '<span class="svq_playlist_item_title3">' . $svq['svq_title3'] . '</span>' : '';
			$html .= (!empty($svq['svq_video'][0]['svq_length'])) ? '<span class="svq_playlist_item_duration">' . $svq['svq_video'][0]['svq_length'] . '</span>' : '';
			$html .= '</div></li>';
		}
		$html .= '</ul>';
		$html .= '</div>';
		}
		$output = preg_replace('/<source.*<\/video>/', $html, $output);

		// convert metadata to json for use with javascript ?>
		<script type="text/javascript">
		<?php //check if array already exists, if not, create it ?>
		var svq_playlist_data = svq_playlist_data || [];
		var svq_options = svq_options || [];
		<?php //push data as json into array ?>
		svq_playlist_data.push(<?php echo json_encode($svq_stored_metadata) ?>);
		svq_options.push(<?php echo json_encode($svq_options) ?>);
		</script><?php
	}
	return $output;
	return $atts;
}
function add_svq_class($class){
	$svq_options = get_post_meta( get_the_ID(), 'svq_options', true );
	if (isset($svq_options['svq_active'])){
		$class = $class . ' ' . 'svq';
	}
	return $class;
}

add_filter( 'wp_video_shortcode_class', 'add_svq_class', 10, 1 );
add_filter( 'shortcode_atts_video', 'extend_video_shortcode', 10, 3 );
add_filter( 'wp_video_shortcode', 'wp_video_shortcode_filter', 10, 2 );