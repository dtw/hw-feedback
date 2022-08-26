<?php
/*
Plugin Name: Healthwatch Feedback
Version: 2.2.4
Description: Implements a Rate and Review centre on Healthwatch websites. <strong>DO NOT DELETE !</strong>
Author: Phil Thiselton & Jason King
*/

defined( 'ABSPATH' ) or die( 'Sorry, nothing to see here.' );

// Includes

include('post-types-taxonomies.php');

include('taxonomy-icons.php');

include('feedback-form.php');

require_once(plugin_dir_path( __FILE__ ).'/includes/shortcodes.php');

// ADMIN
require_once(plugin_dir_path( __FILE__ ).'/includes/admin.php');
require_once(plugin_dir_path( __FILE__ ).'/includes/dash.php');

	// include('widgets/widget-list-services.php');

// Include widgets


include('widgets/widget-list-service-types.php');

include('widgets/widget-most-rated.php');

include('widgets/widget-enter-and-view.php');

include('widgets/widget-recent-comments.php');
// WIDGET for displaying a DIC VISIT - moved from scaffold-widget-tweaks
include('widgets/widget-latest-dic-visit-hwbucks.php');
// WIDGET for displaying RECENT FEEDBACK - moved from scaffold-widget-tweaks
include('widgets/widget-recent-feedback-hwbucks.php');

// Add functions

require_once('functions/functions-star-rating.php');
require_once('functions/functions-get-rating.php');
require_once('functions/functions-cqc-api-query.php');
require_once('functions/functions-generate-metabox-form-fields.php');
require_once('functions/shortcodes.php');

// Enqueue CSS

function hw_feedback_enqueue_css() {
    wp_enqueue_style( 'prefix-style', plugins_url('css/style.css', __FILE__) );
}
add_action( 'wp_enqueue_scripts', 'hw_feedback_enqueue_css' );
add_action( 'admin_enqueue_scripts', 'hw_feedback_enqueue_css' );

// add fontawesome on edit-comments.php
function hw_feedback_add_fontawesome_edit_comments( $hook ) {
    if ( 'edit-comments.php' != $hook && 'edit.php' != $hook ) {
        return;
    }
		wp_enqueue_script( 'fontawesome_5_cdn_admin', 'https://kit.fontawesome.com/c1c5370dea.js');
}
add_action( 'admin_enqueue_scripts', 'hw_feedback_add_fontawesome_edit_comments' );

/**
 * Disable the "local_services" custom post type feed
 *
 * @since 1.0.0
 * @param object $query
 */
function hw_feedback_disable_local_services_feed( $query ) {
    if ( $query->is_feed() && in_array( 'local_services', (array) $query->get( 'post_type' ) ) ) {
        die( 'Local Service - feed disabled' );
    }
}
//add_action( 'pre_get_posts', 'hw_feedback_disable_local_services_feed' );

// Add new admin columns

add_filter( 'manage_edit-comments_columns', 'hw_feedback_add_comments_columns' );

function hw_feedback_add_comments_columns( $my_cols ){
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
  $hw_columns_a = array(
    'feedback_author_ip_check' => 'IP Check'
  );
	$hw_columns_b = array(
		'feedback_response_boolean' => 'Provider reply',
		'feedback_hw_reply_boolean' => 'LHW reply'
	);
	//$my_cols = array_slice( $my_cols, 0, 2, true ) + $hw_columns_a + array_slice( $my_cols, 2, 2, true ) + $hw_columns_b + array_slice( $my_cols, 3, NULL, true );
  	$my_cols = array_slice( $my_cols, 0, 2, true ) + $hw_columns_a + array_slice( $my_cols, 2, 2, true ) + $hw_columns_b + array_slice( $my_cols, 3, NULL, true );

	// if you want to remove a column, you can just use:
	// unset( $my_cols['response'] );

	// return the result
	return $my_cols;
}

add_action( 'manage_comments_custom_column', 'hw_feedback_add_comment_columns_content', 10, 2 );

function hw_feedback_add_comment_columns_content( $column, $comment_ID ) {
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
    case 'feedback_author_ip_check' : {
      $string = get_comment_author_ip($comment->comment_ID);
      $pattern = "/208\.127\.19[2-9]\.(\d{1,3})/";
      if (preg_match($pattern, $string) ) {
        echo '<i class="fas fa-clinic-medical fa-lg nhs"></i><span class="screen-reader-text">NHS IP Address</span>';  // this will be printed inside the column
      }
      break;
    }
	endswitch;
}

/* Enqueue JS
------------------------------------------------------------------------------ */
// add copy_civicrm_subject_code script
function hw_feedback_add_copy_civicrm_subject_code() {
    wp_enqueue_script(
        'scaffold_copy_civicrm_subject_code', // name your script so that you can attach other scripts and de-register, etc.
        plugin_dir_url( __FILE__ ) . 'js/copy_civicrm_subject_code.js', // this is the location of your script file
        array('jquery'), // this array lists the scripts upon which your script depends
        '0.1'
    );
}

add_action( 'admin_enqueue_scripts', 'hw_feedback_add_copy_civicrm_subject_code' );

