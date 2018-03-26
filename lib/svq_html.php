<?php
/************************************************************
****************HTML and JSON Frontend Output****************
************************************************************/

function svq_video_shortcode_output($output, $attr, $content, $instance){
	global $content_width;
	//check if current query is an embed (= url with 'svq_embed_id' query var)
	$svq_post_id = get_query_var('svq_embed_id');
	$svq_index = 0;
	$is_svq_embed = false;
	if ( !empty($svq_post_id) ){
		$svq_index = get_query_var('svq_index');
		$svq_width = get_query_var('svq_width');
		$is_svq_embed = true;
	} elseif ( !empty( $attr['svq'] ) && is_numeric( $attr['svq'] ) ) {
		$svq_post_id = $attr['svq'];
	} else {
		$svq_post_id = get_the_ID();
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
			if ( isset( $svq_stored_metadata[$svq_index]['svq_subs'] ) ){
				foreach ($svq_stored_metadata[$svq_index]['svq_subs'] as $sub) {
					$html .= '<track srclang="' . esc_attr($sub['svq_lang']) . '" label="' . esc_html($sub['svq_label']) . '" kind="subtitles" src="' . esc_url($sub['svq_src']) . '">';
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
		if ( version_compare( get_bloginfo('version'), '4.9', '>=') ){
			wp_enqueue_script( 'mejs_sourcechooser', SVQ_URL . 'js/mep-feature-sourcechooser-mejs-v4.js', array('mediaelement'), SVQ_VERSION, true );
			wp_enqueue_style( 'mejs_sourcechooser', SVQ_URL . 'css/mejs-sourcechooser.css', array(), SVQ_VERSION );
			wp_enqueue_style( 'svq_css', SVQ_URL . 'css/svq-mejs-v4.css', array('wp-mediaelement'), SVQ_VERSION);
		} else {
			wp_enqueue_script( 'mejs_sourcechooser', SVQ_URL . 'js/mep-feature-sourcechooser.js', array('mediaelement'), SVQ_VERSION, true );
			wp_enqueue_style( 'svq_css', SVQ_URL . 'css/svq.css', array('wp-mediaelement'), SVQ_VERSION);
		}	
		if (isset($svq_options['show_svq_infooverlay']) ) {
			wp_enqueue_script( 'mejs_svq_infooverlay', SVQ_URL . 'js/mep-feature-svq_infooverlay.js', array('mediaelement'), SVQ_VERSION, true );
			$info_js++;
		}
		if (!$is_svq_embed){ //show playlist and embed code overlay if the video is not embedded in an iframe
			if (count($svq_stored_metadata) > 1){
				wp_enqueue_script( 'mejs_svq_playlist', SVQ_URL . 'js/mep-feature-svq_playlist.js', array('mediaelement'), SVQ_VERSION, true );
				$playlist_js++;
			}
			if ($svq_options['svq_embed_active']) {
				wp_enqueue_script( 'mejs_svq_embed', SVQ_URL . 'js/mep-feature-svq_embed.js', array('mediaelement'), SVQ_VERSION, true );
				$embed_js++;
			}
		}
		// register mejs features and add translatable text (only once even when function is called multiple times)
		if ($svq_instance === 1){
			wp_localize_script( 'wp-mediaelement', '_wpmejsSettings',
				array(
					'pluginPath' => includes_url( 'js/mediaelement/', 'relative' ),
					'classPrefix' => 'mejs-',
					'stretching' => 'responsive',
					'features' => array('playpause','progress','volume', 'fullscreen', 'sourcechooser', 'svqinfooverlay', 'svqplaylist', 'svqembed', 'tracks')
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