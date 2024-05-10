<?php
/*
Based on code by Eduardo Zulian - http://flutuante.com.br - cheers Eduardo
Based on code by Jason King - http://kingjason.com - cheers Jason
*/

/**
 * Register the widget
 */
function SF_register_widget_hwbucks_recent_compact_feedback() {
  register_widget( 'SF_HWBucks_Recent_Compact_Feedback_Widget' );
}
add_action( 'widgets_init', 'SF_register_widget_hwbucks_recent_compact_feedback' );
/**
 * A Recent Feedback widget
 * Shows latest feedback comment in a bootstrap panel followed by the next 3 most recent comments in a three column format.
 *
 */
class SF_HWBucks_Recent_Compact_Feedback_Widget extends WP_Widget {
  /**
   * Sets up a new widget instance.
   *
   * @access public
   */
  function __construct() {
    parent::__construct( 'SF_HWBucks_Recent_Compact_Feedback_Widget',
    $name = 'HW Recent Feedback (compact)',
    array(
      'classname'   => 'scaffold_widget_hwbucks_recent_compact_feedback widget_recent_compact_feedback',
      'description' => 'Display 3 most recent star ratings.'
  )
    );
  }

  function SF_HWBucks_Recent_Compact_Feedback_Widget() {
    self::__construct();
  }
  /**
   * Outputs the content for a new widget instance.
   *
   * @see WP_Widget::widget()
   *
   * @param array $args     Widget arguments.
   * @param array $instance   Saved values from database.
   */
  function widget( $args, $instance ) {
    //extract( $args );
    $title = $instance['title'] ;
    $panel_colour = $instance['panel_colour'] ;

    // The Query - gets the last 4 approved comments for post_type local_services
    $args = array(
      'status' => 'approve',
      'post_type' => 'local_services',
      'number' => 3,
    );
    $comments_query = new WP_Comment_Query;
    $comments = $comments_query->query( $args );

    // no use of $before_widget

    $reviewcount = 1;
    // Comment Loop
    if ( $comments ) { ?>
      <div class='col-md-12 col-sm-12 col-xs-12 panel panel-feedback'><!--start widget output-->
      <div class="row">
        <div class="col-md-12 panel-title">
          <h2><?php echo $title ?></h2>
        </div>
      </div>
      <div class="row three-column compact-item-container">
        <?php foreach ( $comments as $comment ) { ?>
          <!-- start a smaller panel -->
          <div class="col-md-4 col-sm-6 hidden-xs compact-item">
          <?php                     // Get icon for taxonomy term
            $term_ids = get_the_terms( $comment->comment_post_ID, 'service_types' );  // Find taxonomies
            $term_id = $term_ids[0]->term_id;                      // Get taxonomy ID
            $term_icon = get_term_meta( $term_id, 'icon', true );            // Get meta
          ?>
              <!-- contains each panel -->
              <div class="row">
              <!-- there is no main panel 4x to 2x to 1x-->
                <!-- add a container and wrap the term icon in a hyperlink to the post -->
                <div class="col-md-3 col-sm-3 col-xs-12 text-center service-icon-container">
                  <a class="img-anchor" href="
                  <?php echo get_the_permalink($comment->comment_post_ID); ?>
                  ">
                  <?php echo wp_get_attachment_image( $term_icon, 'thumbnail', true, array( 'class' => 'service-icon-sm panel-icon-img', 'alt' => get_the_title($comment->comment_post_ID) ) ); ?>
                  </a>
                </div><!-- close icon container -->
          <?php //this outputs one opening div ?>
            <div class="col-md-9 col-sm-9 col-xs-12 service-info-container-sm">
              <h3>
                <a href="
                  <?php echo get_the_permalink($comment->comment_post_ID); ?>">
                  <?php echo get_the_title($comment->comment_post_ID); ?>
                </a>
              </h3>
          <?php
          // Display star rating
          $individual_rating = get_comment_meta( $comment->comment_ID, 'feedback_rating', true ); ?>
              <p class="star-rating p-rating">
                <?php echo hw_feedback_star_rating($individual_rating,array('size' => 'fa-lg'));
                  if ($individual_rating == 1) echo '<span class="screen-reader-text">'.$individual_rating.' star</span>';
                  else echo '<span class="screen-reader-text">'.$individual_rating.' stars</span>';
                ?>
              </p>
              <p class="review-date-time">
                <strong>
                  <?php echo human_time_diff( strtotime($comment->comment_date), current_datetime() ); ?> ago
                </strong>
              </p>
        </div><!-- close service-info-container-sm | service-info-container | panel-text-right -->
      </div> <!-- close row -->
    </div> <!-- close panel | compact-item -->
          <?php  $reviewcount = $reviewcount + 1;
        } // end of loop ?>
  </div><!-- close compact-item-container -->
</div><!-- end of feedback row -->
  <?php }
}
  // Save widget settings

  function update( $new_instance, $old_instance ) {
    $instance = $old_instance;
    $instance['title'] = wp_strip_all_tags( $new_instance['title'] );
    $instance['panel_colour'] = wp_strip_all_tags( $new_instance['panel_colour'] );
    return $instance;
  }
  function form( $instance ) {
    $title = ! empty( $instance['title'] ) ? $instance['title'] : 'Recent feedback from the public';
    $panel_colour = ! empty( $instance['panel_colour'] ) ? $instance['panel_colour'] : 'grey';
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
