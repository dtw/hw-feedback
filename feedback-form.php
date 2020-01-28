<?php

/*
	- Default comment form elements are hidden when user is logged in
	- Check whether the page template is single-local_services.php
	- Add fields after default fields above the comment box, always visible
	- Save the comment meta data along with comment
	- Add an edit option in comment edit screen
	- Update comment meta data from COMMENT EDIT SCREEN
	- Add the comment meta (saved earlier) to the comment text
	- Remove URL field
	- Move the COMMENT FIELD to the top
*/



// Default comment form elements are hidden when user is logged in

add_filter('comment_form_default_fields','custom_fields');
function custom_fields($fields) {

// Check whether the page template is single-local_services.php

if ( is_singular( 'local_services' ) ) {

		$fields[ 'author' ] = '<p class="comment-form-author">'.
			'<label for="author">Your name</label><input placeholder="Your first and last names (optional)" id="author" name="author" autocomplete="off" type="text" size="30" tabindex="6" /></p>';

		$fields[ 'email' ] = '<p class="comment-form-email">'.
			'<label for="email">Email</label><input placeholder="Your email address (optional)" id="email" name="email" autocomplete="off" type="email" size="30" tabindex="7" /></p>';

		$fields[ 'phone' ] = '<p class="comment-form-phone">'.
			'<label for="phone">Phone</label>'.
			'<input placeholder="Your phone number (optional)" id="phone" name="phone" autocomplete="off" type="text" size="30"  tabindex="8" /></p><h2>Privacy</h2><p>Please review our <a href="https://www.healthwatchbucks.co.uk/privacy/" target="_blank">privacy policy</a>. By completing this form, you agree that you have read and understood the privacy information provided, and confirm you are over 18.</p>';

//		$fields[ 'address' ] = '<p class="comment-form-address">'.
//			'<label for="address">Address</label>'.
//			'<input placeholder="Your address" id="address" name="address" type="text" size="30"  tabindex="9" /></p>';




	return $fields;



			} // end of check for being a singular page


				} // End of function



// Add fields after default fields above the comment box, always visible


add_action( 'comment_form_logged_in_before', 'additional_fields' );
add_action( 'comment_form_top', 'additional_fields' );

function additional_fields () {

if ( is_singular( 'local_services' ) ) {

	echo '<p class="comment-form-rating">

			<label for="rating">How would you rate this service overall? Choose from 1 to 5 stars.</label>
			<select required id="rating" name="rating" tabindex="2">
			<option value="1" class="rate-1" id="rate-1">&#9733; Terrible</option>
			<option value="2" class="rate-2" id="rate-2">&#9733;&#9733; Poor</option>
			<option value="3" selected="selected" class="rate-3" id="rate-3">&#9733;&#9733;&#9733; Average</option>
			<option value="4" class="rate-4" id="rate-4">&#9733;&#9733;&#9733;&#9733; Good</option>
			<option value="5" class="rate-5" id="rate-5">&#9733;&#9733;&#9733;&#9733;&#9733; Excellent</option>
	</select>
	</p>';



} // end of check for being a singular page


}






// Save the comment meta data along with comment

add_action( 'comment_post', 'save_comment_meta_data' );
function save_comment_meta_data( $comment_id ) {

	if ( ( isset( $_POST['phone'] ) ) && ( $_POST['phone'] != '') )
	$phone = wp_filter_nohtml_kses($_POST['phone']);
	add_comment_meta( $comment_id, 'feedback_phone', $phone );

	if ( ( isset( $_POST['address'] ) ) && ( $_POST['address'] != '') )
	$address = wp_filter_nohtml_kses($_POST['address']);
	add_comment_meta( $comment_id, 'feedback_address', $address );

	if ( ( isset( $_POST['whenhappened'] ) ) && ( $_POST['whenhappened'] != '') )
	$whenhappened = wp_filter_nohtml_kses($_POST['whenhappened']);
	add_comment_meta( $comment_id, 'feedback_when', $whenhappened );

	// if ( ( isset( $_POST['whoinvolved'] ) ) && ( $_POST['whoinvolved'] != '') )
	// $whoinvolved = wp_filter_nohtml_kses($_POST['whoinvolved']);
	// add_comment_meta( $comment_id, 'feedback_who', $whoinvolved );

	if ( ( isset( $_POST['rating'] ) ) && ( $_POST['rating'] != '') )
	$rating = wp_filter_nohtml_kses($_POST['rating']);
	add_comment_meta( $comment_id, 'feedback_rating', $rating );

}


