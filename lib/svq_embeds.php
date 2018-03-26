<?php

function svq_embed_query_vars($vars){
	$vars[] = 'svq_embed_id';
	$vars[] = 'svq_index';
	$vars[] = 'svq_width';
	return $vars;
}

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