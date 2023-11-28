<?php

/**
 * Bootstrap ODS Role Code from CQC Inspection Category
 *
 * @package   hw-feedback
 * @author    Phil Thiselton <dibblethewrecker@gmail.com>
 * @license   GPL-2.0+
 * @copyright 2022 Phil Thiselton
 *
 * Description: Takes a local_service with no ODS Code and generates ODS Role Codes based on CQC inspection Category Codes
 *              DOES NOT CHECK ODS FHIR API!
 * @param int $post_id
 */

function hw_feedback_ods_role_code_bootstrap($post_id)
{
  error_log('hw-feedback: ods bootstrap start');
  // get the local_service post
  $single_local_service = get_post($post_id);
  // get ods code
  $ods_code = get_post_meta($single_local_service->ID, 'hw_services_ods_code', true);
  // some error checks - if we have an ODS code we don't need to do this!
  if (empty($ods_code) || $ods_code = '') {
    // set the ODS Status to Unmatched, makes it easier to filter in the backend rather than leaving it blank
    wp_set_post_terms($single_local_service->ID, 'Unmatched', 'ods_status', false);
    // get ODS Role Codes for the post - there should be none but you never know!
    $ods_role_code_tax_terms = wp_get_post_terms($single_local_service->ID, 'ods_role_code', array("fields" => "ids"));
    // if there are no ODS Role Codes for the post
    if ( !isset($ods_role_code_tax_terms[0]) ) {
      // get cqc_inspection_catgeory as ids (not names or slugs)
      $cqc_inspection_category_tax_terms = wp_get_post_terms($single_local_service->ID, 'cqc_inspection_category', array("fields" => "names"));
      // check there is at least one
      if (isset($cqc_inspection_category_tax_terms[0])) {
        // for each cqc_inspection_category
        foreach ($cqc_inspection_category_tax_terms as $tax_term) {
          error_log('hw-feedback: ods tax_term' . $tax_term);
          // this is the map of Inspection Category to ODS Role Code
          $cqc_to_ods_map = array(
            'H1' => '198',
            'H12' => '7',
            'P1' => '110',
            'P1' => '65',
            'P2' => '76',
            'P4' => '175',
            'P6' => '87',
            'S1' => '269',
            'S2' => '104',
            'S2' => '270'
          );
          // check against the map
          error_log('hw-feedback: ods bootstrap checks');
          foreach ( $cqc_to_ods_map as $cqc => $ods_role ) {
            // and if there is a match, add the ods_role_code taxonomy
            if ( $tax_term == $cqc ) {
              // set new term - takes id of term
              wp_set_post_terms($single_local_service->ID, $ods_role, 'ods_role_code', false);
              error_log('hw-feedback: ods bootstrap success '. $ods_role);
              $return_string = 'changed';
            }
          }
        }
      }
    }
  }
  error_log('hw-feedback: ods bootstrap end');
  return $return_string;
}

/**
 * Batch query ODS status and role codes
 *
 * @package   hw-feedback
 * @author    Phil Thiselton <dibblethewrecker@gmail.com>
 * @license   GPL-2.0+
 * @copyright 2022 Phil Thiselton
 *
 * Description: Takes a mode, checks all local_services:
 * 'bootstrap' - runs hw_feedback_ods_role_code_bootstrap
 * 'update' - runs hw_feedback_check_ods_registration_single
 * 
 * @param string See above
 */
function hw_feedback_ods_checks($mode)
{

  /* unhook the hw_feedback_check_cqc_registration_status_single function
  not sure but this might start an infinite loop otherwise
  error_log('hw_feedback: unhook the hw_feedback_check_cqc_registration_status_single function');
  remove_action( 'updated_post_meta', 'hw_feedback_save_local_services_meta'); */

  // create array of local_services post_ids that have had their reg changed
  $role_code_status_changed = array();

  global $post;
  // get local_services
  $args = array(
    'post_type'       => 'local_services',
    'posts_per_page'  => -1,
    'post_status' => array('publish', 'private')
  );

  $services = get_posts($args);
  foreach ($services as $hw_feedback_post) : setup_postdata($hw_feedback_post);
  if ( $mode == 'bootstrap') {
    // Update the local_service with CQC data
    // if reg status has been changed, this will return 'changed'
    $role_code_status = hw_feedback_ods_role_code_bootstrap($hw_feedback_post->ID);
    if ($role_code_status === 'changed') {
      array_push($role_code_status_changed, $hw_feedback_post->ID);
    }
    error_log('hw-feedback: ods bootstrap check complete!');
  } else if ( $mode == 'get_code') {

  }
  // remove ALL terms
  //wp_remove_object_terms( $post_id, array('registered','deregistered','not-registered'), 'cqc_reg_status' );
  endforeach;
  error_log('hw-feedback: ods checks complete!');
  // restore the hw_feedback_check_cqc_registration_status_single function hook
  //add_action( 'updated_post_meta', 'hw_feedback_save_local_services_meta', 10, 4);

  // set php mailer variables
  $to = get_option('admin_email');
  $subject = "Local Services - ODS Role Code updates (" . parse_url(get_site_url(), PHP_URL_HOST) . ")";
  // set headers to allow HTML
  $headers = array('Content-Type: text/html; charset=UTF-8');
  // build the content
  $formatted_message = '<p>Hi!</p><p>The ODS Role Code update completed successfully at ' . date('d/m/Y h:i:s a', time()) . '</p>';
  // check if there were changes
  if (empty($role_code_status_changed)) {
    $formatted_message .= '<p>There were no Role Code changes.</p>';
  } else {
    // compose an email contain reg changes
    $formatted_message .= '<p>The Role Codes of the following services were updated automatically:</p><ul>';
    foreach ($role_code_status_changed as $post_id) {
      $location_id = get_post_meta($post_id, 'hw_services_cqc_location', true);
      $formatted_message .= '<li>' . get_the_title($post_id) . ' - <a href="https://www.cqc.org.uk/location/' . $location_id . '" target="_blank">' . $location_id . '</a> (';
      $formatted_message .= '<a href="' . get_site_url() . '/wp-admin/post.php?post=' . $post_id . '&action=edit">Edit</a> | <a href="' . get_post_permalink($post_id) . '">View</a>)</li>';
    }
    $formatted_message .= '</ul>';
  }
  $formatted_message .= '<p>Hugs and kisses!</p>';
  $sent = wp_mail($to, $subject, stripslashes($formatted_message), $headers);

  if ($sent) {
    error_log('hw-feedback: ods role update email sent');
  } else {
    error_log('hw-feedback: ods role update email failed');
  }
}

// add a wrapper to call from cron "add_action"
function hw_feedback_ods_checks_bootstrap()
{
  hw_feedback_ods_checks('bootstrap');
}

// add a wrapper to call from cron "add_action"
function hw_feedback_ods_checks_update()
{
  hw_feedback_ods_checks('update');
}

/**
 * Query ODS FHIR API by ODS Code
 *
 * @package   hw-feedback
 * @author    Phil Thiselton <dibblethewrecker@gmail.com>
 * @license   GPL-2.0+
 * @copyright 2023 Phil Thiselton
 *
 * Description: Looks up organisation on ODS using ODS Code
 * 
 * @param string ODS code
 */
function hw_feedback_ods_api_query_by_code($code)
{
  $options = get_option('hw_feedback_options');
  // ODS API root
  $url = 'https://directory.spineservices.nhs.uk/STU3/Organization';
  $request_url = $url . '/' . $code;
  $curl = curl_init($request_url);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
  // no auth needed
  $response = curl_exec($curl);
  curl_close($curl);
  return $response;
}

?>