$(document).ready(function () {
    $(".decimal").keypress(function (event) {
        return isNumber(event, this);
    });
});
function isNumber(evt, element) {
    var charCode = (evt.which) ? evt.which : event.keyCode;
    if ((charCode !== 45 || $(element).val().indexOf('-') !== -1) && // Check minus and only once.
            (charCode !== 46 || $(element).val().indexOf('.') !== -1) && // Check for dots and only once.
            (charCode < 48 || charCode > 57))
        return false;
    return true;
}