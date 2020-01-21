<?php

/*

1. Create Local Services CUSTOM POST TYPE
2. Put Local Services on the AT A GLANCE section of the dashboard
3. Register CUSTOM TAXONOMIES
		- service types

4. Add ADDRESS & CONTACT DETAILS  META BOX to LOCAL SERVICES edit screen
5. Add ENTER & VIEW  META BOX to LOCAL SERVICES edit screen
6. Create ADMIN COLUMNS for local services

*/

/* 1. Create Local Services CUSTOM POST TYPE
-------------------------------------------------- */

add_action( 'init', 'hw_create_post_type' );
function hw_create_post_type() {
  register_post_type( 'local_services',
    array(

      'labels' => array(
        'name' => 'Local Services',
        'singular_name' => 'Local Service',
        'menu_name' => 'Local Services',
		'edit_item' => 'Edit Local Service',
		'view_item' => 'View Local Service',
		'search_items' => 'Search Local Services',
		'all_items' => 'All Local Services',
		'not_found' => 'No Local Services found',
		'add_new_item' => 'Add New Local Service'
	      ),
		'public' => true,
		'has_archive' => true,
		'exclude_from_search' => false,
		'menu_position' => 4,
		'show_in_nav_menus' => false,
		'menu_icon' => 'dashicons-star-half',
		'rewrite' => array('slug' => 'services'),
		'supports' => array('title','editor','excerpt','comments','thumbnail','custom-fields'),
		'can_export' => 'true',
		'taxonomies' => array('service_types'),

    )
  );
}




/* 2. Put Local Services on the AT A GLANCE section of the dashboard
--------------------------------------------------------------------------- */

add_action( 'dashboard_glance_items', 'cpad_at_glance_content_table_end' );
function cpad_at_glance_content_table_end() {
    $args = array(
        'public' => true,
        '_builtin' => false
    );
    $output = 'object';
    $operator = 'and';

    $post_types = get_post_types( $args, $output, $operator );
    foreach ( $post_types as $post_type ) {
        $num_posts = wp_count_posts( $post_type->name );
        $num = number_format_i18n( $num_posts->publish );
        $text = _n( $post_type->labels->singular_name, $post_type->labels->name, intval( $num_posts->publish ) );
        if ( current_user_can( 'edit_posts' ) ) {
            $output = '<a href="edit.php?post_type=' . $post_type->name . '">' . $num . ' ' . $text . '</a>';
            echo '<li class="post-count ' . $post_type->name . '-count">' . $output . '</li>';
        }
    }
}




/* 3. Register CUSTOM TAXONOMIES
(Note that must add taxonomy to custom post type too)
		- service types
------------------------------------------------------------------ */

function taxonomies_init() {
	// for SERVICE TYPES
	register_taxonomy(
		'service_types',
		'local_services',
		array(
			'label' => 'Service Types',
			'singular_name' => 'Service Type',
			'rewrite' => array( 'slug' => 'type' ),
			'edit_item' => 'Edit Service Type',
			'show_in_nav_menus' => true,
			'show_in_quick_edit' => true,
			'show_admin_column' => true,
			'hierarchical' => true,
			
		)
	);



}
add_action( 'init', 'taxonomies_init' );
































/* 4. Add ADDRESS AND CONTACT DETAILS META BOX to LOCAL SERVICES edit screen
--------------------------------------------------------------- */

function hw_add_meta_box() {

		add_meta_box(
			'hw_services_meta_box',		// Unique ID
			'Additional fields',		// Title
			'hw_meta_box_callback',		// Callback function
			'local_services',	// Which custom post type?
			'moved',	// Placement on editing page
			'high'		// Priority
		);
}
add_action( 'add_meta_boxes', 'hw_add_meta_box' );



// MOVES THE META BOX above the Post Editor
function hw_move_meta_box() {
        # Get the globals:
        global $post, $wp_meta_boxes;

        # Output the "advanced" meta boxes:
        do_meta_boxes( get_current_screen(), 'moved', $post );

        # Remove the initial "advanced" meta boxes:
        unset($wp_meta_boxes['post']['hw_services_meta_box']);
    }

