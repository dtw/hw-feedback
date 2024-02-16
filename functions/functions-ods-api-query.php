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
  // get the local_service post
  $single_local_service = get_post($post_id);
  // get ods code
  $ods_code = get_post_meta($single_local_service->ID, 'hw_services_ods_code', true);
  // some error checks - if we have an ODS code we don't need to do this!
  if (empty($ods_code) || $ods_code = '') {
    // set the ODS Status to Unmatched, makes it easier to filter in the backend rather than leaving it blank
    wp_set_post_terms($single_local_service->ID, 'Unmatched', 'ods_status', false);
    // get ODS Role Codes for the post - there should be none but you never know!
    $ods_role_code_tax_terms = wp_get_post_terms($single_local_service->ID, 'ods_role_code', array("fields" => "names"));
    // if there are no ODS Role Codes for the post
    if ( !isset($ods_role_code_tax_terms[0]) ) {
      // get cqc_inspection_catgeory as ids (not names or slugs)
      $cqc_inspection_category_tax_terms = wp_get_post_terms($single_local_service->ID, 'cqc_inspection_category', array("fields" => "names"));
      // check there is at least one
      if (isset($cqc_inspection_category_tax_terms[0])) {
        // for each cqc_inspection_category
        foreach ($cqc_inspection_category_tax_terms as $tax_term) {
          // this is the map of Inspection Category to ODS Role Code
          $cqc_to_ods_map = array(
            'H1' => '198',
            //'H12' => '7',
            'P1' => '110',
            //'P1' => '65',
            'P2' => '76',
            'P4' => '175',
            //'P6' => '87',
            'S1' => '101',
            'S1' => '269',
            'S2' => '104',
            'S2' => '270'
          );
          // check against the map
          error_log('hw-feedback: ods bootstrap checks');
          foreach ( $cqc_to_ods_map as $cqc => $ods_role ) {
            // and if there is a match, add the ods_role_code taxonomy
            if ( $tax_term == $cqc ) {
              // set new term - takes name of term
              wp_set_post_terms($single_local_service->ID, $ods_role, 'ods_role_code', false);
              error_log('hw-feedback: ods bootstrap success '. $tax_term . ' matched to '.$ods_role);
              $return_string = 'changed';
            }
          }
        }
      } else {
        // no CQC categories for this post!
        error_log('hw-feedback: ods bootstrap no CQC categories');
        $return_string = 'no_cqc';
      }
    } else {
      error_log('hw-feedback: ods bootstrap role code terms exist');
      $return_string = 'skipped';
    }
  } else {
    error_log('hw-feedback: ods bootstrap skipped');
    $return_string = 'matched';
  }
  // error_log('hw-feedback: ods bootstrap end');
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

  // create array of local_services post_ids that have had their role_code changed
  $role_code_status_changed = array();
  $ods_status_changed = array();
  $ods_best_match_success = array();

  global $post;
  // get local_services - is a sub query / subquery tax query to only find services not 'deregistered' OR 'archived'
  $args = array(
    'post_type'       => 'local_services',
    'posts_per_page'  => -1,
    'post_status' => array('publish', 'private'),
    'tax_query' => array(
      array(
        'taxonomy' => 'cqc_reg_status',
        'field'    => 'slug',
        'terms'    => array('deregistered','archived'),
        'operator' => 'NOT IN'
      )
    )
  );

  $services = get_posts($args);
  foreach ($services as $hw_feedback_post) : setup_postdata($hw_feedback_post);
    if ( $mode == 'bootstrap') {
      $role_code_status = hw_feedback_ods_role_code_bootstrap($hw_feedback_post->ID);
      if ($role_code_status === 'changed') {
        $ods_best_match_outcome = hw_feedback_ods_best_match($hw_feedback_post->ID);
        if ($ods_best_match_outcome === 'success') {
          array_push($ods_best_match_success, $hw_feedback_post->ID);
        } else {
          array_push($role_code_status_changed, $hw_feedback_post->ID);
        }
      } elseif ($role_code_status === 'no_cqc'){
        // array_push($ods_no_cqc, $hw_feedback_post->ID);
      }
      error_log('hw-feedback: ods bootstrap check complete! '. $role_code_status);
    } else if ( $mode == 'update') {
      $ods_status = hw_feedback_check_ods_registration_single($hw_feedback_post->ID);
      if ($ods_status === 'inactive') {
        array_push($ods_status_changed, $hw_feedback_post->ID);
      }
      error_log('hw-feedback: ods update complete!');
    }
    // remove ALL terms
    //wp_remove_object_terms( $post_id, array('registered','deregistered','not-registered'), 'cqc_reg_status' );
  endforeach;
  error_log('hw-feedback: ods ' . $mode . ' checks complete!');
  // restore the hw_feedback_check_cqc_registration_status_single function hook
  //add_action( 'updated_post_meta', 'hw_feedback_save_local_services_meta', 10, 4);

  // set php mailer variables
  $to = get_option('admin_email');
  $subject = "Local Services - ODS ". $mode ." (" . parse_url(get_site_url(), PHP_URL_HOST) . ")";
  // set headers to allow HTML
  $headers = array('Content-Type: text/html; charset=UTF-8');
  // build the content
  $formatted_message = '<p>Hi!</p><p>The ODS '. $mode .' check completed successfully at ' . date('d/m/Y h:i:s a', time()) . '</p>';
  // check if there were any Role Code changes
  if (empty($role_code_status_changed)) {
    $formatted_message .= '<p>There were no Role Code changes.</p>';
  } else {
    // compose an email contain reg changes
    $formatted_message .= '<p>The Role Codes of the following services were updated automatically:</p><ul>';
    foreach ($role_code_status_changed as $post_id) {
      $formatted_message .= '<li>' . get_the_title($post_id) . ' (';
      $formatted_message .= '<a href="' . get_site_url() . '/wp-admin/post.php?post=' . $post_id . '&action=edit">Edit</a> | <a href="' . get_post_permalink($post_id) . '">View</a>)</li>';
    }
    $formatted_message .= '</ul>';
  }
  // check if there were any best matches
  if ( !empty($ods_best_match_success) ) {
    // compose an email contain reg changes
    $formatted_message .= '<p>The following services were "best matched" and updated automatically:</p><ul>';
    foreach ($ods_best_match_success as $post_id) {
      $formatted_message .= '<li>' . get_the_title($post_id) . ' (';
      $formatted_message .= '<a href="' . get_site_url() . '/wp-admin/post.php?post=' . $post_id . '&action=edit">Edit</a> | <a href="' . get_post_permalink($post_id) . '">View</a>)</li>';
    }
    $formatted_message .= '</ul>';
  } elseif ( $mode == 'bootstrap' ) {
    $formatted_message .= '<p>There were no best matches.</p>';
  }
  // check if there were any status changes
  if (empty($ods_status_changed)) {
    $formatted_message .= '<p>There were no Status changes.</p>';
  } else {
    // compose an email contain reg changes
    $formatted_message .= '<p>The Status of the following services have changed:</p><ul>';
    foreach ($ods_status_changed as $post_id) {
      $ods_code = get_post_meta($post_id, 'hw_services_ods_code', true);
      $formatted_message .= '<li>' . get_the_title($post_id) . ' - <a href="https://directory.spineservices.nhs.uk/STU3/Organization/' . $ods_code . '" target="_blank">' . $ods_code . '</a> (';
      $formatted_message .= '<a href="' . get_site_url() . '/wp-admin/post.php?post=' . $post_id . '&action=edit">Edit</a> | <a href="' . get_post_permalink($post_id) . '">View</a>)</li>';
    }
    $formatted_message .= '</ul>';
  }
  $formatted_message .= '<p>Hugs and kisses!</p>';
  $sent = wp_mail($to, $subject, stripslashes($formatted_message), $headers);

  if ($sent) {
    error_log('hw-feedback: ods role ' . $mode . ' email sent');
  } else {
    error_log('hw-feedback: ods role ' . $mode . ' email failed');
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

/**
* Query ODS FHIR API
*
* @package   hw-feedback
* @author    Phil Thiselton <dibblethewrecker@gmail.com>
* @license   GPL-2.0+
* @copyright 2023 Phil Thiselton
*
* Description: Looks up organisation on ODS using specified search options.
* See https://digital.nhs.uk/developer/api-catalogue/organisation-data-service-fhir#get-/Organization
* 
* @param array search options
*/ 
function hw_feedback_ods_api_query_search($search_options)
{
  $options = get_option('hw_feedback_options');
  // basic search options
  $default_search_options = array(
    // 'active' => 'true',
  );
  // merge basic with passed array
  $search_options = array_merge(
    $default_search_options,
    $search_options);
  // build query
  $query = http_build_query($search_options, '', '&', PHP_QUERY_RFC3986);
  // ODS API root
  $url = 'https://directory.spineservices.nhs.uk/STU3/Organization';
  $request_url = $url . '?' . $query;
  error_log('hw-feedback: ods request url ' . $request_url);
  $curl = curl_init($request_url);
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
  // no auth needed
  $response = curl_exec($curl);
  curl_close($curl);
  return $response;
}

/**
 * Query for single local_service and update
 *
 * @package   hw-feedback
 * @author    Phil Thiselton <dibblethewrecker@gmail.com>
 * @license   GPL-2.0+
 * @copyright 2023 Phil Thiselton
 *
 * Description: Looks up organisation on ODS using ODS Code and updates ods_status and ods_role_codes
 * 
 * @param array search options
 */ 
function hw_feedback_check_ods_registration_single($post_id)
{
  $single_local_service = get_post($post_id);
  // get location id
  $ods_code = get_post_meta($single_local_service->ID, 'hw_services_ods_code', true);
  // some error checks
  if (!empty($ods_code) || $ods_code != '') {
    error_log('hw-feedback: ods_code ' . $ods_code);
    // call API
    $api_response = json_decode(hw_feedback_ods_api_query_by_code($ods_code));
    // get post tax terms as names
    $tax_terms = wp_get_post_terms($single_local_service->ID, 'ods_status', array("fields" => "names"));
    // if service is Inactive (which is done manually), close comments and bail
    if (isset($tax_terms[0]) && $tax_terms[0] == 'Inactive') {
      // update_comment_status($single_local_service->ID, "closed");
      error_log('hw-feedback: inactive true unchanged');
      return 'inactive unchanged';
    }
    // if there is a status from the api
    if (isset($api_response->active)) {
      $ods_status = '';
      // check the ODS Status is Active
      if ($api_response-> active == "true") {
        wp_set_post_terms($single_local_service->ID, 'Active', 'ods_status', false);
        //update_comment_status($single_local_service->ID, "open");
        $ods_status = 'active';
      } else if ($api_response->active == "false") {
        // if it is inactive
        // set ods_status to 'inactive'
        wp_set_post_terms($single_local_service->ID, 'Inactive', 'ods_status', false);
        // close comments
        // update_comment_status($single_local_service->ID, "closed");
        // just bail (for now)
        return  'inactive';
      }
      // get all ods_role_codes
      $ods_role_code_terms = get_terms(
        array(
          'taxonomy' => 'ods_role_code',
          'hide_empty' => false
        )
        );
      // clear existing role codes
      foreach ( $ods_role_code_terms as $term ) {
        wp_remove_object_terms($single_local_service->ID, $term->term_id, 'ods_role_code') ? error_log('hw-feedback: ods role code term ' . $term->name . ' deleted') : '';
      }
      // account for an infinite loop
      $max_count = isset($api_response->extension) ? ( count($api_response->extension) - 1 ) : 1;
      // Set role codes - start by counting how many roles there are. We skip extension[0] because it doesn't define a role.
      for ($counter = 1; $counter <= $max_count; $counter++) {
        // make sure the extension describes a role
        if ( isset($api_response->extension[$counter]->extension[0]->url) && $api_response->extension[$counter]->extension[0]->url == 'role' ) {
          // get role code and save it as a term
          wp_set_post_terms($single_local_service->ID, sanitize_text_field($api_response->extension[$counter]->extension[0]->valueCoding->code), 'ods_role_code', true);
          error_log('hw-feedback: ods role code term set ' . $api_response->extension[$counter]->extension[0]->valueCoding->code );
          // check for primary role
          if ( isset($api_response->extension[$counter]->extension[1]->url) && $api_response->extension[$counter]->extension[1]->url == 'primaryRole' && $api_response->extension[$counter]->extension[1]->valueBoolean ) {
            $primary_ods_role = $api_response->extension[$counter]->extension[0]->valueCoding->code;
            error_log('hw-feedback: ods_primary_role_code ' . $primary_ods_role);
          }
        }
      }
      return $ods_status;
    }
  }
}

/**
 * Generate a simple table to show multiple results from ODS
 *
 * @package   hw-feedback
 * @author    Phil Thiselton <dibblethewrecker@gmail.com>
 * @license   GPL-2.0+
 * @copyright 2023 Phil Thiselton
 *
 * Description: Looks up organisation on ODS using ODS Code and updates ods_status and ods_role_codes
 * 
 * @param object decoded from JSON json_decode()
 */
function hw_feedback_generate_ods_registration_table($results_object, $local_service_name)
{
  // get total from results - account for an infinite loop
  $max_count = isset($results_object->total) ? $results_object->total - 1 : 1;
  // start the table
  $table_content = '<table id="ods-registration-table">';
  // add some headers
  $table_content .=  '<tr id="ods-registration-header-row">' . 
  '<th>ODS Code</th>' .
  '<th>Name</th>' .
  '<th>Match (%)</th>' .
  '<th>Leve Distance</th>' .
  '<th>Last Updated</th>' .
  '<th>ODS API Link</th>' .
  '<th>Role Code</th>' .
  '<th>Role Name</th>' .
  '<th>Start Date</th>' .
  '</tr>';
  // we need to loop through [] entry
  for ($entry_counter = 0; $entry_counter <= $max_count; $entry_counter++) {
    $table_content .= '<!-- start entry ' . $entry_counter . ' of ' . $max_count . ' -->';
    $current_entry = $results_object->entry[$entry_counter];
    // how many extentions are there other than [0]
    $extension_count = ( count($current_entry->resource->extension) - 1 );
    // loop through extensions - we skip 0
    for ($extension_counter = 1; $extension_counter <=$extension_count ; $extension_counter++) {
      // check which row we're generating
      if ( $extension_counter >= 2)  {
        $table_content .= '<tr class="ods-registration-row" onclick="update_ods_code(\'hw-services-ods-code\',\'ods-code-' . $entry_counter . '\')"><td colspan="6"></td>';
      } else {
        // start the row
        $table_content .= '<tr id="ods-registration-' . $entry_counter . '" class="ods-registration-row first-row" onclick="update_ods_code(\'hw-services-ods-code\',\'ods-code-' . $entry_counter . '\')">';
        // print ODS code
        $table_content .= '<td id="ods-code-' . $entry_counter . '">' .  $current_entry->resource->id . '</td>';
        // print name
        $table_content .= '<td id="ods-registration-name-' . $entry_counter . '">' .  $current_entry->resource->name . '</td>';
        // compare the name using similar text
        similar_text(strtoupper(str_replace("&amp;", "&", $local_service_name)), strtoupper($current_entry->resource->name), $match_percent);
        // check strength to add highlight
        $match_perc_classes = ($match_percent >= 95) ? "ods-registration-name-match highlight-cell" : "ods-registration-name-match";
        // print percentage
        $table_content .= '<td id="ods-registration-match-perc-' . $entry_counter . '" class="'. $match_perc_classes.'">' . number_format((float)$match_percent, 2, '.', '') . '</td>';
        // compare the name using levenshtein
        $levenshtein_distance = levenshtein(strtoupper(str_replace("&amp;", "&", $local_service_name)), strtoupper($current_entry->resource->name), 1, 1, 0);
        // check distance to add highlight
        $leve_dist_classes = ($levenshtein_distance <=1) ? "ods-registration-name-match highlight-cell" : "ods-registration-name-match";
        // print distance
        $table_content .= '<td id="ods-registration-leve-dist-' . $entry_counter . '" class="' . $leve_dist_classes . '">' . $levenshtein_distance . '</td>';
        // format last updated - it's in ISO-8601 / ATOM, which is nice!
        $last_updated = DateTimeImmutable::createFromFormat(DateTimeInterface::ATOM, $current_entry->resource->meta->lastUpdated);
        // print last updated
        $table_content .= '<td id="ods-registration-last-update-' . $entry_counter . '">' .  $last_updated->format('Y-m-d') . '</td>';
        // print fullUrl
        $table_content .= '<td id="ods-registration-fullUrl-' . $entry_counter . '"><a href="' .  $current_entry->fullUrl . '" target="_blank" class="ods-registration-view-url">View</a></td>';
      }
      // print code
      $table_content .= '<td id="ods-registration-role-code-' . $entry_counter . '-' . $extension_counter . '">'. $current_entry->resource->extension[$extension_counter]->extension[0]->valueCoding->code . '</td>';
      // print display
      $table_content .= '<td id="ods-registration-display-' . $entry_counter . '-' . $extension_counter . '">' . $current_entry->resource->extension[$extension_counter]->extension[0]->valueCoding->display . '</td>';
      // print start date
      $table_content .= '<td id="ods-registration-start-date-' . $entry_counter . '-' . $extension_counter . '">' . $current_entry->resource->extension[$extension_counter]->extension[2]->valuePeriod->start . '</td>';
      // end the row
      $table_content .= '</tr><!-- end of extension '. $extension_counter . ' of ' . $extension_count . ' -->';
    }
    $table_content .= '<!-- end entry ' . $entry_counter . ' of ' . $max_count . ' -->';
  }
  // end the table
  $table_content .= '</table>';
  echo $table_content;
}

/**
 * Find best match based on ODS Role Code, postcode and service name
 *
 * @package   hw-feedback
 * @author    Phil Thiselton <dibblethewrecker@gmail.com>
 * @license   GPL-2.0+
 * @copyright 2023 Phil Thiselton
 *
 * Description: Takes a local_service with no ODS Code and at least one ODS Role Code, and postcode, and generates a best match for the ODS Code.
 * 
 * @param int $post_id
 */

function hw_feedback_ods_best_match($post_id)
{
  // get the local_service post
  $single_local_service = get_post($post_id);
  // get the name
  $local_service_name = get_the_title($single_local_service);
  // get ods code
  $ods_code = get_post_meta($single_local_service->ID, 'hw_services_ods_code', true);
  // some error checks - if we have an ODS code we don't need to do this!
  if (empty($ods_code) || $ods_code = '') {
    error_log('hw-feedback: ods '.$single_local_service->ID.' best_match start');
    // get ODS Role Codes for the post - there should be at least one!
    $ods_role_code_tax_terms = wp_get_post_terms($single_local_service->ID, 'ods_role_code', array("fields" => "names"));
    // if there are no ODS Role Codes for the post
    if (! isset($ods_role_code_tax_terms[0])) {
      error_log('hw-feedback: ods ' . $single_local_service->ID . ' best_match no role codes');
      return 'no_check';
    }
    foreach ($ods_role_code_tax_terms as $tax_term) {
      // get the postcode
      $postcode = get_post_meta($single_local_service->ID, 'hw_services_postcode', true);
      // set as search option
      $search_options['address-postalcode:exact'] = $postcode;
      // set tax term as search option
      $search_options['ods-org-role'] = $tax_term;
      // set for active only
      $search_options['active'] = 'true';
      // check the API
      $objodsapiquery = json_decode(hw_feedback_ods_api_query_search($search_options));
      // if there is no result
      if ($objodsapiquery->total == 0) {
        error_log('hw-feedback: ods ' . $single_local_service->ID . ' best_match no results');
        error_log('hw-feedback: ods ' . $single_local_service->ID . ' best_match org role '. $tax_term);
      }
      // if there is only one result
      elseif ($objodsapiquery->total == 1) {
        error_log('hw-feedback: ods ' . $single_local_service->ID . ' best_match 1 match');
        // check the name is 100% match
        similar_text(strtoupper(str_replace("&amp;", "&", $local_service_name)), strtoupper($objodsapiquery->entry[0]->resource->name), $match_percent);
        $name_match_percentage = number_format((float)$match_percent);
        if ($name_match_percentage == 100 ) {
          // set the ods_code
          $ods_code = $objodsapiquery->entry[0]->resource->id;
          update_post_meta($post_id, 'hw_services_ods_code', $ods_code);
          // set the ods_status to matched
          wp_set_post_terms($single_local_service->ID, 'Active', 'ods_status', false);
          error_log('hw-feedback: ods ' . $single_local_service->ID . ' best_match success');
          return 'success';
        } else {
          error_log('hw-feedback: ods ' . $single_local_service->ID . ' best_match no name match '. $name_match_percentage);
        }
      }
      // if there is more than one result
      elseif ($objodsapiquery->total > 1) {
        error_log('hw-feedback: ods ' . $single_local_service->ID . ' best_match multiple results ' . $objodsapiquery->total);
        // get total from results - account for an infinite loop
        $max_count = isset($objodsapiquery->total) ? $objodsapiquery->total - 1 : 1;
        // loop through results
        for ($entry_counter = 0; $entry_counter <= $max_count; $entry_counter++) {
          $current_entry = $objodsapiquery->entry[$entry_counter];
          // how many extentions are there other than [0]
          $extension_count = (count($current_entry->resource->extension) - 1);
          // loop through extensions - we skip 0
          for ($extension_counter = 1; $extension_counter <= $extension_count; $extension_counter++) {
            // check the name is 100% match
            similar_text(strtoupper(str_replace("&amp;", "&", $local_service_name)), strtoupper($current_entry->resource->name), $match_percent);
            $name_match_percentage = number_format((float)$match_percent);
            if ($name_match_percentage == 100) {
              // set the ods_code
              $ods_code = $current_entry->resource->id;
              update_post_meta($post_id, 'hw_services_ods_code', $ods_code);
              // set the ods_status to matched
              wp_set_post_terms($single_local_service->ID, 'Active', 'ods_status', false);
              error_log('hw-feedback: ods ' . $single_local_service->ID . ' best_match_multiple success');
              return 'success';
            } else {
              error_log('hw-feedback: ods ' . $single_local_service->ID . ' best_match_multiple no name match ' . $name_match_percentage);
            }
          }
        }
      }
    }
    error_log('hw-feedback: ods ' . $single_local_service->ID . ' best_match end');
  }
}
?>