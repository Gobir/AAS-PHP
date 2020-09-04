//To allow only number and + sign for phone number field
$(document).ready(function () {
    $('.number').keypress(function (evt) {
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        if (charCode !== 43 && charCode > 31 && (charCode < 48 || charCode > 57)) {
            return false;
        } else {
            return true;
        }
    });
});
