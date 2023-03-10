<?php
/**
 * Output feedback star rating
 *
 * @package   hw-feedback
 * @author    Phil Thiselton <dibblethewrecker@gmail.com>
 * @license   GPL-2.0+
 * @copyright 2019 Phil Thiselton
 *
 * Description: Takes a post and returns the ratings count and ratings total
 * @param array $post
 */

  function hw_feedback_get_rating($post) {
    // Set TOTAL and COUNT to zero
    $rating['total'] = 0;
    $rating['count'] = 0;
    $rating['average'] = 0;
    $rating['recent_total'] = 0;
    $rating['recent_count'] = 0;
    $rating['recent_average'] = 0;

    // QUERY the COMMENTS for current single post
    $args = array (
    	'post_id' => $post->ID,
    	'status' => 'approve'
    	);
    $comments = get_comments($args);

    // LOOP comments
    foreach($comments as $comment) {
  		// Get comment META for RATING field
  		$feedback_rating = get_comment_meta( $comment->comment_ID, 'feedback_rating', true );
      // check there is a numeric value for $feedback_rating
      if (is_numeric($feedback_rating)) {
    		// Add to TOTAL
    		$rating['total'] = $rating['total'] + $feedback_rating;
    		// Increase COUNT by 1
    		$rating['count'] = $rating['count'] + 1;
        }
  		} // End of comments LOOP
    //get the average
    if ( $rating['count'] > 0 ) {
      $rating['average'] = $rating['total'] / $rating['count'];
    }
    // QUERY the COMMENTS for current single post in the last 12 months
    $args = array (
    	'post_id' => $post->ID,
    	'status' => 'approve',
      'date_query'  => array(
        array(
          'after' => '12 months ago',
          'before' => 'tomorrow',
          'inclusive' => true,
        )
      ),
    );
    $recent_comments = get_comments($args);
    // LOOP comments
    foreach($recent_comments as $recent_comment) {
  		// Get comment META for RATING field
  		$feedback_rating = get_comment_meta( $recent_comment->comment_ID, 'feedback_rating', true );
      // check there is a numeric value for $feedback_rating
      if (is_numeric($feedback_rating)) {
    		// Add to TOTAL
    		$rating['recent_total'] = $rating['recent_total'] + $feedback_rating;
    		// Increase COUNT by 1
    		$rating['recent_count'] = $rating['recent_count'] + 1;
        }
  		} // End of comments LOOP
    //get the average
    if ( $rating['recent_count'] > 0 ) {
      $rating['recent_average'] = $rating['recent_total'] / $rating['recent_count'];
    }

    return $rating;
  }
?>
