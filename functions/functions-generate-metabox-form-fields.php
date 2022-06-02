<?php
/**
 * Generate metabox fields
 *
 * @package   hw-feedback
 * @author    Phil Thiselton <dibblethewrecker@gmail.com>
 * @license   GPL-2.0+
 * @copyright 2022 Phil Thiselton
 *
 * Description: Generate different metabox fields - used in loops in post-types-taxonomies.php

 * @param array $post
 */

/* $array contains field_id, label, value from CQC api plus post ID and size of the form field */
function hw_feedback_generate_metabox_form_field($array,$id,$size) {
    $value = get_post_meta( $id, $array[0], true );
      echo '<label for="'.$array[0].'">' . $array[1] . ' </label>';
      echo '<input type="text" id="'.$array[0].'" name="'.$array[0].'" value="' . esc_attr( $value ) . '" size="' . $size . '" />';
      //switch to HTML ?><button class="ed_button button button-small" type="button" onclick="update_from_cqc('<?php echo $array[0]?>','<?php echo $array[0]?>_cqc_field')"><= Update</button><input type="text" value="<?php echo $array[2]?>" id="<?php echo $array[0]?>_cqc_field" size="<?php echo $size?>" readonly>
      <br /><br />
<?php }

function hw_feedback_generate_metabox_radio_field($array,$name,$value){
  foreach ($array as $key => $val) {
    echo $key .' <input type="radio" name="' . $name . '" value="' . strval($val) . '" '; if ($value == $val) { echo 'checked'; }; echo '> ';
  }
}
?>
