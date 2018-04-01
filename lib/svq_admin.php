<?php
/************************************************************
******************SVQ Metaboxes******************
************************************************************/
function switch_video_quality_settings() {
	add_action( 'add_meta_boxes', 'switch_video_quality_box' );
}

function switch_video_quality_box() {
	$post_types = get_post_types( array( 'public' => true ) );
	$excluded = apply_filters( 'svq_exclude_post_types', $excluded = array('attachment', 'revision', 'nav_menu_item') );
	$post_types = array_diff( $post_types, $excluded );
	add_meta_box( 'svq_settings', 'Switch Video Quality', 'print_svq_box', $post_types, 'normal', 'high');
	add_meta_box( 'svq_shortcode_snippet', 'SVQ Shortcode', 'svq_shortcode_snippet', 'svq_video', 'side');
}

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
						<input class="link_text_input" type="text" name="svq[<?php echo $cnt ?>][svq_ext_link][text]" value="<?php if (isset( $svq['svq_ext_link']['text'] ) ) echo esc_html($svq['svq_ext_link']['text']) ?>" size="50" />
					</label>
				</div>
			</div>
			<div class="svq_subtitles svq_input_wrap">
				<p><?php _e('Set subtitle file', 'switch-video-quality'); ?></p>
				<input class="button svq_choose_subtitle" type="button" value="<?php _e('Choose/upload subtitle', 'switch-video-quality'); ?>" title="<?php _e('Open the media manager', 'switch-video-quality'); ?>" />
				<input type="button" class="button svq_manual_entry" value="<?php _e('Add input fields', 'switch-video-quality'); ?>" title="<?php _e('Adds empty input fields for manual data entries', 'switch-video-quality'); ?>" />
				<?php if (!empty( $svq['svq_subs'] ) ) {
					$i = 0;
					foreach ($svq['svq_subs'] as $svq_sub) { ?>
						<div class="svq_subtitle">
							<span class="svq_clear_input" title="<?php _e('Remove Fields', 'switch-video-quality'); ?>"></span>
							<div>
								<label><?php _e('Label', 'switch-video-quality'); ?><input class="svq_subtitle_label" required type="text" size="15" value="<?php echo $svq_sub['svq_label']; ?>" name="svq[<?php echo $cnt; ?>][svq_subs][<?php echo $i; ?>][svq_label]" /></label>
							</div>
							<div>
								<label><?php _e('Language Tag', 'switch-video-quality'); ?><input class="svq_subtitle_lang" required type="text" size="5" value="<?php echo $svq_sub['svq_lang']; ?>" name="svq[<?php echo $cnt; ?>][svq_subs][<?php echo $i; ?>][svq_lang]" />
									<a href="https://r12a.github.io/app-subtags/" rel="noopener noreferrer" target="_blank" title="<?php _e('Find the right language tag', 'switch-video-quality'); ?>"><span class="dashicons dashicons-info"></span></a>
								</label>
							</div>
							<div>
								<label><?php _e('URL', 'switch-video-quality'); ?><input class="svq_subtitle_src" type="text" size="80" value="<?php echo $svq_sub['svq_src']; ?>" name="svq[<?php echo $cnt; ?>][svq_subs][<?php echo $i; ?>][svq_src]" /></label>
							</div>
						</div>
						<?php $i++;
					} 
				} ?>
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
				<input type="button" class="button svq_manual_entry" value="<?php _e('Add input fields', 'switch-video-quality'); ?>" title="<?php _e('Adds empty input fields for manual data entries', 'switch-video-quality'); ?>" />
				<?php if (!empty( $svq['svq_video'] ) ) {
					$i = 0;
					foreach ($svq['svq_video'] as $svq_video) { ?>
						<div class="svq_video_qualities">
							<span class="svq_clear_input" title="<?php _e('Remove fields', 'switch-video-quality'); ?>"></span>
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
	wp_nonce_field( 'svq_nonce_' . $post->ID, 'svq_box_nonce' );
	$svq_stored_metadata = get_post_meta( $post->ID, 'svq_metadata', true );
	$svq_custom_html = get_post_meta( $post->ID, 'svq_custom_html', true ); 
	$svq_options = (array) get_post_meta( $post->ID, 'svq_options', true);

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

function svq_shortcode_snippet($post){ ?>
	<div class="svq-shortcode">
		<label for="svq-<?php echo $post->ID; ?>"><?php _e('Copy this shortcode and paste it into a post or page.', 'switch-video-quality'); ?></label>
		<p><strong><input id="svq-<?php echo $post->ID; ?>" type="text" value="[video svq=&quot;<?php echo $post->ID; ?>&quot;]"></strong></p>
		<button type="button" class="button"><?php _e('Copy to clipboard', 'switch-video-quality'); ?></button>
	</div>
<?php }