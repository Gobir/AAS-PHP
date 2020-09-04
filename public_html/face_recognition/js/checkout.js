$(document).ready(function () {
    $('#paypal').click(function (){
        var usersNbr = $("#unsersNbr").val();
        $("#hidden").val(usersNbr);
        //$("#paypal-form").submit();
    });
});