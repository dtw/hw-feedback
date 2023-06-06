<?php

/* Just some helpers */

function update_comment_status ($post_id,$status) {
  wp_update_post(array(
    'ID' => $post_id,
    'comment_status' => $status
  ));
  error_log('hw-feedback: comments '. $status );
}

?>