add_action('edit_form_after_title', 'hw_move_meta_box');




/**
 * Prints the box content.
 * 
 * @param WP_Post $post The object for the current post/page.
 */
function hw_meta_box_callback( $post ) {

	// Add a NONCE field so we can check for it later.
	wp_nonce_field( 'hw_meta_box', 'hw_meta_box_nonce' );



	// Use get_post_meta() to retrieve existing values
	// from the database and use the value for the form


// ADDRESS FIELDS
echo "<br /><h2 class='hndle'><strong>Address</strong></h2><br /><br />";

	
	// ADDRESS LINE 1
	$value = get_post_meta( $post->ID, 'hw_services_address_line_1', true );
		echo '<label for="hw_services_address_line_1">Address line 1 </label>';
		echo '<input type="text" id="hw_services_address_line_1" name="hw_services_address_line_1" value="' . esc_attr( $value ) . '" size="60" />';

echo "<br /><br />";

	// ADDRESS LINE 2
	$value = get_post_meta( $post->ID, 'hw_services_address_line_2', true );
		echo '<label for="hw_services_address_line_2">Address line 2 </label>';
		echo '<input type="text" id="hw_services_address_line_2" name="hw_services_address_line_2" value="' . esc_attr( $value ) . '" size="60" />';

echo "<br /><br />";

	// CITY
	$value = get_post_meta( $post->ID, 'hw_services_city', true );
		echo '<label for="hw_services_city">City </label>';
		echo '<input type="text" id="hw_services_city" name="hw_services_city" value="' . esc_attr( $value ) . '" size="60" />';

echo "<br /><br />";

	// COUNTY
	$value = get_post_meta( $post->ID, 'hw_services_county', true );
		echo '<label for="hw_services_county">County </label>';
		echo '<input type="text" id="hw_services_county" name="hw_services_county" value="' . esc_attr( $value ) . '" size="60" />';

echo "<br /><br />";
	
	// POSTCODE
	$value = get_post_meta( $post->ID, 'hw_services_postcode', true );
		echo '<label for="hw_services_postcode">Postcode </label>';
		echo '<input type="text" id="hw_services_postcode" name="hw_services_postcode" value="' . esc_attr( $value ) . '" size="20" />';

echo "<br /><br />";


// CONTACT FIELDS
echo "<br /><h2 class='hndle'><strong>Contact details</strong></h2><br /><br />";

	// PHONE
	$value = get_post_meta( $post->ID, 'hw_services_phone', true );
		echo '<label for="hw_services_phone">Phone </label>';
		echo '<input type="text" id="hw_services_phone" name="hw_services_phone" value="' . esc_attr( $value ) . '" size="30" />';

echo "<br /><br />";

	// WEBSITE
	$value = get_post_meta( $post->ID, 'hw_services_website', true );
		echo '<label for="hw_services_website">Website </label>';
		echo '<input type="text" id="hw_services_website" placeholder="Begins www" name="hw_services_website" value="' . esc_attr( $value ) . '" size="50" />';


// CQC LOCATION CODE FIELD

echo "<br /><br /><br /><br /><h2 class='hndle'><strong>CQC Location code</strong></h2><br /><br />";


	// CQC LOCATION CODE
	$value = get_post_meta( $post->ID, 'hw_services_cqc_location', true );
		echo '<label for="hw_services_cqc_location">CQC Location </label>';
		echo '<input type="text" id="hw_services_cqc_location" name="hw_services_cqc_location" value="' . esc_attr( $value ) . '" size="30" />';




// RATE AND REVIEW FIELDS
	echo "<br /><br /><br /><br /><h2 class='hndle'><strong>How you rated this service</strong></h2><br /><br />";


	// OVERALL RATING
	$value = get_post_meta( $post->ID, 'hw_services_overall_rating', true );
		echo '<label for="hw_services_overall_rating">Overall rating </label>';
		echo 'No rating <input type="radio" name="hw_services_overall_rating" value="" '; if ($value == "") { echo "checked"; }; echo '> ';
		echo '1 <input type="radio" name="hw_services_overall_rating" value="1" '; if ($value == 1) { echo "checked"; }; echo '>  ';
		echo '2 <input type="radio" name="hw_services_overall_rating" value="2" '; if ($value == 2) { echo "checked"; }; echo '>  ';
		echo '3 <input type="radio" name="hw_services_overall_rating" value="3" '; if ($value == 3) { echo "checked"; }; echo '>  ';
		echo '4 <input type="radio" name="hw_services_overall_rating" value="4" '; if ($value == 4) { echo "checked"; }; echo '>  ';
		echo '5 <input type="radio" name="hw_services_overall_rating" value="5" '; if ($value == 5) { echo "checked"; }; echo '>  ';

echo "<br /><br />";

	// HOW PEOPLE ARE TREATED
	$value = get_post_meta( $post->ID, 'hw_services_how_people_treated', true );
		echo '<label for="hw_services_how_people_treated">How people are treated </label>';
		echo 'No rating <input type="radio" name="hw_services_how_people_treated" value="" '; if ($value == "") { echo "checked"; }; echo '> ';
		echo '1 <input type="radio" name="hw_services_how_people_treated" value="1" '; if ($value == 1) { echo "checked"; }; echo '> ';
		echo '2 <input type="radio" name="hw_services_how_people_treated" value="2" '; if ($value == 2) { echo "checked"; }; echo '> ';
		echo '3 <input type="radio" name="hw_services_how_people_treated" value="3" '; if ($value == 3) { echo "checked"; }; echo '> ';
		echo '4 <input type="radio" name="hw_services_how_people_treated" value="4" '; if ($value == 4) { echo "checked"; }; echo '> ';
		echo '5 <input type="radio" name="hw_services_how_people_treated" value="5" '; if ($value == 5) { echo "checked"; }; echo '> ';


echo "<br /><br />";

	// PERSONAL CHOICE
	$value = get_post_meta( $post->ID, 'hw_services_personal_choice', true );
		echo '<label for="hw_services_personal_choice">Personal choice </label>';
		echo 'No rating <input type="radio" name="hw_services_personal_choice" value="" '; if ($value == "") { echo "checked"; }; echo '> ';
		echo '1 <input type="radio" name="hw_services_personal_choice" value="1" '; if ($value == 1) { echo "checked"; }; echo '> ';
		echo '2 <input type="radio" name="hw_services_personal_choice" value="2" '; if ($value == 2) { echo "checked"; }; echo '> ';
		echo '3 <input type="radio" name="hw_services_personal_choice" value="3" '; if ($value == 3) { echo "checked"; }; echo '> ';
		echo '4 <input type="radio" name="hw_services_personal_choice" value="4" '; if ($value == 4) { echo "checked"; }; echo '> ';
		echo '5 <input type="radio" name="hw_services_personal_choice" value="5" '; if ($value == 5) { echo "checked"; }; echo '> ';

echo "<br /><br />";

	// JUST LIKE BEING AT HOME
	$value = get_post_meta( $post->ID, 'hw_services_being_home', true );
		echo '<label for="hw_services_being_home">Just like being at home </label>';
		echo 'No rating <input type="radio" name="hw_services_being_home" value="" '; if ($value == "") { echo "checked"; }; echo '> ';
		echo '1 <input type="radio" name="hw_services_being_home" value="1" '; if ($value == 1) { echo "checked"; }; echo '> ';
		echo '2 <input type="radio" name="hw_services_being_home" value="2" '; if ($value == 2) { echo "checked"; }; echo '> ';
		echo '3 <input type="radio" name="hw_services_being_home" value="3" '; if ($value == 3) { echo "checked"; }; echo '> ';
		echo '4 <input type="radio" name="hw_services_being_home" value="4" '; if ($value == 4) { echo "checked"; }; echo '> ';
		echo '5 <input type="radio" name="hw_services_being_home" value="5" '; if ($value == 5) { echo "checked"; }; echo '> ';

echo "<br /><br />";
	
	// PRIVACY
	$value = get_post_meta( $post->ID, 'hw_services_privacy', true );
		echo '<label for="hw_services_privacy">Privacy </label>';
		echo 'No rating <input type="radio" name="hw_services_privacy" value="" '; if ($value == "") { echo "checked"; }; echo '> ';
		echo '1 <input type="radio" name="hw_services_privacy" value="1" '; if ($value == 1) { echo "checked"; }; echo '> ';
		echo '2 <input type="radio" name="hw_services_privacy" value="2" '; if ($value == 2) { echo "checked"; }; echo '> ';
		echo '3 <input type="radio" name="hw_services_privacy" value="3" '; if ($value == 3) { echo "checked"; }; echo '> ';
		echo '4 <input type="radio" name="hw_services_privacy" value="4" '; if ($value == 4) { echo "checked"; }; echo '> ';
		echo '5 <input type="radio" name="hw_services_privacy" value="5" '; if ($value == 5) { echo "checked"; }; echo '> ';

echo "<br /><br />";

	// QUALITY OF LIFE
	$value = get_post_meta( $post->ID, 'hw_services_quality_life', true );
		echo '<label for="hw_services_quality_life">Quality of life </label>';
		echo 'No rating <input type="radio" name="hw_services_quality_life" value="" '; if ($value == "") { echo "checked"; }; echo '> ';
		echo '1 <input type="radio" name="hw_services_quality_life" value="1" '; if ($value == 1) { echo "checked"; }; echo '> ';
		echo '2 <input type="radio" name="hw_services_quality_life" value="2" '; if ($value == 2) { echo "checked"; }; echo '> ';
		echo '3 <input type="radio" name="hw_services_quality_life" value="3" '; if ($value == 3) { echo "checked"; }; echo '> ';
		echo '4 <input type="radio" name="hw_services_quality_life" value="4" '; if ($value == 4) { echo "checked"; }; echo '> ';
		echo '5 <input type="radio" name="hw_services_quality_life" value="5" '; if ($value == 5) { echo "checked"; }; echo '> ';

echo "<br /><br />";

	// DATE OF VISIT
	$value = get_post_meta( $post->ID, 'hw_services_date_visited', true );
		echo '<label for="hw_services_date_visited">Date of visit </label>';
		echo '<input placeholder="2nd May 2016" type="text" id="hw_services_date_visited" name="hw_services_date_visited" value="' . esc_attr( $value ) . '" size="30" />';

echo "<br /><br />";

	// LINK TO FULL REPORT
	$value = get_post_meta( $post->ID, 'hw_services_full_report', true );
		echo '<label for="hw_services_full_report">Link to full report </label>';
		echo '<input placeholder="Begins http://" type="url" id="hw_services_full_report" name="hw_services_full_report" value="' . esc_attr( $value ) . '" size="90" />';

echo "<br /><br />";

}



