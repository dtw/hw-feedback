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
  $("#hw_services_cqc_location").change(function(){
    if ($("#hw_services_cqc_location").value != ''){
      $("#hw_services_cqc_location_alert").css("display", "inline-block");
    }
  });
});
