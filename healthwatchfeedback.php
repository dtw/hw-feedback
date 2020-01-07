<?php
/*
Plugin Name: Healthwatch Feedback
Version: 1.5.0
Description: Implements a Rate and Review centre on Healthwatch websites. <strong>DO NOT DELETE !</strong>
Author: Phil Thiselton & Jason King
*/

defined( 'ABSPATH' ) or die( 'Sorry, nothing to see here.' );

// Includes

include('post-types-taxononmies.php');

include('taxonomy-icons.php');

include('feedback-form.php');

	// include('widgets/widget-list-services.php');

// Include widgets


include('widgets/widget-list-service-types.php');

include('widgets/widget-most-rated.php');

include('widgets/widget-enter-and-view.php');

include('widgets/widget-recent-comments.php');

// Include shortcodes

include('shortcodes/shortcode-ratings-block.php');

// Add functions

require_once('functions/functions-star-rating.php');
require_once('functions/functions-get-rating.php');

// Enqueue CSS

 add_action( 'wp_enqueue_scripts', 'hw_enqueue_css' );
    function hw_enqueue_css() {
        wp_enqueue_style( 'prefix-style', plugins_url('css/style.css', __FILE__) );
    }

// add fontawesome on edit-comments.php
function add_fontawesome_edit_comments( $hook ) {
    if ( 'edit-comments.php' != $hook ) {
        return;
    }
		wp_enqueue_script( 'fontawesome_5_cdn_admin', 'https://kit.fontawesome.com/c1c5370dea.js');
}
add_action( 'admin_enqueue_scripts', 'add_fontawesome_edit_comments' );
?>
