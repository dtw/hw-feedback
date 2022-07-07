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
    // CQC API root
    $url = 'https://api.cqc.org.uk/public/v1';
    $request_url = $url . '/' . $collection_name . '/' . $id . '?partnerCode=HW_BUCKS';
    $curl = curl_init($request_url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    // no auth needed
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
      die("Error in ".__FUNCTION__.": perPage request $parameters->perPage greater than 1000");
    }
    if ( ! $parameters['localAuthority']) {
      die("Error in ".__FUNCTION__.": localAuthority required e.g. Buckinghamshire");
    }
    if ( ! $parameters['primaryInspectionCategoryCode']) {
      die("Error in ".__FUNCTION__.": primaryInspectionCategoryCode required");
    }
    // CQC API root
    $url = 'https://api.cqc.org.uk/public/v1/locations?';

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
    // no auth needed
    $response = curl_exec($curl);
    curl_close($curl);
    return $response;
  }