/**
 * When the post is saved, saves our custom data.
 *
 * @param int $post_id The ID of the post being saved.
 */
function hw_save_meta_box_data( $post_id ) {

	/*
	 * We need to verify this came from our screen and with proper authorization,
	 * because the save_post action can be triggered at other times.
	 */

	// Check if our NONCE is set.
	if ( ! isset( $_POST['hw_meta_box_nonce'] ) ) {
		return;
	}

	// Verify that the NONCE is valid.
	if ( ! wp_verify_nonce( $_POST['hw_meta_box_nonce'], 'hw_meta_box' ) ) {
		return;
	}

	// IF THIS IS AN AUTOSAVE,
	// our form has not been submitted, so we don't want to do anything.
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	// Check the user's PERMISSIONS.
	if ( isset( $_POST['post_type'] ) && 'page' == $_POST['post_type'] ) {

		if ( ! current_user_can( 'edit_page', $post_id ) ) {
			return;
		}

	} else {

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
	}





/* 5. SAVE THE DATA when page updated
--------------------------------------------------------- */


	/* OK, it's safe for us to save the data now. */
		// For each custom field:
		// Make sure that FIELD is SET.
		// SANITIZE user input.
		// UPDATE the meta field in the database.
		
		// ADDRESS LINE 1
		if ( ! isset( $_POST['hw_services_address_line_1'] ) ) { return; }
		$my_data = sanitize_text_field( $_POST['hw_services_address_line_1'] );
		update_post_meta( $post_id, 'hw_services_address_line_1', $my_data );

		// ADDRESS LINE 2	
		if ( ! isset( $_POST['hw_services_address_line_2'] ) ) { return; }
		$my_data = sanitize_text_field( $_POST['hw_services_address_line_2'] );
		update_post_meta( $post_id, 'hw_services_address_line_2', $my_data );
		
		// CITY
		if ( ! isset( $_POST['hw_services_city'] ) ) { return; }
		$my_data = sanitize_text_field( $_POST['hw_services_city'] );
		update_post_meta( $post_id, 'hw_services_city', $my_data );

		// COUNTY
		if ( ! isset( $_POST['hw_services_county'] ) ) { return; }
		$my_data = sanitize_text_field( $_POST['hw_services_county'] );
		update_post_meta( $post_id, 'hw_services_county', $my_data );
		
		// POSTCODE
		if ( ! isset( $_POST['hw_services_postcode'] ) ) { return; }
		$my_data = sanitize_text_field( $_POST['hw_services_postcode'] );
		update_post_meta( $post_id, 'hw_services_postcode', $my_data );
	
		// PHONE
		if ( ! isset( $_POST['hw_services_phone'] ) ) { return; }
		$my_data = sanitize_text_field( $_POST['hw_services_phone'] );
		update_post_meta( $post_id, 'hw_services_phone', $my_data );

		// WEBSITE
		if ( ! isset( $_POST['hw_services_website'] ) ) { return; }
		$my_data = sanitize_text_field( $_POST['hw_services_website'] );
		update_post_meta( $post_id, 'hw_services_website', $my_data );

		// CQC LOCATION CODE
		if ( ! isset( $_POST['hw_services_cqc_location'] ) ) { return; }
		$my_data = sanitize_text_field( $_POST['hw_services_cqc_location'] );
		update_post_meta( $post_id, 'hw_services_cqc_location', $my_data );

		// OVERALL RATING
		if ( ! isset( $_POST['hw_services_overall_rating'] ) ) { return; }
		$my_data = sanitize_text_field( $_POST['hw_services_overall_rating'] );
		update_post_meta( $post_id, 'hw_services_overall_rating', $my_data );

		// HOW PEOPLE ARE TREATED
		if ( ! isset( $_POST['hw_services_how_people_treated'] ) ) { return; }
		$my_data = sanitize_text_field( $_POST['hw_services_how_people_treated'] );
		update_post_meta( $post_id, 'hw_services_how_people_treated', $my_data );

		// PERSONAL CHOICE
		if ( ! isset( $_POST['hw_services_personal_choice'] ) ) { return; }
		$my_data = sanitize_text_field( $_POST['hw_services_personal_choice'] );
		update_post_meta( $post_id, 'hw_services_personal_choice', $my_data );

		// JUST LIKE BEING AT HOME
		if ( ! isset( $_POST['hw_services_being_home'] ) ) { return; }
		$my_data = sanitize_text_field( $_POST['hw_services_being_home'] );
		update_post_meta( $post_id, 'hw_services_being_home', $my_data );

		// PRIVACY
		if ( ! isset( $_POST['hw_services_privacy'] ) ) { return; }
		$my_data = sanitize_text_field( $_POST['hw_services_privacy'] );
		update_post_meta( $post_id, 'hw_services_privacy', $my_data );

		// QUALITY OF LIFE
		if ( ! isset( $_POST['hw_services_quality_life'] ) ) { return; }
		$my_data = sanitize_text_field( $_POST['hw_services_quality_life'] );
		update_post_meta( $post_id, 'hw_services_quality_life', $my_data );

		// URL OF FULL REPORT
		if ( ! isset( $_POST['hw_services_full_report'] ) ) { return; }
		$my_data = esc_url_raw( $_POST['hw_services_full_report'] );
		update_post_meta( $post_id, 'hw_services_full_report', $my_data );

		// DATE OF VISIT
		if ( ! isset( $_POST['hw_services_date_visited'] ) ) { return; }
		$my_data = sanitize_text_field( $_POST['hw_services_date_visited'] );
		update_post_meta( $post_id, 'hw_services_date_visited', $my_data );

}

