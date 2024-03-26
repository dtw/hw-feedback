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

add_filter('comment_form_default_fields', 'hw_feedback_custom_fields');
function hw_feedback_custom_fields($fields)
{
  // Check whether the page template is single-local_services.php
  if (is_singular('local_services')) {
    $fields['author'] = '<p class="comment-form-section comment-form-author">' .
      '<label for="author">Your name</label><input placeholder="Your full name (optional)" id="author" name="author" autocomplete="off" type="text" size="30" tabindex="0" /></p>';

    $fields['email'] = '<p class="comment-form-section comment-form-email">' .
      '<label for="email">Email</label><input placeholder="Your email address (optional)" id="email" name="email" autocomplete="off" type="email" size="30" tabindex="0" /></p>';

    $fields['phone'] = '<p class="comment-form-section comment-form-phone">' .
      '<label for="phone">Phone</label>' .
      '<input placeholder="Your phone number (optional)" id="phone" name="phone" autocomplete="off" type="text" size="30"  tabindex="0" /></p><h2>Privacy</h2><p>Please review our <a href="' . get_site_url() . '/privacy/" target="_blank">privacy policy</a>. By completing this form, you agree that you have read and understood the privacy information provided, and confirm you are over 18.</p>';

    //		$fields[ 'address' ] = '<p class="comment-form-section comment-form-address">'.
    //			'<label for="address">Address</label>'.
    //			'<input placeholder="Your address" id="address" name="address" type="text" size="30"  tabindex="0" /></p>';
    return $fields;
  } // end of check for being a singular page
} // End of function

// Add fields after default fields above the comment box, always visible

add_action('comment_form_logged_in_before', 'hw_feedback_additional_fields');
add_action('comment_form_top', 'hw_feedback_additional_fields');

function hw_feedback_additional_fields()
{
  if (is_singular('local_services')) {
    echo '<p class="comment-form-section comment-form-rating">
			<label for="rating">How would you rate this service overall?</label></p>
			<p>Choose from 1 to 5 stars.</p>
			<select required="required" id="rating" name="rating" tabindex="0" size="1">
			<option value="">Choose</option>
			<option value="1" class="rate-1" id="rate-1">&#9733; Terrible</option>
			<option value="2" class="rate-2" id="rate-2">&#9733;&#9733; Poor</option>
			<option value="3" selected="selected" class="rate-3" id="rate-3">&#9733;&#9733;&#9733; Average</option>
			<option value="4" class="rate-4" id="rate-4">&#9733;&#9733;&#9733;&#9733; Good</option>
			<option value="5" class="rate-5" id="rate-5">&#9733;&#9733;&#9733;&#9733;&#9733; Excellent</option>
	</select>';
  } // end of check for being a singular page
}
// Save the comment meta data along with comment

add_action('comment_post', 'hw_feedback_save_comment_meta_data');
function hw_feedback_save_comment_meta_data($comment_id)
{
  if ((isset($_POST['phone'])) && ($_POST['phone'] != '')) {
    $phone = wp_filter_nohtml_kses($_POST['phone']);
    add_comment_meta($comment_id, 'feedback_phone', $phone);
  }
  // if ( ( isset( $_POST['address'] ) ) && ( $_POST['address'] != '') )
  // $address = wp_filter_nohtml_kses($_POST['address']);
  // add_comment_meta( $comment_id, 'feedback_address', $address );
  if ((isset($_POST['whenhappened'])) && ($_POST['whenhappened'] != '')) {
    $whenhappened = wp_filter_nohtml_kses($_POST['whenhappened']);
    add_comment_meta($comment_id, 'feedback_when', $whenhappened);
  }
  // if ( ( isset( $_POST['whoinvolved'] ) ) && ( $_POST['whoinvolved'] != '') )
  // $whoinvolved = wp_filter_nohtml_kses($_POST['whoinvolved']);
  // add_comment_meta( $comment_id, 'feedback_who', $whoinvolved );
  if ((isset($_POST['rating'])) && ($_POST['rating'] != '')) {
    $rating = wp_filter_nohtml_kses($_POST['rating']);
    add_comment_meta($comment_id, 'feedback_rating', $rating);
  }
  // apparently wp_generate_uuid4 used mt_rand(), which is seeded using the "PID + LCG" (https://www.php.net/manual/en/function.mt-rand.php) and that means collisions are "more likely"
  // I don't REALLY know how likely we're talking here since we're not doing 30k w/s
  // but I saw this recommended (https://developer.wordpress.org/reference/functions/wp_generate_uuid4/) and it can't hurt, can it?
  mt_srand(crc32(serialize(array(microtime(true), $comment_id))));
  // add a UUID for the comment
  add_comment_meta($comment_id, 'feedback_uuid', wp_generate_uuid4());
}

