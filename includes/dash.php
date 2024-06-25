<?php
/**
 * Add the top level menu page.
 */
function hw_feedback_add_menus() {
	add_menu_page(
		'Healthwatch Feedback Settings',
		'HW Feedback',
		'manage_options',
		'hw-feedback',
		'hw_feedback_options_page_html',
		plugins_url( 'hw-feedback/images/hw-feedback-icon.png' )
	);
	//apparently we need to duplicate the top page (?) https://developer.wordpress.org/reference/functions/add_submenu_page/#comment-446
	add_submenu_page(
		'hw-feedback',
		'Healthwatch Feedback Settings',
		'Settings',
		'manage_options',
		'hw-feedback'
	);
	// then add the submenu
	add_submenu_page(
		'hw-feedback',
		'CQC Data Import',
		'CQC Data Import',
		'manage_options',
		'cqc-data-import',
		'hwbucks_cqc_data_import_contents',
	);
}

/**
* Register our hw_feedback_add_menus to the admin_menu action hook.
*/
add_action( 'admin_menu', 'hw_feedback_add_menus' );

	function hwbucks_cqc_data_import_contents() {
		// save the hw-feedback-form-inspection-category from POST
		$primary_inspection_category = isset($_POST['hw-feedback-form-inspection-category']) ? $_POST['hw-feedback-form-inspection-category'] : 'P1';
		$force_refresh = isset($_POST['hw-feedback-force-refresh']) ? $_POST['hw-feedback-force-refresh'] : false;
		$preview_only = isset($_POST['hw-feedback-preview-only']) ? $_POST['hw-feedback-preview-only'] : false;
		// default to 5
		$import_number = isset($_POST['hw-feedback-form-import-number']) ? $_POST['hw-feedback-form-import-number'] : 5;
		// get the options
		$options = get_option( 'hw_feedback_options');
	  // get the api_cache dir
	  $api_cache = $options['hw_feedback_field_api_cache_path'];
		// create new directory with 744 permissions if it does not exist yet
	  // owner will be the user/group the PHP script is run under
	  if ( !file_exists($api_cache) ) {
	      mkdir ($api_cache, 0744) or die("hw-feedback: Unable to create folder $api_cache");
	  }
		// make the local authority name dir path friendly
		$local_auth_name = $options['hw_feedback_field_local_authority'];
		$local_auth_name = str_replace(", ","_",$local_auth_name);
		$local_auth_name = str_replace(" ","_",$local_auth_name);
		// build filename for inspection category
		$api_category_results = $api_cache . 'cqc_api_locations_' . $local_auth_name . '_' . $primary_inspection_category . '.json';
    // get the wordpress uploads folder - we can't use the cache because it might be set to an unlistable directory
    $uploads_folder = wp_upload_dir();
    // build filename for complete file
    $api_filename = 'cqc_api_locations_raw_' . time() . '.json';
    // build filename for latest json download in the uploads folder
    $api_download = $uploads_folder['basedir'] . '/' . $api_filename;

    // create a simple form
    ?>
    <div id="hw-feedback-data-import-tool">
      <h1>CQC Data Import Tool</h1>
      <div id="hw-feedback-data-import-tool-instructions">
        <p>This tool adds Locations from the CQC API that are not currently listed as a Local Service in hw-feedback. Note that it ignores Locations that are not marked as "Registered".</p>
        <p>To reduce system overhead, by default, this tool is limited to adding 5 Locations at a time. This should be plenty if the tool is used regularly. To further reduce overheads, the tool will only check for new Locations for one Inspection Category at a time.</p>
				<p>You can choose that category using the drop-down list.</p>
      </div>
			<?php // check the local_authority is set
			if (  $options['hw_feedback_field_local_authority'] === '' ) {// if no data saved ?>
				<div id="hw-feedback-api-alert" class="hw-feedback-alert" role="alert">The Local Authority must be selected in <a href="<?php menu_page_url('hw_feedback');?>">Settings</a>.</div>
			<?php } ?>
      <div id="hw-feedback-cqc-import-form" class="hw-feedback-cqc-form">
        <form name="hwfeedbackcqcimportform" action="admin.php?page=cqc-data-import" method="post">
					<div class="hw-feedback-cqc-import-form-row">
	          <label for="hw-feedback-form-inspection-category">Select Inspection Category</label>
	          <select class="hw-feedback-select widefat" name="hw-feedback-form-inspection-category" id="hw-feedback-form-inspection-category">
	          <?php // generate a list of inspection categories with name and description
						foreach (get_terms('cqc_inspection_category', array('hide_empty' => false)) as $key => $term) {
							if ($primary_inspection_category && $primary_inspection_category == $term->name ) {
								echo '<option value="'.$term->name.'" id="hw-feedback-'.$term->name.'" selected>'.$term->name.' - '.$term->description.'</option>';
							} else {
	            	echo '<option value="'.$term->name.'" id="hw-feedback-'.$term->name.'">'.$term->name.' - '.$term->description.'</option>';
							}
	          }
	          ?>
	          </select>
					</div>
					<div class="hw-feedback-cqc-import-form-row">
	          <label for="hw-feedback-form-import-number">Select number of Locations to preview/import</label>
	          <select class="hw-feedback-select" name="hw-feedback-form-import-number" id="hw-feedback-form-import-number">
	          <?php foreach (array(1, 5, 10, 20, 30, 40, 50) as $option) {
							if ($import_number && $import_number == $option ) {
								echo '<option value=' . $option . ' id="hw-feedback-import-number-' . $option . '" selected>' . $option . '</option>';
							} else {
								echo '<option value=' . $option . ' id="hw-feedback-import-number-' . $option . '">' . $option . '</option>';
							}
						}
	          ?>
	          </select>
					</div>
					<div class="hw-feedback-cqc-import-form-row">
						<label for="hw-feedback-force-refresh">Force refresh?</label>
						<?php if ( $force_refresh ) {
							echo '<input type="checkbox" id="hw-feedback-force-refresh" class="hw-feedback-checkbox" name="hw-feedback-force-refresh" value="true" checked>';
						} else {
							echo '<input type="checkbox" id="hw-feedback-force-refresh" class="hw-feedback-checkbox" name="hw-feedback-force-refresh" value="true">';
						} ?>
					</div>
					<div id="hw-feedback-preview-only-row" class="hw-feedback-cqc-import-form-row">
						<label for="hw-feedback-preview-only">Preview ONLY!</label>
						<input type="checkbox" id="hw-feedback-preview-only" class="hw-feedback-checkbox" name="hw-feedback-preview-only" value="true" checked>
						<div id="hw-feedback-import-alert" class="hw-feedback-alert" role="alert">Unmatched locations will be imported to the database!</div>
					</div>
          <div class="hw-feedback-cqc-import-form-row">
						<input type="submit" class="button-primary hw-feedback-form-submit" id="hw-feedback-form-submit" value="Preview" onclick="hw_feedback_submit_form();">
					</div>
        </form>
      </div>
			<div id="hw-feedback-cqc-import-throbber">
				<!-- <a title="Andrii.kalishuk, CC BY-SA 4.0 &lt;https://creativecommons.org/licenses/by-sa/4.0&gt;, via Wikimedia Commons" href="https://commons.wikimedia.org/wiki/File:Balls.gif"><img width="64" alt="Balls" src="https://upload.wikimedia.org/wikipedia/commons/7/7a/Balls.gif"></a> -->
				<a title="icon 'Ellipsis' from loading.io" href="https://loading.io/icon/"><img width="64" alt="Ellipsis" src="<?php echo plugins_url( 'hw-feedback/images/hw-feedback-throbber.svg' ) ?>" data-fallback="<?php echo plugins_url( 'hw-feedback/images/hw-feedback-throbber.gif' ) ?>"></a>
			</div>
    <?php

    if ($_SERVER['REQUEST_METHOD'] == 'POST') { ?>
			<div id="hw-feedback-cqc-import-results">
    <?php
      echo "<hr><h1>$primary_inspection_category Locations</h1>";
      // Get start time
      $executionStartTime = microtime(true);
			// get cache file modification time or create file
			if (file_exists($api_category_results)) {
        // this is the time in UTC which is what the server is set to
				$api_file_mod_time = new DateTime();
        $api_file_mod_time->setTimestamp(filectime($api_category_results));
				error_log("hw-feedback: Time since last modification of $api_category_results: ".time(). "/". $api_file_mod_time->format('Y-m-d H:i:s'));
			} else {
				// create file
				$api_file = fopen($api_category_results, "w") or die("hw-feedback: Unable to create file $api_category_results");
				// and close it
				fclose($api_file) && error_log("hw-feedback: $api_category_results closed post-creation");
			}
      // create download file if it doesn't exist
      if ( !file_exists($api_download)) {
        // create file
        $api_raw = fopen($api_download, "w") or die("hw-feedback: Unable to create file $api_download");
        // and close it
        fclose($api_raw) && error_log("hw-feedback: $api_download closed post-creation");
      }

			// check if the last modification was less than a day ago (24*60*60) AND we're not doing a forced refresh
			if ( isset($api_file_mod_time) && time() - $api_file_mod_time->getTimestamp() < 24*60*60 && ! $force_refresh) {
				// open file for read
				$api_file = fopen($api_category_results, "r") or die("hw-feedback: Unable to open file $api_category_results");
				// read file and convert to array
				$locations = array_values(json_decode(fread($api_file,filesize($api_category_results))));
				// close file again
				fclose($api_file) && error_log("hw-feedback: $api_category_results closed post-read");
        // check the wordpress timezone and display the mod time in that
				echo "<p>Locations read from <strong>file</strong>, last modified at ". ($api_file_mod_time->setTimezone(wp_timezone()))->format('Y-m-d H:i:s') . " (" .  wp_date('T') . ")</p>";
				error_log("hw-feedback: Locations read from $api_category_results");
				if (empty($locations)){ ?>
					<div id="hw-feedback-nothing-to-do" class="hw-feedback-alert" role="alert">Nothing to do! It looks like all services have been processed. Use "Force refresh?" above to make sure.</div>
				<?php }
			} else {
				// Query CQC API
				$api_response = json_decode(hw_feedback_cqc_api_query_locations(array(
					// get the local authority name from options
	        'localAuthority' => $options['hw_feedback_field_local_authority'],
	        'page' => '1',
	        'perPage' => '700',
	        'primaryInspectionCategoryCode' => $primary_inspection_category
	      )));
        // Add some API error checking
        if (isset($api_response->statusCode)) {
          echo '<p><strong>Status Code:</strong> ' . $api_response->statusCode . ' - ' . $api_response->message . '</p>';
          error_log('hw-feedback: API Status Code: ' . $api_response->statusCode);
          error_log('hw-feedback: API message: ' . $api_response->message);
          echo "</div> <!--hw-feedback-cqc-import-results -->";
          echo "</div> <!-- hwbucks-data-import-tool -->";
          return;
        } else {
          // save the API response to $api_download file
          $api_raw = fopen($api_download, "w") or die("hw-feedback: Unable to open file $api_download");
          // write the API response as JSON (I know we decode it and then encode it again...)
          fwrite($api_raw, json_encode($api_response));
          // close the file
          fclose($api_raw) && error_log("hw-feedback: $api_download closed post-write");
          // echo the query string (this no longer takes you to the results)
          echo '<p>API Query: https://api.service.cqc.org.uk/public/v1' . $api_response->firstPageUri . '</p>';
          // echo a link to the raw file
          echo '<p><a href="' . $uploads_folder['baseurl'] . '/' . $api_filename . '" target="_blank">View JSON results</a> | <a href="' . $uploads_folder['baseurl']  . '/' . $api_filename . '" download="' . $uploads_folder['baseurl']  . '/' . $api_filename . '" target="_blank">Download JSON results</a></p>';
          // Convert "JSON object" to array
          $locations = array_values($api_response->locations);
          // count number of locations
          $total_locations = count($locations);
          error_log("hw-feedback: " . $total_locations . " locations fetched from API");
        }
			}

      // set some counters
      $registered_counter = 0;
      $matched_count = 0;

      // Clean out anything not Registered
      foreach ($locations as $key => $current_location) {
        $current_location_id = $current_location->locationId;
        // get the location details
        $current_location_detail = json_decode(hw_feedback_cqc_api_query_by_id('locations',$current_location->locationId));
        if ($current_location_detail->registrationStatus != 'Registered') {
          //echo '<p>'. $current_location->locationName . ' (<a href="https://www.cqc.org.uk/location/' . $current_location->locationId . '" target="_blank">' . $current_location->locationId . '</a>)</p>';
          unset($locations[$key]);
          continue;
        }
        $registered_counter++;
      }
      // Reindex array - THIS IS CRITICAL!
      $locations = array_values($locations);

      // query all local_services posts regardless of status
      $args = array(
        'posts_per_page' => -1,
        'post_type' => 'local_services',
        'meta_key' => 'hw_services_cqc_location',
        'orderby' => 'meta_value',
				'post_status' => array('publish','private')
      );

      $services = get_posts( $args );
			$matched_locations = '';
      //echo '<h2>Matched in hw-feedback</h2>';
      // loop the posts
      foreach($services as $hw_feedback_service) : setup_postdata($hw_feedback_service);
        // get the CQC Location ID from post_meta
        $our_location_id = get_post_meta( $hw_feedback_service->ID, 'hw_services_cqc_location', true );
        // search for the location_id in $locations
        $result = array_search($our_location_id, array_column($locations, 'locationId'));
        // $result can return empty which PHP can read as [0] - so check it is not empty
        if ($result !== false){
					if ( $force_refresh === "true" ) {
	          $current_location_name = $locations[$result]->locationName;
	          $current_location_id = $locations[$result]->locationId;
	          //echo '<p>'. $current_location_name . ' (<a href="https://www.cqc.org.uk/location/' . $current_location_id . '" target="_blank">' . $current_location_id . '</a>)</p>';
						$matched_locations .= '<li>'. $current_location_name . ' (<a href="https://www.cqc.org.uk/location/' . $current_location_id . '" target="_blank">' . $current_location_id . '</a>)</li>';
					}
          // count the match
          $matched_count ++;
          // remove the service from $locations
          unset($locations[$result]);
        }
      endforeach;
			// Reindex array - THIS IS CRITICAL!
			$locations = array_values($locations);
			$unmatched_location_count = count($locations);

			// log some stuff
			error_log("hw-feedback: " . $registered_counter . " registered locations");
			error_log("hw-feedback: " . $unmatched_location_count . " unmatched locations");
			
			// be verbose
			if ( $force_refresh === "true" ) {
				echo '<h3>Found ' . $registered_counter . ' registered locations - ' . $unmatched_location_count . ' locations unmatched</h3>';
			}

			// now the locations are cleaned-up the locations to file as JSON object
			$api_file = fopen($api_category_results, "w") or die("hw-feedback: Unable to open file $api_category_results");
			fwrite($api_file,json_encode($locations));
			fclose($api_file) && error_log("hw-feedback: $api_category_results closed post-write");

			// limit the number of results to $import_number
			$locations = array_slice($locations,0,$import_number,true);
			if ( $preview_only ) {
				echo '<h4>Previewing ' . count($locations) . ' of ' . $unmatched_location_count. ' unmatched locations</h4>';
			} else {
				echo '<h4>' . count($locations) . ' locations successfully imported</h4>';
			}
			// start ordered list
			echo '<ol>';
      // loop the remaning $locations
      foreach ($locations as $location) {
        //
				$location_api_response = json_decode(hw_feedback_cqc_api_query_by_id('locations',$location->locationId));
				$cqc_gac_service_types = $location_api_response->gacServiceTypes[0]->description;
				// we know what the primary category code is because we chose it
				$service_types_term_name = (hw_feedback_inspection_category_to_service_type($primary_inspection_category) !== false) ? hw_feedback_inspection_category_to_service_type($primary_inspection_category) : hw_feedback_gac_category_to_service_type($cqc_gac_service_types);
				$service_types_term_id = get_term_by('name',$service_types_term_name,'service_types','ARRAY_A');
				// do something different if this is just a preview
				if ( $preview_only ) {
					echo '<li>'. $location->locationName . ' (' . $service_types_term_name . ' - <a href="https://www.cqc.org.uk/location/' . $location->locationId . '" target="_blank">' . $location->locationId . '</a>)</li>';
				} else {
					// build an array of these
					$cqc_inspection_category_terms = array();
					foreach ($location_api_response->inspectionCategories as $inspection_category) {
						array_push($cqc_inspection_category_terms,$inspection_category->code);
					}
					// get the registration date from API
					$date_stamp = strtotime($location_api_response->registrationDate);
					// I don't want it to be midnight so - yeah really - this is the easiest way to add eight hours
					$date_stamp = $date_stamp + (8*(60*60));
					// format as an SQL date
					$registration_date = date("Y-m-d H:i:s", $date_stamp);

					// generate excerpt
					$post_excerpt =  hw_feedback_generate_local_service_excerpt ($primary_inspection_category, $location_api_response);

					$post_arr = array(
					    'post_title'   => $location_api_response->name,
					    'post_content' => '',
							'post_excerpt' => $post_excerpt,
					    'post_status'  => 'publish',
					    'post_author'  => get_current_user_id(),
							'post_date' => $registration_date,
							'post_type' => 'local_services',
					    'tax_input'    => array(
					        'service_types'     => $service_types_term_id,
									// we removed everything that was not Registered
					        'cqc_reg_status' => 'Registered',
									'cqc_inspection_category' => $cqc_inspection_category_terms
					    ),
					    'meta_input'   => array(
								'hw_services_address_line_1' => isset($location_api_response->postalAddressLine1) == true ? $location_api_response->postalAddressLine1 : '',
								'hw_services_address_line_2' => isset($location_api_response->postalAddressLine2) == true ? $location_api_response->postalAddressLine2 : '',
								'hw_services_city' => isset($location_api_response->postalAddressTownCity) == true ? $location_api_response->postalAddressTownCity : '',
								'hw_services_county' => isset($location_api_response->postalAddressCounty) == true ? $location_api_response->postalAddressCounty : '',
								'hw_services_postcode' => isset($location_api_response->postalCode) == true ? $location_api_response->postalCode : '',
								'hw_services_cqc_location' => $location->locationId,
								'hw_services_phone' => isset($location_api_response->mainPhoneNumber) == true ? $location_api_response->mainPhoneNumber : '',
								'hw_services_website' => isset($location_api_response->website) == true ? $location_api_response->website : ''
					    ),
					);
					//echo '<p>'.print_r($post_arr).'</p>';
					$post_id = wp_insert_post( $post_arr );
					echo '<li>'.$location_api_response->name.' (<a href="'.get_edit_post_link($post_id).'">Edit</a> | <a href="'.get_post_permalink($post_id).'">View</a>)</li>';
				}
			// for loop ends here
			}
			// close ordered list
			echo '</ol>';
			if ( $force_refresh === "true" ) {
				echo '<h3>Matched ' . $matched_count . ' locations</h3><ol>';
				echo $matched_locations;
				echo '</ol>';
			}
			echo "<hr>";
			if ($force_refresh === "true") {
				// add some debug notes
				$deregistered_count = $total_locations - $registered_counter;
				echo "<p>API query returned " . $total_locations . " locations, " . $deregistered_count . " of which are deregistered.</p>";
				error_log("hw-feedback: " . $deregistered_count . " deregistered locations");
			}
      // Get finish time
      $executionEndTime = microtime(true);
      // The result will be in seconds and milliseconds.
      $seconds = round($executionEndTime - $executionStartTime,2);
      // Print it out
      echo "<p>This script took $seconds seconds to execute.</p>";
			echo "</div> <!--hw-feedback-cqc-import-results -->";
    }
		echo "</div> <!-- hwbucks-data-import-tool -->";
  }
?>