add_action( 'save_post', 'hw_save_meta_box_data' );
























/* 6. Create ADMIN COLUMNS for local services
--------------------------------------------------------- */

add_filter('manage_local_services_posts_columns', 'bs_local_services_table_head');
function bs_local_services_table_head( $columns ) {

    $columns['rated']  = 'Rated';
    $columns['contact']  = 'Contact';
    $columns['website']  = 'Website';
    $columns['cqc_location']  = 'CQC location';
    return $columns;

}
add_action( 'manage_local_services_posts_custom_column', 'bs_local_services_table_content', 10, 2 );

// Fill data into ADMIN COLUMNS for local services
function bs_local_services_table_content( $column_name, $post_id ) {


    if( $column_name == 'contact' ) {
        
	
		$address_city = get_post_meta( $post_id, 'hw_services_city', true );
		$address_county = get_post_meta( $post_id, 'hw_services_county', true );
	
		if ($address_city) { echo $address_city; } 				
		if ($address_county) { echo ",<br />" . $address_county; } 				
	
		echo $col_address;

		echo "<br /><span class='dashicons dashicons-phone' style='font-size: 1rem; width: auto; '></span> <strong>";
        $col_phone = get_post_meta( $post_id, 'hw_services_phone', true );
        echo $col_phone; echo "</strong>";

    }



    if( $column_name == 'website' ) {
        $col_website = get_post_meta( $post_id, 'hw_services_website', true );
        echo "<a target='_blank' href='http://". $col_website ."'>";
		echo $col_website;
		echo "</a>";
    }

    if( $column_name == 'cqc_location' ) {
        $col_cqc_location = get_post_meta( $post_id, 'hw_services_cqc_location', true );
        echo $col_cqc_location;
		echo "<br />";

    }



    if( $column_name == 'rated' ) {
		$col_rating = get_post_meta( $post_id, 'hw_services_overall_rating', true );
    if($col_rating > 0){echo '<p>'.feedbackstarrating($col_rating,array('colour' => 'green')).'</p>';}

	}




}




