// Add a moderation section in the comment edit screen
add_action('add_meta_boxes_comment', 'hw_feedback_add_meta_box_moderation');
function hw_feedback_add_meta_box_moderation()
{
  add_meta_box('moderation', __('Moderation'), 'moderation_meta_box', 'comment', 'normal', 'high');
}

function moderation_meta_box($comment)
{
  //get hw-feedback options
  $options = get_option('hw_feedback_options');
  $withheld_comment_text = htmlspecialchars($options['hw_feedback_field_withheld_comment_text']);
  $partial_withheld_comment_text = htmlspecialchars($options['hw_feedback_field_partial_withheld_comment_text']);
?>
  <div id="moderation_toolbar">
    <button class="ed_button button button-small" type="button" onclick="hw_feedback_withhold_comment('<?php echo $withheld_comment_text ?>')">Withhold All</button>
    <button class="ed_button button button-small" type="button" onclick="hw_feedback_partial_comment('<?php echo $partial_withheld_comment_text ?>')">Withhold Partial</button>
    <button class="ed_button button button-small" type="button" onclick="hw_feedback_clear_personal_data()">Clear Personal Data</button>
    <button class="ed_button button button-small" type="button" onclick="hw_feedback_restore_comment()">Restore</button>
  </div>
<?php }
// Add an edit option in comment edit screen

add_action('add_meta_boxes_comment', 'hw_feedback_add_meta_box_extend_comment');
function hw_feedback_add_meta_box_extend_comment()
{
  add_meta_box('feedback_fields', __('Feedback fields'), 'extend_comment_meta_box', 'comment', 'normal', 'high');
}

function extend_comment_meta_box($comment)
{
  $phone = get_comment_meta($comment->comment_ID, 'feedback_phone', true);
  // $address = get_comment_meta( $comment->comment_ID, 'feedback_address', true );
  $rating = get_comment_meta($comment->comment_ID, 'feedback_rating', true);
  $uuid = get_comment_meta($comment->comment_ID, 'feedback_uuid', true);
  $when = get_comment_meta($comment->comment_ID, 'feedback_when', true);
  // $who = get_comment_meta( $comment->comment_ID, 'feedback_who', true );
  $response = get_comment_meta($comment->comment_ID, 'feedback_response', true);
  $hw_reply = get_comment_meta($comment->comment_ID, 'feedback_hw_reply', true);

  wp_nonce_field('extend_comment_update', 'extend_comment_update', false);
?>

  <p>CiviCRM Subject Code: <span id="civicrm-subject-code">#w<?php echo $uuid; ?></span><input type="hidden" value="#w<?php echo $uuid; ?>" id="civicrm-subject-code-field"><button class="ed_button button button-small" type="button" onclick="copy_civicrm_subject_code()">Copy</button></p>
  <p>
    <label for="phone">Phone</label>
    <input id="newcomment_author_phone" type="text" name="phone" autocomplete="off" value="<?php echo esc_attr($phone); ?>" class="widefat" />
  </p>

  <p>
    <label for="when">When did it happen?</label>
    <input type="text" name="when" autocomplete="off" value="<?php echo esc_attr($when); ?>" class="widefat" />
  </p>

  <p>
    <label for="rating"><?php _e('Rating: '); ?></label>
    <span class="commentratingbox">
      <?php for ($i = 0; $i <= 5; $i++) {
        echo '<span class="commentrating"><input type="radio" name="rating" id="rating" value="' . $i . '"';
        if ($rating == $i) echo ' checked="checked"';
        echo ' />' . $i . ' </span> ';
      }
      ?>
    </span>
  </p>
  <p>
    <label for="response">Response from Provider</label>
    <textarea name="response" class="widefat"><?php echo esc_html($response); ?></textarea>
  </p>

  <p>
    <label for="hw_reply">Response from Local Healthwatch</label>
    <textarea name="hw_reply" class="widefat"><?php echo esc_html($hw_reply); ?></textarea>
  </p>
<?php
}

