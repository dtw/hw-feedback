async function copy_civicrm_subject_code() {
  /* Get the text field */
  var copyText = document.getElementById("civicrm-subject-code-field");

  /* Select the text field */
  copyText.select();

   /* Copy the text inside the text field */
  await navigator.clipboard.writeText(copyText.value);
}

async function update_from_cqc($target, $source) {
  document.getElementById($target).value = document.getElementById($source).value;
}

jQuery(document).ready(function ($) {
  $("#hw-services-cqc-location").change(function(){
    if ($("#hw-services-cqc-location").value != ''){
      $("#hw-services-cqc-location-alert").css("display", "inline-block");
    }
  });
});
