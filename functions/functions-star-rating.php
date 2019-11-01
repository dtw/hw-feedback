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
 */

  function feedbackstarrating($float,$type = 'individual',$colour = '') {
    // check the type is valid
    if ( $type !== 'individual' or $type !== 'average' ) {
      die("Invalid type argument");
    }
    // has a colour been passed?
    if ( isset($colour)) {
      // add a leading space
      $colour = ' '.$colour
    }
    // output the stars
    // we never want less than one star
		if ($float < 1) {
			echo '<i class="fas fa-star fa-lg' . $colour . '"></i>';
		} else {
      // output whole stars based on the integer value of float
			for ($int_count = 1; $int_count <= floor($float); $int_count++) {
				echo '<i class="fas fa-star fa-lg' . $colour . '"></i> ';
				$star_count++;
			}
      // if type is average, check if half a star needed
      if ( $type === 'average' ) {
  			if (($float - floor($float)) >= 0.25 && $float < 0.75) {
  				echo '<i class="fa fa-star-half-empty fa-lg' . $colour . '"></i> ';
  				$star_count++;
  			}
      }
      // add empty stars up to 5
			while ($star_count < 5) {
				echo '<i class="far fa-star fa-lg' . $colour . '"></i> ';
				$star_count++;
			}
		}
  }
?>