// Update comment meta data from COMMENT EDIT SCREEN

add_action('edit_comment', 'hw_feedback_extend_comment_edit_metafields');
function hw_feedback_extend_comment_edit_metafields($comment_id)
{
  if (!isset($_POST['extend_comment_update']) || !wp_verify_nonce($_POST['extend_comment_update'], 'extend_comment_update')) return;

  if ((isset($_POST['phone'])) && ($_POST['phone'] != '')) :
    $phone = wp_filter_nohtml_kses($_POST['phone']);
    update_comment_meta($comment_id, 'feedback_phone', $phone);
  else :
    delete_comment_meta($comment_id, 'feedback_phone');
  endif;

  /* if ( ( isset( $_POST['address'] ) ) && ( $_POST['address'] != '') ) :
	$address = wp_filter_nohtml_kses($_POST['address']);
	update_comment_meta( $comment_id, 'feedback_address', $address );
	else :
	delete_comment_meta( $comment_id, 'feedback_address');
	endif; */

  if ((isset($_POST['rating'])) && ($_POST['rating'] != '')) :
    $rating = wp_filter_nohtml_kses($_POST['rating']);
    update_comment_meta($comment_id, 'feedback_rating', $rating);
  else :
    delete_comment_meta($comment_id, 'feedback_rating');
  endif;

  if ((isset($_POST['when'])) && ($_POST['when'] != '')) :
    $rating = wp_filter_nohtml_kses($_POST['when']);
    update_comment_meta($comment_id, 'feedback_when', $rating);
  else :
    delete_comment_meta($comment_id, 'feedback_when');
  endif;

  /* if ( ( isset( $_POST['who'] ) ) && ( $_POST['who'] != '') ):
	$rating = wp_filter_nohtml_kses($_POST['who']);
	update_comment_meta( $comment_id, 'feedback_who', $rating );
	else :
	delete_comment_meta( $comment_id, 'feedback_who');
	endif; */

  if ((isset($_POST['response'])) && ($_POST['response'] != '')) :
    $response = wp_filter_post_kses($_POST['response']);
    update_comment_meta($comment_id, 'feedback_response', $response);
  else :
    delete_comment_meta($comment_id, 'feedback_response');
  endif;

  if ((isset($_POST['hw_reply'])) && ($_POST['hw_reply'] != '')) :
    $hw_reply = wp_filter_post_kses($_POST['hw_reply']);
    update_comment_meta($comment_id, 'feedback_hw_reply', $hw_reply);
  else :
    delete_comment_meta($comment_id, 'feedback_hw_reply');
  endif;
}

// Add the comment meta (saved earlier) to the comment text
// You can also output the comment meta values directly in comments template

