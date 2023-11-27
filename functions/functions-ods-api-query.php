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

function hw_feedback_ods_role_code_bootstrap($collection_name, $id)
{
  // check there are no ODS roles assigned to the service

  // if not, add ods_role_code based on cqc_inspection_category based on these pairs:

  // get each cqc_inspection_category, check against the list, add the ods_role_code taxonomy

}
?>