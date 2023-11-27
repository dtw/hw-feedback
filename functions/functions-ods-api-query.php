<?php

/**
 * Bootstrap ODS Role Code from CQC Inspection Category
 *
 * @package   hw-feedback
 * @author    Phil Thiselton <dibblethewrecker@gmail.com>
 * @license   GPL-2.0+
 * @copyright 2022 Phil Thiselton
 *
 * Description: Takes a local_service with no ODS Code and generates ODS Role Codes based on CQC inspection Categhory Codes
 * @param array $post
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

  );

}
?>