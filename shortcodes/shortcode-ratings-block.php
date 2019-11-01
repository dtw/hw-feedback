<?php

function hw_shortcode_ratings_block() {

ob_start();

$id = get_the_id();



if ( get_post_meta( $id, 'hw_services_overall_rating', true ) )  { ?>



            <div class="rate-and-review-scores">


		<div class="row">
        	<div class="col-md-6">
	            <p>How people are treated:</p>
    		        </div>
			<div class="col-md-6">
			<?php $rating = get_post_meta( $id, 'hw_services_how_people_treated', true );
				feedbackstarrating($rating,'green');
					?>
				</div></div>

		<div class="row">
        	<div class="col-md-6">
	            <p>Personal choice:</p>
    		        </div>
			<div class="col-md-6">
			<?php $rating = get_post_meta( $id, 'hw_services_personal_choice', true );
				feedbackstarrating($rating,'green');
					?>
						</div></div>


		<div class="row">
        	<div class="col-md-6">
	            <p>Just like being at home:</p>
    		        </div>
			<div class="col-md-6">

			<?php $rating = get_post_meta( $id, 'hw_services_being_home', true );
				feedbackstarrating($rating,'green');
					?>
					</div></div>

		<div class="row">
        	<div class="col-md-6">
	            <p>Privacy:</p>
    		        </div>
			<div class="col-md-6">
			<?php $rating = get_post_meta( $id, 'hw_services_privacy', true );
			  feedbackstarrating($rating,'green');
					?>
					</div></div>

		<div class="row">
        	<div class="col-md-6">
	            <p>Quality of life:</p>
    		        </div>
			<div class="col-md-6">

			<?php $rating = get_post_meta( $id, 'hw_services_quality_life', true );
			  feedbackstarrating($rating,'green');
					?>
					</div></div>



                	</div><!-- end of rate and review panel -->




            			<?php } ?>


<?php return ob_get_clean(); ?>
<? }

add_shortcode( 'ratings', 'hw_shortcode_ratings_block' ); ?>
