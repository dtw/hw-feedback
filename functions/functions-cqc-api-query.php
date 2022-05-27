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

  function cqcapiquery($collection_name,$id) {
    // CQC API root
    $url = 'https://api.cqc.org.uk/public/v1/';
    $request_url = $url . '/' . $collection_name . '/' . $id;
    $curl = curl_init($request_url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    // no auth needed
    $response = curl_exec($curl);
    curl_close($curl);
    return $response;
  }