/* this adds a class to the metabox too (there is an ID already so...) */

function hw_feedback_add_metabox_classes($classes) {
    array_push($classes,'hw_services_meta_box');
    return $classes;
}

add_filter('postbox_classes_local_services_hw_services_meta_box','hw_feedback_add_metabox_classes');

/**
 * Activate the plugin.
 */
function hw_feedback_activate() {
  /* Add cron job to run check_cqc_registration_status
  --------------------------------------------------------- */
  if ( ! wp_next_scheduled( 'hw_feedback_cqc_reg_check_cron_job' ) ) {
      // set the first run 2 minutes from "now"
      wp_schedule_event( time()+120, 'weekly', 'hw_feedback_cqc_reg_check_cron_job' );
  }
}
register_activation_hook( __FILE__, 'hw_feedback_activate' );

/**
 * Deactivate the plugin.
 */
function hw_feedback_deactivate() {
    // Remove the cronjob
    wp_clear_scheduled_hook('hw_feedback_cqc_reg_check_cron_job' );
}
register_deactivation_hook( __FILE__, 'hw_feedback_deactivate' );

/**
 * Uninstall the plugin.
 */
function hw_feedback_uninstall() {
    // delete settings/options values
    delete_option('hw_feedback_options');
}
register_uninstall_hook( __FILE__, 'hw_feedback_uninstall' );

/**
 * https://developer.wordpress.org/plugins/settings/custom-settings-page/
 *
 * @internal never define functions inside callbacks.
 * these functions could be run multiple times; this would result in a fatal error.
 */

/**
 * custom option and settings
 */
function hw_feedback_settings_init() {
    // Register a new setting for "hw_feedback" page.
    register_setting( 'hw_feedback', 'hw_feedback_options' );

    // Register a new section in the "hw_feedback" page.
    add_settings_section(
        'hw_feedback_section_api_settings',
        __( 'CQC API settings', 'hw_feedback' ), 'hw_feedback_section_api_settings_callback',
        'hw_feedback'
    );

    // Register a new field in the "hw_feedback_section_developers" section, inside the "hw_feedback" page.
    add_settings_field(
        'hw_feedback_field_local_authority', // As of WP 4.6 this value is used only internally.
                                // Use $args' label_for to populate the id inside the callback.
            __( 'Local Authority', 'hw_feedback' ),
        'hw_feedback_field_local_authority_cb',
        'hw_feedback',
        'hw_feedback_section_api_settings',
        array(
            'label_for'         => 'hw_feedback_field_local_authority',
            'class'             => 'hw_feedback_row',
            'hw_feedback_custom_data' => 'custom',
        )
    );
    // Register a new field in the "hw_feedback_section_api_settings" section, inside the "hw_feedback" page.
    add_settings_field(
        'hw_feedback_field_partner_code', // As of WP 4.6 this value is used only internally.
                                // Use $args' label_for to populate the id inside the callback.
            __( 'Partner Code', 'hw_feedback' ),
        'hw_feedback_field_partner_code_cb',
        'hw_feedback',
        'hw_feedback_section_api_settings',
        array(
            'label_for'         => 'hw_feedback_field_partner_code',
            'class'             => 'hw_feedback_row',
            'hw_feedback_custom_data' => 'custom',
        )
    );
    // Register a new field in the "hw_feedback_section_api_settings" section, inside the "hw_feedback" page.
    add_settings_field(
        'hw_feedback_field_api_cache_path', // As of WP 4.6 this value is used only internally.
                                // Use $args' label_for to populate the id inside the callback.
            __( 'API Cache Path', 'hw_feedback' ),
        'hw_feedback_field_api_cache_path_cb',
        'hw_feedback',
        'hw_feedback_section_api_settings',
        array(
            'label_for'         => 'hw_feedback_field_api_cache_path',
            'class'             => 'hw_feedback_row',
            'hw_feedback_custom_data' => 'custom',
        )
    );
}

/**
 * Register our hw_feedback_settings_init to the admin_init action hook.
 */
add_action( 'admin_init', 'hw_feedback_settings_init' );

/**
 * custom option and settings
 */
function hw_feedback_settings_defaults() {
  if ( ! get_option( 'hw_feedback_options')) {
    // create api cache path
    $upload_dir = wp_upload_dir();
    $api_cache_path = $upload_dir['basedir'];
    $api_cache_path .= "/api_cache/";
    // create defaults
    $defaults = array(
      'hw_feedback_field_local_authority' => '',
      'hw_feedback_field_partner_code' => '',
      'hw_feedback_field_api_cache_path' => $api_cache_path
    );
    update_option( 'hw_feedback_options', $defaults);
    error_log("hw-feedback: api_cache_path updated " . $api_cache_path);
  }
}

/**
 * Register our hw_feedback_settings_defaults to the current_screen action hook.
 */
// Not sure when to hook this but this works
add_action( 'plugins_loaded', 'hw_feedback_settings_defaults' );

/**
 * Custom option and settings:
 *  - callback functions
 */


