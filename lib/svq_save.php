<?php
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
    $is_valid_nonce = ( isset( $_POST[ 'svq_box_nonce' ] ) && wp_verify_nonce( $_POST[ 'svq_box_nonce' ], 'svq_nonce_' . $post_id ) ) ? true : false;
 
    // Exits script depending on save status
    if ( $is_autosave || $is_revision || !$is_valid_nonce ) {
        return;
    }
    
    // Check the user's permissions.
    if ( !current_user_can( 'edit_post', $post_id ) ) {
    	return;
    }

   	$svq_metadata = ( isset( $_POST['svq'] ) ) ? omit_empty_items( $_POST['svq'] ) : null;

	if ( isset( $svq_metadata ) ){
		update_post_meta( $post_id, 'svq_metadata', $svq_metadata );
	}
	if ( isset( $_POST['svq_custom_html'] ) ){
		update_post_meta( $post_id, 'svq_custom_html', $_POST['svq_custom_html'] );
	}
	if ( isset( $_POST['svq_options'] ) ){
		update_post_meta( $post_id, 'svq_options', $_POST['svq_options'] );
	}

}