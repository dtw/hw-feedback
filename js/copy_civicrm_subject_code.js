async function copy_civicrm_subject_code() {
  /* Get the text field */
  var copyText = document.getElementById("civicrm-subject-code-field");

  /* Select the text field */
  copyText.select();

   /* Copy the text inside the text field */
  await navigator.clipboard.writeText(copyText.value);
}
