function hw_feedback_submit_form() {
  document.hwfeedbackcqcimportform.submit();
  document.getElementById("hw-feedback-cqc-import-throbber").style.display = "block";
  document.getElementById("hw-feedback-cqc-import-results").style.display = "none";
}

/* From https://css-tricks.com/a-complete-guide-to-svg-fallbacks/ */

/* Check if browser supports svg - hasFeature is DEPRECATED in modern browsers but we need this check on old browsers... */
function svgasimg() {
  return document.implementation.hasFeature(
    "http://www.w3.org/TR/SVG11/feature#Image", "1.1");
}

/* if not, use the data-fallback, which is set to an alternative image */
if (!svgasimg()) {
  var e = document.getElementsByTagName("img");
  if (!e.length) {
    e = document.getElementsByTagName("IMG");
  }
  for (var i = 0, n = e.length; i < n; i++) {
    var img = e[i],
      src = img.getAttribute("src");
    if (src.match(/svgz?$/)) {
      /* URL ends in svg or svgz */
      img.setAttribute("src",
        img.getAttribute("data-fallback"));
    }
  }
}

