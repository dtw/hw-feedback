<?php
/*
Based on code by Eduardo Zulian - http://flutuante.com.br - cheers Eduardo
Based on code by Jason King - http://kingjason.com - cheers Jason
*/

/**
 * Register the widget
 */
function SF_register_widget_hwbucks_latest_dic() {
	register_widget( 'SF_HWBucks_Latest_DIC_Widget' );
}
add_action( 'widgets_init', 'SF_register_widget_hwbucks_latest_dic' );
/**
 * A Recent Feedback widget
 * Shows latest feedback comment in a bootstrap panel followed by the next 3 most recent comments in a three column format.
 *
 */
class SF_HWBucks_Latest_DIC_Widget extends WP_Widget {
	/**
	 * Sets up a new widget instance.
	 *
	 * @access public
	 */
	function __construct() {
		parent::__construct( 'SF_HWBucks_Latest_DIC_Widget',
		$name = 'HW Latest DIC Visit',
		array(
			'classname'   => 'scaffold_widget_hwbucks_latest_dic widget_latest_dic',
			'description' => 'Display full details of the latest DIC visit as a panel.'
	)
		);
	}

	function SF_HWBucks_Latest_DIC_Widget() {
		self::__construct();
	}
	/**
	 * Outputs the content for a new widget instance.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args 		Widget arguments.
	 * @param array $instance 	Saved values from database.
	 */
	function widget( $args, $instance ) {
		//extract( $args );
		$title = $instance['title'] ;
		$panel_colour = $instance['panel_colour'] ;

		// The Query - gets the first service with a hw_services_overall_rating greater than or equal to 1
		$dic_query = new WP_Query(array(
			'post_type' => 'Local_services',
			// 'orderby' => 'rand',
			'showposts' => 1,
			'post_status' => 'publish',
			'meta_query' => array(
				array(
					'key'     => 'hw_services_overall_rating',
					'value'   => 1,
					'compare' => '>='
				)
			),
		));

		// no use of $before_widget
		echo '<div class="row latest-dic-visit">';
		if( $dic_query->have_posts() ) :
			while($dic_query->have_posts()) : $dic_query->the_post();
			$dic = get_post(); ?>
				<div class="panel col-md-12 col-sm-12 col-xs-12 panel-<?php echo $panel_colour ?>" id="dignity-in-care"><!-- start panel -->
					<div class="row">
						<?php $img_orient = orientation_check(get_post_thumbnail_id($post->post_ID));
						if ( $img_orient == 'ls') {
							echo '<!--ls--><div class="col-md-4 col-sm-6 hidden-xs panel-icon-left">';
						} elseif ( $img_orient == 'pt') {
							echo '<!--pt--><div class="col-md-2 col-sm-3 hidden-xs panel-icon-left">';
						} elseif ( $img_orient == 'sq') {
							echo '<!--sq--><div class="col-md-3 col-sm-4 hidden-xs panel-icon-left">';
						}
						?>
							<a class="img-anchor" href="
								<?php the_permalink(); ?>" rel="bookmark">
								<?php the_post_thumbnail('medium', array('class' => 'panel-icon-img')); ?>
							</a>
						</div>
						<?php if ( $img_orient == 'ls') {
							echo '<!--ls--><div class="col-md-8 col-sm-6 col-xs-12 panel-text-right">';
						} elseif ( $img_orient == 'pt') {
							echo '<!--pt--><div class="col-md-10 col-sm-9 col-xs-12 panel-text-right">';
						} elseif ( $img_orient == 'sq') {
							echo '<!--sq--><div class="col-md-9 col-sm-8 col-xs-12 panel-text-right">';
						}
						?>
							<div class="row">
								<div class="col-md-12 panel-title-right">
									<h2><?php echo $title ?></h2>
								</div>
							</div>
							<div class="row">
								<div class="col-md-12 panel-content-right">
									<span class="city">
										<a class="title-link" href="
											<?php the_permalink(); ?>" rel="bookmark">
											<?php the_title(); ?>
										</a>
										<?php $city = get_post_meta( $dic->ID, 'hw_services_city', true ); if ($city) { echo $city; }?>
									</span>
									<p class="panel-excerpt"> <?php echo get_the_excerpt(); ?> </p>
									<p>
										<?php $rating = get_post_meta( $dic->ID, 'hw_services_overall_rating', true );
											echo hw_feedback_star_rating($rating,array('colour' => 'green','size' => 'fa-lg'));
											if ($rating == 1) echo '<span class="screen-reader-text">'.$rating.' star</span>';
											else echo '<span class="screen-reader-text">'.$rating.' stars</span>';
										?>
									</p>
									<p class="visit-date">Visited on
										<?php echo the_date(); ?>
									</p>
								</div>
								<?php // get_template_part("elements/comments-rating-average"); ?>
							</div>
						</div>
					</div>
					<!-- end of column -->
				</div>
					<!-- end of panel -->
		<?php
			endwhile;
			endif; wp_reset_query();
			echo '</div>';
}
	// Save widget settings

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = wp_strip_all_tags( $new_instance['title'] );
		$instance['panel_colour'] = wp_strip_all_tags( $new_instance['panel_colour'] );
		return $instance;
	}
	function form( $instance ) {
		$title = ! empty( $instance['title'] ) ? $instance['title'] : 'Latest Dignity in Care visit';
		$panel_colour = ! empty( $instance['panel_colour'] ) ? $instance['panel_colour'] : 'green';
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>">Content title:</label>
			<input type="text" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('panel_colour'); ?>">Panel colour:
				<select class='widefat' id="<?php echo $this->get_field_id('panel_colour'); ?>"
						 name="<?php echo $this->get_field_name('panel_colour'); ?>" type="text">
					<?php
					/* This array and loop generates the rows for the dropdown menu. Blue results in panel-blue. Matching styles required in CSS */
					$colourArray = ["Grey", "Orange", "Blue", "Green", "Pink", "Turquoise"];
						foreach ($colourArray as $colour)  {
							echo "<option value='" . strtolower($colour) . "'";
							echo ($panel_colour==strtolower($colour))?'selected':'';
							echo ">" . $colour . "</option>";
						}
					?>
				</select>
			</label>
		</p>
	<?php
	}
}
?>
