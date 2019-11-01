<?php
/*
Plugin Name: Healthwatch Feedback
Version: 1.1
Description: Implements a Rate and Review centre on Healthwatch websites. <strong>DO NOT DELETE !</strong>
Author: Jason King
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

// Enqueue CSS

 add_action( 'wp_enqueue_scripts', 'hw_enqueue_css' );
    function hw_enqueue_css() {
        wp_enqueue_style( 'prefix-style', plugins_url('css/style.css', __FILE__) );
    }


// Form validation

function load_form_validation_js()
{
    // Register the script like this for a plugin:
    wp_register_script( 'form-validation', plugins_url( '/javascript/form-validation.js', __FILE__ ) );
 
    // For either a plugin or a theme, you can then enqueue the script:
    wp_enqueue_script( 'form-validation' );
}
add_action( 'wp_enqueue_scripts', 'load_form_validation_js' );

?>