if (is_admin()) {

  add_filter('comment_text', 'hw_feedback_modify_comment');



  function hw_feedback_modify_comment($text)
  {


    $plugin_url_path = WP_PLUGIN_URL;

    if ($commentphone = get_comment_meta(get_comment_ID(), 'feedback_phone', true)) {
      $commentphone = '<strong>Phone: </strong>' . esc_attr($commentphone) . '<br/><br/>';
    }

    /* if( $commentaddress = get_comment_meta( get_comment_ID(), 'feedback_address', true ) ) {
		$commentaddress = '<strong>Address: </strong>' . esc_attr( $commentaddress ) . '<br/><br/>';
	} */

    if ($commentwhen = get_comment_meta(get_comment_ID(), 'feedback_when', true)) {
      $commentwhen = '<strong>When? </strong>' . esc_attr($commentwhen) . '<br/><br/>';
    }

    // if( $commentwho = get_comment_meta( get_comment_ID(), 'feedback_who', true ) ) { $commentwho = '<strong>Who was involved? </strong>' . esc_attr( $commentwho ) . '<br/><br/>'; }


    $text = $text . "<br /><br />" . $commentphone . $commentwhen;

    if ($commentrating = get_comment_meta(get_comment_ID(), 'feedback_rating', true)) {
      $commentratingtxt = '<p class="star-rating p-rating">' . hw_feedback_star_rating($commentrating, array()) . '</p><br/>Rating: <strong>' . $commentrating . ' / 5</strong></p>';
      $text = $text . $commentratingtxt;
      return $text;
    } else {
      return $text;
    }
  }
}

// Remove URL field
function hw_feedback_remove_comment_fields($fields)
{
  unset($fields['url']);
  return $fields;
}

// Move the COMMENT FIELD to the top

add_filter('comment_form_default_fields', 'hw_feedback_remove_comment_fields');

function hw_feedback_move_textarea($input = array())
{
  static $textarea = '';


  if ('comment_form_defaults' === current_filter()) {
    $textarea = '<p class="comment-form-section comment-form-comment"><label for="comment">Why have you given this rating?</label><p>Reviews are public and shared with the service. <strong>If you include names, dates or detailed health/case information the service may be able to identify the person receiving care.</strong></p><textarea tabindex="0" id="comment" name="comment" cols="45" rows="4" required="required"></textarea>';
    $input['comment_field'] = '';
    return $input;
  }
  if (is_singular('local_services')) {
    print $textarea;

    echo '<p class="comment-form-section comment-form-when">' .
      "<label for='whenhappened'>When did it happen?</label><p>If you don't know the exact date a year and month is helpful!</p>" .
      '<input required="required" id="whenhappened" name="whenhappened" type="text" size="30"  tabindex="0" /></p><hr />' .
      '<h2>Your contact details</h2><p>If you would like us to contact you about your feedback, please provide your details below. You can also <a href="https://www.healthwatchbucks.co.uk/how-we-work/contact-us/">contact us</a> directly.</p>';

    // echo '<p class="comment-form-section comment-form-how">'.
    // '<label for="whoinvolved">Who was involved?</label>'.
    // '<input required placeholder="e.g. Dr Smith" id="whoinvolved" name="whoinvolved" type="text" size="30"  tabindex="0" /></p><hr />';
  } else {
    echo '<p class="comment-form-section comment-form-comment"><label for="comment">Your comment</label><textarea placeholder="Type your comment here" tabindex="0" id="comment" name="comment" cols="45" rows="4" required="required"></textarea></p><hr /><h2>Your contact details</h2><p>If you would like us to contact you about your comment, please provide your details below. You can also <a href="https://www.healthwatchbucks.co.uk/how-we-work/contact-us/">contact us</a> directly.</p>';
    echo '<p class="comment-form-section comment-form-author"><label for="author">Your name</label><input placeholder="Your full name (optional)" id="author" name="author" autocomplete="off" type="text" size="30" tabindex="0" /></p>';
    echo '<label for="email">Email</label><input placeholder="Your email address (optional)" id="email" name="email" autocomplete="off" type="email" size="30" tabindex="0" /></p><h2>Privacy</h2><p>Please review our <a href="' . get_site_url() . '" target="_blank">data protection policy</a>. By completing this form, you agree that you have read and understood the privacy information provided, and confirm you are over 18.</p>';
  }
}

add_action('comment_form_defaults',  'hw_feedback_move_textarea');
add_action('comment_form_top',    'hw_feedback_move_textarea');
?>