// Add an edit option in comment edit screen

add_action( 'add_meta_boxes_comment', 'extend_comment_add_meta_box' );
function extend_comment_add_meta_box() {
    add_meta_box( 'title', __( 'Feedback fields' ), 'extend_comment_meta_box', 'comment', 'normal', 'high' );
}

function extend_comment_meta_box ( $comment ) {
    $phone = get_comment_meta( $comment->comment_ID, 'feedback_phone', true );
    $address = get_comment_meta( $comment->comment_ID, 'feedback_address', true );
    $rating = get_comment_meta( $comment->comment_ID, 'feedback_rating', true );
    $when = get_comment_meta( $comment->comment_ID, 'feedback_when', true );
    $who = get_comment_meta( $comment->comment_ID, 'feedback_who', true );
    $response = get_comment_meta( $comment->comment_ID, 'feedback_response', true );


    wp_nonce_field( 'extend_comment_update', 'extend_comment_update', false );
    ?>


    <p>
        <label for="phone">Phone</label>
        <input type="text" name="phone" autocomplete="off" value="<?php echo esc_attr( $phone ); ?>" class="widefat" />
    </p>

    <p>
        <label for="address">Address</label>
        <input type="text" name="address" autocomplete="off" value="<?php echo esc_attr( $address ); ?>" class="widefat" />
    </p>

    <p>
        <label for="when">When did it happen?</label>
        <input type="text" name="when" autocomplete="off" value="<?php echo esc_attr( $when ); ?>" class="widefat" />
    </p>

    <!--<p>
        <label for="who">Who was involved?</label>
        <input type="text" name="who" value="<?php echo esc_attr( $who ); ?>" class="widefat" />
    </p>-->

    <p>
        <label for="rating"><?php _e( 'Rating: ' ); ?></label>
			<span class="commentratingbox">
			<?php for( $i=1; $i <= 5; $i++ ) {
				echo '<span class="commentrating"><input type="radio" name="rating" id="rating" value="'. $i .'"';
				if ( $rating == $i ) echo ' checked="checked"';
				echo ' />'. $i .' </span> ';
				}
			?>
			</span>
    </p>



    <p>
        <label for="response">Response from the Service</label>
        <textarea name="response" class="widefat"><?php echo esc_attr( $response ); ?></textarea>
    </p>





    <?php
}









// Update comment meta data from COMMENT EDIT SCREEN

add_action( 'edit_comment', 'extend_comment_edit_metafields' );
function extend_comment_edit_metafields( $comment_id ) {
    if( ! isset( $_POST['extend_comment_update'] ) || ! wp_verify_nonce( $_POST['extend_comment_update'], 'extend_comment_update' ) ) return;

	if ( ( isset( $_POST['phone'] ) ) && ( $_POST['phone'] != '') ) :
	$phone = wp_filter_nohtml_kses($_POST['phone']);
	update_comment_meta( $comment_id, 'feedback_phone', $phone );
	else :
	delete_comment_meta( $comment_id, 'feedback_phone');
	endif;

	if ( ( isset( $_POST['address'] ) ) && ( $_POST['address'] != '') ) :
	$address = wp_filter_nohtml_kses($_POST['address']);
	update_comment_meta( $comment_id, 'feedback_address', $address );
	else :
	delete_comment_meta( $comment_id, 'feedback_address');
	endif;

	if ( ( isset( $_POST['rating'] ) ) && ( $_POST['rating'] != '') ):
	$rating = wp_filter_nohtml_kses($_POST['rating']);
	update_comment_meta( $comment_id, 'feedback_rating', $rating );
	else :
	delete_comment_meta( $comment_id, 'feedback_rating');
	endif;

	if ( ( isset( $_POST['when'] ) ) && ( $_POST['when'] != '') ):
	$rating = wp_filter_nohtml_kses($_POST['when']);
	update_comment_meta( $comment_id, 'feedback_when', $rating );
	else :
	delete_comment_meta( $comment_id, 'feedback_when');
	endif;

	if ( ( isset( $_POST['who'] ) ) && ( $_POST['who'] != '') ):
	$rating = wp_filter_nohtml_kses($_POST['who']);
	update_comment_meta( $comment_id, 'feedback_who', $rating );
	else :
	delete_comment_meta( $comment_id, 'feedback_who');
	endif;

	if ( ( isset( $_POST['response'] ) ) && ( $_POST['response'] != '') ):
	$response = wp_filter_nohtml_kses($_POST['response']);
	update_comment_meta( $comment_id, 'feedback_response', $response );
	else :
	delete_comment_meta( $comment_id, 'feedback_response');
	endif;

}

