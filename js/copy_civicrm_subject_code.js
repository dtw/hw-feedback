async function copy_civicrm_uuid() {
  /* Get the text field */
  var copyText = document.getElementById("civicrm-uuid-field");

  /* Select the text field */
  copyText.select();

   /* Copy the text inside the text field */
  await navigator.clipboard.writeText(copyText.value);
}

async function copy_civicrm_subject_code() {
  /* Get the text field */
  var copyText = document.getElementById("civicrm-subject-code-field");

  /* Select the text field */
  copyText.select();

   /* Copy the text inside the text field */
  await navigator.clipboard.writeText(copyText.value);
}

async function hw_feedback_store_values() {
  if (!sessionStorage.content) {
    sessionStorage.setItem('content', document.getElementById('content').value)
    sessionStorage.setItem('newcomment_author_url', document.getElementById('newcomment_author_url').value)
    sessionStorage.setItem('name', document.getElementById('name').value)
    sessionStorage.setItem('email', document.getElementById('email').value)
    sessionStorage.setItem('newcomment_author_phone', document.getElementById('newcomment_author_phone').value)
    sessionStorage.setItem('newcomment_author_address', document.getElementById('newcomment_author_address').value)
  }
}

async function hw_feedback_withhold_comment($substituted_text) {
  /* Save the value */
  hw_feedback_store_values()

  /* Replace text in comment field */
  document.getElementById('content').value = $substituted_text;
}

async function hw_feedback_partial_comment($substituted_text) {
  /* Save the value */
  hw_feedback_store_values()

  /* Append text in comment field */
  document.getElementById('content').value += '\n\n'+$substituted_text;
}

async function hw_feedback_restore_comment() {
  /* Replace text in comment field */
  document.getElementById('content').value = sessionStorage.getItem('content')
  document.getElementById('newcomment_author_url').value = sessionStorage.getItem('newcomment_author_url')
  document.getElementById('name').value = sessionStorage.getItem('name')
  document.getElementById('email').value = sessionStorage.getItem('email')
  document.getElementById('newcomment_author_phone').value = sessionStorage.getItem('newcomment_author_phone')
  document.getElementById('newcomment_author_address').value = sessionStorage.getItem('newcomment_author_address')
}

async function hw_feedback_clear_personal_data() {
  hw_feedback_store_values()
  /* Replace text in comment field */
  document.getElementById('newcomment_author_url').value = ''
  document.getElementById('name').value = ''
  document.getElementById('email').value = ''
  document.getElementById('newcomment_author_phone').value = ''
  document.getElementById('newcomment_author_address').value = ''
}

async function update_from_cqc($target, $source) {
  document.getElementById($target).value = document.getElementById($source).value;
}

async function update_ods_code($target, $source) {
  document.getElementById($target).value = document.getElementById($source).innerText;
  if (document.getElementById($target).value != '') {
    document.getElementById('hw-services-ods-code-alert').style.display = "inline-block";
  }
}

async function update_ods_code_single($target, $value) {
  document.getElementById($target).value = $value;
  document.getElementById('hw-services-ods-code-alert').style.display = "inline-block";
}

/* this is used on local_services edit page */
jQuery(document).ready(function ($) {
  $("#hw-services-cqc-location").change(function(){
    if ($("#hw-services-cqc-location").value != ''){
      $("#hw-services-cqc-location-alert").css("display", "inline-block");
    }
  });
});

jQuery(document).ready(function ($) {
  $("#hw-services-ods-code").change(function () {
    if ($("#hw-services-ods-code").value != '') {
      $("#hw-services-ods-code-alert").css("display", "inline-block");
    }
  });
});

jQuery(document).ready(function ($) {
  $("#hw-feedback-preview-only").change(function(){
    if ($(this).is(":checked")) {
      $("#hw-feedback-import-alert").css("display", "none");
      $("#hw-feedback-form-submit").prop("value", "Preview");
    }
    if (!$(this).is(":checked")) {
      $("#hw-feedback-import-alert").css("display", "inline-block");
      $("#hw-feedback-form-submit").prop("value", "Import");
    }
  });
});

jQuery(document).ready(function ($) {
  if ($("#hw_feedback_field_email_from_address").val() != '') {
    $("#hw-feedback-address-alert").css("display", "none");
    $("#hw_feedback_field_email_from_name").removeAttr("disabled");
  }
  else {
    $("#hw-feedback-address-alert").css("display", "inline-block");
    $("#hw_feedback_field_email_from_name").attr("disabled", "disabled");
  }
  $("#hw_feedback_field_email_from_address").change(function () {
    if ($(this).val() != '') {
      $("#hw-feedback-address-alert").css("display", "none");
      $("#hw_feedback_field_email_from_name").removeAttr("disabled");
    }
    else {
      $("#hw-feedback-address-alert").css("display", "inline-block");
      $("#hw_feedback_field_email_from_name").attr("disabled", "disabled");
    }
  });
});
