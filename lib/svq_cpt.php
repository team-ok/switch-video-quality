<?php

/************************************************************
***************Register SVQ Video Post Type******************
************************************************************/
function register_cpt_svq_video() {

	$labels = array(
		"name" => __( "SVQ Videos", "switch-video-quality" ),
		"singular_name" => __( "SVQ Video", "switch-video-quality" ),
	);

	$args = array(
		"label" => __( "SVQ Videos", "switch-video-quality" ),
		"labels" => $labels,
		"description" => "",
		"public" => true,
		"publicly_queryable" => true,
		"show_ui" => true,
		"show_in_rest" => false,
		"rest_base" => "",
		"has_archive" => false,
		"show_in_menu" => true,
		"exclude_from_search" => true,
		"capability_type" => "post",
		"map_meta_cap" => true,
		"hierarchical" => false,
		"rewrite" => array( "slug" => "svq_video", "with_front" => true ),
		"query_var" => true,
		"menu_position" => 10,
		"menu_icon" => "dashicons-video-alt3",
		"supports" => array( "title" ),
	);

	register_post_type( "svq_video", $args );
}