// Add the comment meta (saved earlier) to the comment text
// You can also output the comment meta values directly in comments template

	if ( is_admin() ) {

add_filter( 'comment_text', 'modify_comment');



function modify_comment( $text ){


	$plugin_url_path = WP_PLUGIN_URL;

	if( $commentphone = get_comment_meta( get_comment_ID(), 'feedback_phone', true ) ) {
		$commentphone = '<strong>Phone: </strong>' . esc_attr( $commentphone ) . '<br/><br/>';
	}

	if( $commentaddress = get_comment_meta( get_comment_ID(), 'feedback_address', true ) ) {
		$commentaddress = '<strong>Address: </strong>' . esc_attr( $commentaddress ) . '<br/><br/>';
	}

	if( $commentwhen = get_comment_meta( get_comment_ID(), 'feedback_when', true ) ) {
		$commentwhen = '<strong>When? </strong>' . esc_attr( $commentwhen ) . '<br/><br/>';

	}

// if( $commentwho = get_comment_meta( get_comment_ID(), 'feedback_who', true ) ) { $commentwho = '<strong>Who was involved? </strong>' . esc_attr( $commentwho ) . '<br/><br/>'; }


		$text = $text . "<br /><br />" . $commentphone . $commentaddress . $commentwhen . $commentwho;

	if( $commentrating = get_comment_meta( get_comment_ID(), 'feedback_rating', true ) ) {
		$commentratingtxt = '<p class="star-rating p-rating">' . feedbackstarrating($commentrating, array()) . '</p><br/>Rating: <strong>'. $commentrating .' / 5</strong></p>';
		$text = $text . $commentratingtxt;
		return $text;
	} else {
		return $text;
	}
}

}




// Remove URL field
function remove_comment_fields($fields) {
    unset($fields['url']);
    return $fields;
}






// Move the COMMENT FIELD to the top


add_filter('comment_form_default_fields', 'remove_comment_fields');

function hw_move_textarea( $input = array () ) {
    static $textarea = '';


    if ( 'comment_form_defaults' === current_filter() ) {
        $textarea = '<p class="comment-form-comment"><label for="comment">What happened?</label><p>Please tell us what happened and make suggestions for improvements. Please do not include any personal information like names, dates or detailed health information.</p><textarea tabindex="3" id="comment" name="comment" cols="45" rows="4" required="required"></textarea>';
        $input['comment_field'] = '';
        return $input;
    }
    if ( is_singular( 'local_services' ) ) {
        print $textarea;

		echo '<p class="comment-form-when">'.
			'<label for="whenhappened">When did it happen?</label>'.
			'<input required id="whenhappened" name="whenhappened" type="text" size="30"  tabindex="4" /></p><hr /><h2>Your contact details</h2><p>If you would like us to contact you about your feedback, please provide your details below. You can also <a href="https://www.healthwatchbucks.co.uk/how-we-work/contact-us/">contact us</a> directly.</p>';

		// echo '<p class="comment-form-how">'.
			// '<label for="whoinvolved">Who was involved?</label>'.
			// '<input required placeholder="e.g. Dr Smith" id="whoinvolved" name="whoinvolved" type="text" size="30"  tabindex="5" /></p><hr />';



    }


	else {
        echo '<p class="comment-form-comment"><label for="comment">Your comment</label><textarea placeholder="Type your comment here" tabindex="1" id="comment" name="comment" cols="45" rows="4" required="required"></textarea></p><hr /><h2>Your contact details</h2><p>If you would like us to contact you about your comment, please provide your details below. You can also <a href="https://www.healthwatchbucks.co.uk/how-we-work/contact-us/">contact us</a> directly.</p>';
		echo '<p class="comment-form-author"><label for="author">Your name</label><input placeholder="Your first and last names (optional)" id="author" name="author" autocomplete="off" type="text" size="30" tabindex="2" /></p>';
		echo '<label for="email">Email</label><input placeholder="Your email address (optional)" id="email" name="email" autocomplete="off" type="email" size="30" tabindex="3" /></p><h2>Privacy</h2><p>Please review our <a href="https://www.healthwatchbucks.co.uk/privacy/" target="_blank">data protection policy</a>. By completing this form, you agree that you have read and understood the privacy information provided, and confirm you are over 18.</p>';
		}

}

add_action( 'comment_form_defaults',	'hw_move_textarea' );
add_action( 'comment_form_top',		'hw_move_textarea' );
?>
