function validateForm() {
    var x = document.forms["commentform"]["author"].value;
    if (x == null || x == "") {
        alert("Please give your name");
        return false;
    }
}