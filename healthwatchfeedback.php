<?php
/*
Plugin Name: Healthwatch Feedback
Version: 1.8.2
Description: Implements a Rate and Review centre on Healthwatch websites. <strong>DO NOT DELETE !</strong>
Author: Phil Thiselton & Jason King
*/

defined( 'ABSPATH' ) or die( 'Sorry, nothing to see here.' );

// Includes

include('post-types-taxonomies.php');

include('taxonomy-icons.php');

include('feedback-form.php');

	// include('widgets/widget-list-services.php');

// Include widgets


include('widgets/widget-list-service-types.php');

include('widgets/widget-most-rated.php');

include('widgets/widget-enter-and-view.php');

include('widgets/widget-recent-comments.php');

// Add functions

require_once('functions/functions-star-rating.php');
require_once('functions/functions-get-rating.php');
require_once('functions/shortcodes.php');

// Enqueue CSS

function hw_enqueue_css() {
    wp_enqueue_style( 'prefix-style', plugins_url('css/style.css', __FILE__) );
}
add_action( 'wp_enqueue_scripts', 'hw_enqueue_css' );
add_action( 'admin_enqueue_scripts', 'hw_enqueue_css' );

// add fontawesome on edit-comments.php
function add_fontawesome_edit_comments( $hook ) {
    if ( 'edit-comments.php' != $hook && 'edit.php' != $hook ) {
        return;
    }
		wp_enqueue_script( 'fontawesome_5_cdn_admin', 'https://kit.fontawesome.com/c1c5370dea.js');
}
add_action( 'admin_enqueue_scripts', 'add_fontawesome_edit_comments' );

/**
 * Disable the "local_services" custom post type feed
 *
 * @since 1.0.0
 * @param object $query
 */
function disable_local_services_feed( $query ) {
    if ( $query->is_feed() && in_array( 'local_services', (array) $query->get( 'post_type' ) ) ) {
        die( 'Local Service - feed disabled' );
    }
}
//add_action( 'pre_get_posts', 'disable_local_services_feed' );

// Add new admin columns

add_filter( 'manage_edit-comments_columns', 'hw_add_comments_columns' );

function hw_add_comments_columns( $my_cols ){
	// $my_cols is the array of all column IDs and labels
	// if you know arrays, you can add, remove or change column order with no problems
	// like this:
	/*
	$my_cols = array(
		'cb' => '', // do not forget about the CheckBox
		'author' => 'Author',
		'comment' => 'Comment',
		'm_comment_id' => 'ID', // added
		'm_parent_id' => 'Parent ID', // added
		'response' => 'In reply to',
		'date' => 'Date'
	);
	*/
	// but the above way is not so good - there could be problems when plugins would like to hook the comment columns
	// so, better like this:
	$hw_columns = array(
		'feedback_response_boolean' => 'Provider reply',
		'feedback_hw_reply_boolean' => 'LHW reply'
	);
	$my_cols = array_slice( $my_cols, 0, 3, true ) + $hw_columns + array_slice( $my_cols, 3, NULL, true );

	// if you want to remove a column, you can just use:
	// unset( $my_cols['response'] );

	// return the result
	return $my_cols;
}

add_action( 'manage_comments_custom_column', 'hw_add_comment_columns_content', 10, 2 );

function hw_add_comment_columns_content( $column, $comment_ID ) {
	global $comment;
	switch ( $column ) :
		case 'feedback_response_boolean' : {
      if (get_comment_meta( $comment->comment_ID, 'feedback_response', true ) != "" ) {
        echo '<a class="checkmark-wrapper" title="Edit comment" href="' . get_edit_comment_link() . '"><i class="fas fa-check fa-lg checkmark"></i><span class="screen-reader-text">TRUE</span></a>';  // this will be printed inside the column
			}
			break;
		}
		case 'feedback_hw_reply_boolean' : {
      if (get_comment_meta( $comment->comment_ID, 'feedback_hw_reply', true ) != "" ) {
        echo '<a class="checkmark-wrapper" title="Edit comment" href="' . get_edit_comment_link() . '"><i class="fas fa-check fa-lg checkmark"></i><span class="screen-reader-text">TRUE</span></a>';  // this will be printed inside the column
      }
			break;
		}
	endswitch;
}

/* Enqueue JS
------------------------------------------------------------------------------ */
wp_enqueue_script( 'scaffold_copy_civicrm_subject_code', plugin_dir_url( __FILE__ ) . 'js/copy_civicrm_subject_code.js');

?>
