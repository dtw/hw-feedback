<?php
/* 9. Add Management Page
-------------------------------------------------------- */



function hwbucks_cqc_data_import_tool() {
		add_management_page(
			'CQC Data Import',
			'CQC Data Import',
			'read_private_posts',
			'cqc-data-import',
			'hwbucks_cqc_data_import_contents',
		);
	}

	add_action( 'admin_menu', 'hwbucks_cqc_data_import_tool' );

	function hwbucks_cqc_data_import_contents() {
		// save the hw-feedback-form-inspection-category from POST
		$primary_inspection_category = isset($_POST['hw-feedback-form-inspection-category']) ? $_POST['hw-feedback-form-inspection-category'] : 'P1';
		$force_refresh = isset($_POST['hw-feedback-force-refresh']) ? $_POST['hw-feedback-force-refresh'] : false;
		$preview_only = isset($_POST['hw-feedback-preview-only']) ? $_POST['hw-feedback-preview-only'] : false;
		// default to 10
		$import_number = isset($_POST['hw-feedback-form-import-number']) ? $_POST['hw-feedback-form-import-number'] : 5;

		// establish the api cache in UPLOADS dir
		$upload_dir = wp_upload_dir();
		$api_cache = $upload_dir['basedir'];
		$api_cache .= "/api_cache/";
		// create new directory with 744 permissions if it does not exist yet
	  // owner will be the user/group the PHP script is run under
	  if ( !file_exists($api_cache) ) {
	      mkdir ($api_cache, 0744) or die("hw-feedback: Unable to create folder $api_cache");
	  }
		// build filename for inspection category
		$api_filename = $api_cache . 'cqc_api_locations_' . $primary_inspection_category . '.json';

    // create a simple form
    ?>
    <div id="hw-feedback-data-import-tool">
      <h1>CQC Data Import Tool</h1>
      <div id="hw-feedback-data-import-tool-instructions">
        <p>This tool adds Locations from the CQC API that are not currently listed as a Local Service in hw-feedback. Note that it ignores Locations that are not marked as "Registered".</p>
        <p>To reduce system overhead, by default, this tool is limited to adding 10 Locations at a time. This should be plenty if the tool is used regularly. To further reduce overheads, the tool will only check for new Locations for one Inspection Category at a time.</p>
				<p>You can choose that category using the drop-down list.</p>
      </div>
      <div id="hw-feedback-cqc-import-form" class="hw-feedback-cqc-form">
        <form action="tools.php?page=cqc-data-import" method="post">
					<div class="hw-feedback-cqc-import-form-row">
	          <label for="hw-feedback-form-inspection-category">Select Inspection Category</label>
	          <select class="hw-feedback-select widefat" name="hw-feedback-form-inspection-category" id="hw-feedback-form-inspection-category">
	          <?php foreach (get_terms('cqc_inspection_category', array('hide_empty' => false)) as $key => $term) {
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
	          <label for="hw-feedback-form-import-number">Select number of Locations to check/import</label>
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
						<div id="hw-feedback-import-alert" role="alert">New locations will be imported to the database!</div>
					</div>
          <div class="hw-feedback-cqc-import-form-row">
						<input type="submit" class="btn btn-primary hw-feedback-form-submit" id="hw-feedback-form-submit" value="Preview">
					</div>
        </form>
      </div>
    <?php

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			echo '<div id="hw-feedback-cqc-import-results">';
			echo "<h1>$primary_inspection_category Locations</h1>";
      // Get start time
      $executionStartTime = microtime(true);

      // Query CQC API
      $api_response = json_decode(hw_feedback_cqc_api_query_locations(array(
            'localAuthority' => 'Buckinghamshire',
            'page' => '1',
            'perPage' => '700',
            'primaryInspectionCategoryCode' => $primary_inspection_category,
            'partnerCode' => 'HW_BUCKS'
          )));

      // Convert "JSON object" to array
      $locations = array_values($api_response->locations);

      // get the total count of results
      $total = $api_response->total;
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
        'orderby' => 'meta_value'
      );

      $services = get_posts( $args );
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
	          echo '<p>'. $current_location_name . ' (<a href="https://www.cqc.org.uk/location/' . $current_location_id . '" target="_blank">' . $current_location_id . '</a>)</p>';
					}
          // count the match
          $matched_count ++;
          // remove the service from $locations
          unset($locations[$result]);
        }
      endforeach;
      // Reindex array - THIS IS CRITICAL!
      $locations = array_values($locations);


			// limit the number of results to $import_number
			$locations = array_slice($locations,0,$import_number,true);
			echo '<h3>Showing: ' . count($locations) . ' results</h3>';

      // loop the remaning $locations
      foreach ($locations as $location) {
        //
				$location_api_response = json_decode(hw_feedback_cqc_api_query_by_id('locations',$location->locationId));
				$cqc_gac_service_types = $location_api_response->gacServiceTypes[0]->description;
				$number_of_beds = $location_api_response->numberOfBeds;
				// we know what the primary category code is because we chose it
				$service_types_term_name = (hw_feedback_inspection_category_to_service_type($primary_inspection_category) !== false) ? hw_feedback_inspection_category_to_service_type($primary_inspection_category) : hw_feedback_gac_category_to_service_type($cqc_gac_service_types);
				$service_types_term_id = get_term_by('name',$service_types_term_name,'service_types','ARRAY_A');
				// do something different if this is just a preview
				if ( $preview_only ) {
					echo '<p>'. $location->locationName . ' (' . $service_types_term_name . ' - <a href="https://www.cqc.org.uk/location/' . $location->locationId . '" target="_blank">' . $location->locationId . '</a>)</p>';
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

					// build excerpt
					if ($primary_inspection_category == "P2") {
						$post_excerpt = "General practice";
					} else {
					$post_excerpt = $cqc_gac_service_types;
						if ($number_of_beds !== 0 && $number_of_beds !== false) {
							$post_excerpt .= ' - ' . $number_of_beds . ' beds';
						}
					}

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
								'hw_services_address_line_1' => $location_api_response->postalAddressLine1,
								'hw_services_address_line_2' => $location_api_response->postalAddressLine2,
								'hw_services_city' => $location_api_response->postalAddressTownCity,
								'hw_services_county' => $location_api_response->postalAddressCounty,
								'hw_services_postcode' => $location_api_response->postalCode,
					      'hw_services_cqc_location' => $location->locationId,
								'hw_services_phone' => $location_api_response->mainPhoneNumber,
								'hw_services_website' => $location_api_response->website,
					    ),
					);
					//echo '<p>'.print_r($post_arr).'</p>';
					$post_id = wp_insert_post( $post_arr );
					echo '<p>'.$location_api_response->name.' (<a href="'.get_edit_post_link($post_id).'">Edit</a> | <a href="'.get_post_permalink($post_id).'">View</a>)</p>';
				}
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
