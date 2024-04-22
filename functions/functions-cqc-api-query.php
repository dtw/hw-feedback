<?php
/**
 * Output feedback star rating
 *
 * @package   hw-feedback
 * @author    Phil Thiselton <dibblethewrecker@gmail.com>
 * @license   GPL-2.0+
 * @copyright 2022 Phil Thiselton
 *
 * Description: Takes a collection (locations/providers) and id and returns info from CQC API
 * @param array $post
 */

  function hw_feedback_cqc_api_query_by_id($collection_name,$id) {
    $options = get_option( 'hw_feedback_options' );
    // CQC API root
    $url = 'https://api.service.cqc.org.uk/public/v1';
    $request_url = $url . '/' . $collection_name . '/' . $id;
    $curl = curl_init($request_url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    // auth needed!
    curl_setopt($curl, CURLOPT_HTTPHEADER, [
      'Ocp-Apim-Subscription-Key:'. $options['hw_feedback_field_api_subscription_key']
    ]);
    $response = curl_exec($curl);
    curl_close($curl);
    return $response;
  }

// Query by CQC locations //
  function hw_feedback_cqc_api_query_locations($parameters) {
    // set some very low defaults - getting this wrong would be bad
    $defaults = array (
      'page' => '1',
      'perPage' => '20',
    );
    $parameters = array_merge($defaults,$parameters);
    // some sanity checks
    if ($parameters['perPage'] > 1000) {
      die("Error in ".__FUNCTION__.": perPage request " . $parameters['perPage'] . " greater than 1000");
    }
    if ( ! $parameters['localAuthority']) {
      die("Error in ".__FUNCTION__.": localAuthority required e.g. Buckinghamshire");
    }
    if ( ! $parameters['primaryInspectionCategoryCode']) {
      die("Error in ".__FUNCTION__.": primaryInspectionCategoryCode required");
    }
    // CQC API root
    $url = 'https://api.service.cqc.org.uk/public/v1/locations?';

    /* Use this URL for testing to keep results down
    / Independent Ambulance, Primary Dental Care, Primary Medical Services, NHS Healthcare Organisation, Independent Healthcare Org, or Social Care Org
    / inspectionDirectorate is one of
    /   Primary medical services
    /   Adult social care
    /   Hospitals
    /
    / gacServiceTypeDescription is one of
    /   Dental+service
    /   Domiciliary care service
    /   Care home service with nursing
    /   Care home service without nursing
    /   Community health care services - Nurses Agency only
    /   Doctors treatment service
    /
    / primaryInspectionCategoryCode
    /   P1 - Dentists
    /   P2 - GP Practices
    /   P3 - Out of hours
    /   P4 - Prison Healthcare
    /   P5 - Remote clinical advice
    /   P6 - Urgent care services & mobile doctors
    /   P7 - Independent consulting doctors
    /   P8 - Slimming Clinics
    /   P9 -
    /   S1 - Residential social care
    /   S2 - Community based adult social care services
    /   S3 - Hospice services
    /   H1 - Acute hospital - NHS non-specialist
    /   H2 - Acute hospital - NHS specialist
    /   H3 - Acute hospital - Independent non-specialist
    /   H4 - Acute hospital - Independent specialist
    /   H5 - Ambulance service
    /   H6 - Community health - NHS & Independent
    /   H7 - Community substance misuse
    /   H8 - Mental health - community & hospital - independent
    /   H9 - Mental health - community & residential - NHS
    /   H10 - Residential substance misuse
    /   H11 - Acute Services - Non Hospital
    /   H12 - Hospice services
    */
    $request_url = $url . http_build_query($parameters);
    $curl = curl_init($request_url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    // auth needed!
    $options = get_option( 'hw_feedback_options' );
    curl_setopt($curl, CURLOPT_HTTPHEADER, [
      'Ocp-Apim-Subscription-Key:' . $options['hw_feedback_field_api_subscription_key']
    ]);
    $response = curl_exec($curl);
    curl_close($curl);
    return $response;
  }

function hw_feedback_inspection_category_to_service_type($inspection_category) {
  $category_to_type_mapping = array(
    'P1' => 'Dentist',
    'P2' => 'General practice',
    'P3' => 'Urgent & emergency care',
    'P4' => 'Prison healthcare',
    'P6' => 'Urgent & emergency care',
    'P7' => 'Independent consulting doctor', // must be added
    //'S1' => 'Care home',
    //'S2' => 'Home care agency',
    'S3' => 'Hospice service', // must be added
    'H1' => 'Hospital',
    'H2' => 'Hospital',
    'H3' => 'Hospital',
    //'H4' => 'Hospital',
    'H5' => 'Ambulance service', // must be added
    'H6' => 'Community healthcare service', // must be added
    'H8' => 'Mental Health',
    'H9' => 'Mental Health'
  );
  if (key_exists($inspection_category,$category_to_type_mapping)) {
    return $category_to_type_mapping[$inspection_category];
  } else {
    return false;
  }
}

function hw_feedback_gac_category_to_service_type($gac_category) {
  $gac_to_type_mapping = array(
    'Community healthcare service' => 'Community Healthcare service',
    'Hospice services' => 'Hospice service',
    'Diagnostic and/or screening service' => 'Diagnostic / screening service', // must be added
    'Domiciliary care service' => 'Home care agency', // must be added
    'Supported living service' => 'Supported living service', // must be added
    'Care home service with nursing' => 'Care home with nursing', // must be added
    'Care home service without nursing' => 'Care home',
  );
  if (key_exists($gac_category,$gac_to_type_mapping)) {
    return $gac_to_type_mapping[$gac_category];
  } else {
    return "Other";
  }
}

function hw_feedback_generate_local_auth_options($args,$options) {
  // Our JSON file
  $json_filename = plugin_dir_path( __DIR__ ).'CQC_Local_Authority_Names.json';
  // open the file
  $json_file = fopen($json_filename, "r") or die("hw-feedback: Unable to read file $json_filename");
  // read file and convert to array
  $local_authority_names = json_decode(fread($json_file,filesize($json_filename)));
  fclose($json_file) && error_log("hw-feedback: $json_filename closed post-read");
  // add a blank as top/default
  ?> <option value=""></option> <?php
  foreach ($local_authority_names as $option) {
    $local_authority = $option->LocalAuthority; ?>
    <option value="<?php echo $local_authority ?>" <?php echo isset( $options[ $args['label_for'] ] ) ? ( selected( $options[ $args['label_for'] ], $local_authority, false ) ) : ( '' ); ?>>
        <?php echo $local_authority ?>
    </option>
  <?php }
}

function hw_feedback_check_cqc_registration_status_single($post_id) {
  $single_local_service = get_post($post_id);
  // get location id
  $location_id = get_post_meta( $single_local_service->ID, 'hw_services_cqc_location', true );
  // some error checks
  if ( ! empty( $location_id ) || $location_id != '') {
    error_log('hw-feedback: location_id '.$location_id);
    // call API
    $api_response = json_decode(hw_feedback_cqc_api_query_by_id('locations',$location_id));
    // get post tax terms as names
    $tax_terms = wp_get_post_terms( $single_local_service->ID, 'cqc_reg_status', array( "fields" => "names" ));
    // if service is Archived (which is done manually), close comments and bail
    if ( isset($tax_terms[0]) && $tax_terms[0] == 'Archived' ) {
      update_comment_status ($single_local_service->ID,"closed");
      error_log('hw-feedback: archive true');
      return 'archived';
    }
    // if there is a reg status from the api
    if ( isset($api_response->registrationStatus) ) {
      $reg_status = '';
      // is it different from the current status AND NOT Archived
      if ( ! isset($tax_terms[0]) || $tax_terms[0]  != $api_response->registrationStatus ) {
        // set new terms - takes names of terms not slugs...
        wp_set_post_terms( $single_local_service->ID, sanitize_text_field($api_response->registrationStatus) , 'cqc_reg_status', false );
        $reg_status = 'changed';
        error_log('hw-feedback: tax_terms '.$tax_terms[0]);
      }
        // try and override Registered local_services to "Allow Comments"
      if ( $api_response->registrationStatus == "Registered" ) {
        update_comment_status ($single_local_service->ID,"open");
      }
      // set Inspection Categories
      $primary_inspection_category = hw_feedback_update_inspection_categories($single_local_service->ID,$api_response->inspectionCategories);
      error_log('hw-feedback: primary_inspection_category '.$primary_inspection_category);

      // update the excerpt if blank
      if ( ! has_excerpt($single_local_service->ID) ) {
        $post_excerpt = hw_feedback_generate_local_service_excerpt ($primary_inspection_category, $api_response);
        wp_update_post(array(
          'ID' => $single_local_service->ID,
          'post_excerpt' => $post_excerpt));
      }
      return $reg_status;
    // otherwise, it has a location id locally but that is not listed by CQC
    } else {
      // set new terms - takes names of terms not slugs...
      wp_set_post_terms( $single_local_service->ID, 'Not registered' , 'cqc_reg_status', false );
    }
  } else {
    wp_set_post_terms( $single_local_service->ID, 'Not applicable' , 'cqc_reg_status', false );
  }
}

/* Run CQC update when local_services are SAVED */
function hw_feedback_update_local_services($post_id, $post, $update) {
  remove_action( 'save_post_local_services', 'hw_feedback_update_local_services', 10, 3);
  global $pagenow;
  // only do something if the post is UPDATED
  if (( 'post.php' === $pagenow ) && ( $update )) {
    error_log('hw-feedback: update action');
    hw_feedback_check_cqc_registration_status_single($post_id);
    hw_feedback_ods_role_code_bootstrap($post_id);
    hw_feedback_check_ods_registration_single($post_id);
  }
  add_action( 'save_post_local_services', 'hw_feedback_update_local_services', 10, 3);
}

// fires when local_services post type is SAVED - $post_id, WP_Post $post, bool $update
add_action( 'save_post_local_services', 'hw_feedback_update_local_services', 10, 3);

/* Run CQC update when local_services meta data is SAVED */
function hw_feedback_save_local_services_meta($meta_id, $post_id, $meta_key, $_meta_value) {
  global $pagenow;
  remove_action( 'updated_post_meta', 'hw_feedback_save_local_services_meta', 10, 4);
  // only do something if the hw_services_cqc_location is UPDATED
  if (( 'post.php' === $pagenow ) && ($meta_key == 'hw_services_cqc_location')) {
    hw_feedback_check_cqc_registration_status_single($post_id);
    hw_feedback_ods_role_code_bootstrap($post_id);
    hw_feedback_check_ods_registration_single($post_id);
  }
  add_action( 'updated_post_meta', 'hw_feedback_save_local_services_meta', 10, 4);
}

// fires when meta data updated, which is not the same as...
add_action( 'updated_post_meta', 'hw_feedback_save_local_services_meta', 10, 4);
// fires when meta data is added to a post
add_action( 'added_post_meta', 'hw_feedback_save_local_services_meta', 10, 4);