// Remove date filter from dashboard
add_filter('months_dropdown_results', '__return_empty_array' );








/**
 * Display a custom taxonomy dropdown in admin
 * Thank you to:
 * @author Mike Hemberger
 * @link http://thestizmedia.com/custom-post-type-filter-admin-custom-taxonomy/
 */
add_action('restrict_manage_posts', 'hw_filter_post_type_by_taxonomy');
function hw_filter_post_type_by_taxonomy() {
	global $typenow;
	$post_type = 'local_services'; // change to your post type
	$taxonomy  = 'service_types'; // change to your taxonomy
	if ($typenow == $post_type) {
		$selected      = isset($_GET[$taxonomy]) ? $_GET[$taxonomy] : '';
		$info_taxonomy = get_taxonomy($taxonomy);
		wp_dropdown_categories(array(
			'show_option_all' => __("Show All {$info_taxonomy->label}"),
			'taxonomy'        => $taxonomy,
			'name'            => $taxonomy,
			'orderby'         => 'name',
			'selected'        => $selected,
			'show_count'      => true,
			'hide_empty'      => true,
		));
	};
}
/**
 * Filter posts by taxonomy in admin
 */
add_filter('parse_query', 'hw_convert_id_to_term_in_query');
function hw_convert_id_to_term_in_query($query) {
	global $pagenow;
	$post_type = 'local_services'; // change to your post type
	$taxonomy  = 'service_types'; // change to your taxonomy
	$q_vars    = &$query->query_vars;
	if ( $pagenow == 'edit.php' && isset($q_vars['post_type']) && $q_vars['post_type'] == $post_type && isset($q_vars[$taxonomy]) && is_numeric($q_vars[$taxonomy]) && $q_vars[$taxonomy] != 0 ) {
		$term = get_term_by('id', $q_vars[$taxonomy], $taxonomy);
		$q_vars[$taxonomy] = $term->slug;
	}
}


















?>