/**
 * API section callback function.
 *
 * @param array $args  The settings array, defining title, id, callback.
 */
function hw_feedback_section_api_settings_callback( $args ) {
    ?>
    <p id="<?php echo esc_attr( $args['id'] ); ?>">The CQC API must be configured for you local area. You can <a href="https://anypoint.mulesoft.com/exchange/portals/care-quality-commission-5/4d36bd23-127d-4acf-8903-ba292ea615d4/cqc-syndication-1/" target="_blank">read more about the API here</a>.</p>
    <?php
}

/**
 * local_authority field callbakc function.
 *
 * WordPress has magic interaction with the following keys: label_for, class.
 * - the "label_for" key value is used for the "for" attribute of the <label>.
 * - the "class" key value is used for the "class" attribute of the <tr> containing the field.
 * Note: you can add custom key value pairs to be used inside your callbacks.
 *
 * @param array $args
 */
function hw_feedback_field_local_authority_cb( $args ) {
    // Get the value of the setting we've registered with register_setting()
    $options = get_option( 'hw_feedback_options' );
    ?>
    <select
            id="<?php echo esc_attr( $args['label_for'] ); ?>"
            data-custom="<?php echo esc_attr( $args['hw_feedback_custom_data'] ); ?>"
            name="hw_feedback_options[<?php echo esc_attr( $args['label_for'] ); ?>]">
        <?php hw_feedback_generate_local_auth_options($args,$options); ?>
    </select>
    <!--<p class="description">
        <?php // esc_html_e( 'You take the blue pill and the story ends. You wake in your bed and you believe whatever you want to believe.', 'hw_feedback' ); ?>
    </p>
    <p class="description">
        <?php // esc_html_e( 'You take the red pill and you stay in Wonderland and I show you how deep the rabbit-hole goes.', 'hw_feedback' ); ?>
    </p> -->
    <?php
}

/**
 * partner_code field callback function.
 *
 * WordPress has magic interaction with the following keys: label_for, class.
 * - the "label_for" key value is used for the "for" attribute of the <label>.
 * - the "class" key value is used for the "class" attribute of the <tr> containing the field.
 * Note: you can add custom key value pairs to be used inside your callbacks.
 *
 * @param array $args
 */
function hw_feedback_field_partner_code_cb( $args ) {
    // Get the value of the setting we've registered with register_setting()
    $options = get_option( 'hw_feedback_options' );
    ?>
    <input type="text"
      id="<?php echo esc_attr( $args['label_for'] ); ?>"
      name="hw_feedback_options[<?php echo esc_attr( $args['label_for'] ); ?>]"
      value="<?php echo isset( $options[ $args['label_for'] ] ) ? ( ( $options[ $args['label_for'] ]) ) : ( '' ); ?>">
    <p class="description">
        <?php esc_html_e( "In order to provide CQC's public data services we ask that all organisations consuming this API add an additional query parameter to all requests. An informative but concise code representing your organisation should be chosen.", 'hw_feedback' ); ?>
    </p>
    <?php
}

/**
 * api_cache_path field callback function.
 *
 * WordPress has magic interaction with the following keys: label_for, class.
 * - the "label_for" key value is used for the "for" attribute of the <label>.
 * - the "class" key value is used for the "class" attribute of the <tr> containing the field.
 * Note: you can add custom key value pairs to be used inside your callbacks.
 *
 * @param array $args
 */
function hw_feedback_field_api_cache_path_cb( $args ) {
    // Get the value of the setting we've registered with register_setting()
    $options = get_option( 'hw_feedback_options' );
    ?>
    <input type="text"
      id="<?php echo esc_attr( $args['label_for'] ); ?>"
      name="hw_feedback_options[<?php echo esc_attr( $args['label_for'] ); ?>]"
      value="<?php echo isset( $options[ $args['label_for'] ] ) ? ( ( $options[ $args['label_for'] ]) ) : ( '' ); ?>">
    <p class="description">
        <?php esc_html_e( "Set the path to store cached files from the CQC API.", 'hw_feedback' ); ?>
    </p>
    <?php
}

/**
 * Top level menu callback function
 */
function hw_feedback_options_page_html() {
    // check user capabilities
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    // add error/update messages

    // check if the user have submitted the settings
    // WordPress will add the "settings-updated" $_GET parameter to the url
    if ( isset( $_GET['settings-updated'] ) ) {
        // add settings saved message with the class of "updated"
        add_settings_error( 'hw_feedback_messages', 'hw_feedback_message', __( 'Settings Saved', 'hw_feedback' ), 'updated' );
    }

    // show error/update messages
    settings_errors( 'hw_feedback_messages' );
    ?>
    <div class="wrap">
        <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
        <form action="options.php" method="post">
            <?php
            // output security fields for the registered setting "hw_feedback"
            settings_fields( 'hw_feedback' );
            // output setting sections and their fields
            // (sections are registered for "hw_feedback", each field is registered to a specific section)
            do_settings_sections( 'hw_feedback' );
            // output save settings button
            submit_button( 'Save Settings' );
            ?>
        </form>
    </div>
    <?php
}
?>
