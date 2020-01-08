<?php
/**
 * Output feedback star rating
 *
 * @package   hw-feedback
 * @author    Phil Thiselton <dibblethewrecker@gmail.com>
 * @license   GPL-2.0+
 * @copyright 2019 Phil Thiselton
 *
 * Description: Takes three arguments to output star ratings for hw-feedback
 * @param float $rating Takes any number
 * @param string $colour A colour to pass as a CSS class e.g. 'green'
 * @param string $size A size corresponding to fontawesome styles https://fontawesome.com/how-to-use/on-the-web/styling/sizing-icons
 */

  function feedbackstarrating($rating, $options = array()) {
    //set $defaults
    $defaults = array(
      'colour' => '',
      'size' = ''
    );
    // merge passed arguements with defaults
    $config = array_merge($defaults, $options);

    // if the rating is a float then we're working with an average rating
    $average = is_float($rating);
    // has a colour been passed?
    if ( $config['colour'] != '') {
      // add a leading space
      $colour = ' '.$config['colour'];
    }
    // has a size been passed?
    if ( $config['size'] != '') {
      // add a leading space
      $size = ' '.$config['size'];
    }
    // output the stars
    $output .= '';
    // we never want less than one star
		if ($rating < 1) {
			$output .= '<i class="fas fa-star' . $size . $colour . '"></i>';
		} else {
      // output whole stars based on the integer value of float
			for ($int_count = 1; $int_count <= floor($rating); $int_count++) {
				$output .= '<i class="fas fa-star' . $size . $colour . '"></i> ';
				$star_count++;
			}
      // if rating is average, check if half a star needed
      if ( $average) {
  			if (($rating - floor($rating)) >= 0.25 && ($rating - floor($rating)) < 0.75) {
  				$output .= '<i class="fas fa-star-half-alt' . $size . $colour . '"></i> ';
  				$star_count++;
  			}
      }
      // add empty stars up to 5
			while ($star_count < 5) {
				$output .= '<i class="far fa-star' . $size . $colour . '"></i> ';
				$star_count++;
			}
		}
  return $output;
  }
?>
