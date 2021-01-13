$(document).ready(function () {
    //Datepicker settings.
    $('#datepicker').datepicker({
        autoclose: true,
        format: "mm-yyyy",
        viewMode: "months",
        minViewMode: "months"
    });
    $('#sick_days, #leave_days').datepicker({
        multidate: true,
	format: 'mm/dd/yyyy'
    });
    $("#report").click(function(){
        $('#form').submit();
    });
});

