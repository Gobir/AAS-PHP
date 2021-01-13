var idUserToSendMailTo = "";
var idAdminToRemove = "";
//Click events
function setEvents() {
    //Send TrackMe mobile application credentials access event.
    $('a.mail_access').on('click', function () {
        //Gets the target user id.
        idUserToSendMailTo = $(this).attr("id").replace("mail_", "");
        //Shows the send email model.
        $('#ajaxModalSendEmail').modal('show');
    });
    //Send email model event.
    $('#ajaxModalSendEmail .modal-footer button').on('click', function (event) {
        var $button = $(event.target);
        $(this).closest('.modal').one('hidden.bs.modal', function () {
            //Email send confirmation button clicked.
            if ($button.attr("id") === "confirmSendAccess") {
                //AJAX call to the script request-email.php.
                doAjax(idUserToSendMailTo, "request-email.php");
            }
        });
    });
    //Delete admin click event.
    $('a.delete_admin').on('click', function () {
        //Gets the target user id.
        idAdminToRemove = $(this).attr("id");
        //Shows the delete user model.
        $('#ajaxModal').modal('show');
    });
    //Delete user model event.
    $('#ajaxModal .modal-footer button').on('click', function (event) {
        var $button = $(event.target);
        $(this).closest('.modal').one('hidden.bs.modal', function () {
            //Delete confirmation button clicked.
            if ($button.attr("id") === "confirmDelete") {
                //AJAX call to the script delete-user.php.
                doAjax(idAdminToRemove, "delete-admin.php");
            }
        });
    });
}
$(document).ready(function () {
    //Initializes DataTable with responsive mode active.
    var table = $('#dataTable').DataTable({
        responsive: true
    });
    //Adds the setEvents function to the table delete & email icons click when it is in responsive mode.
    table.on('responsive-display', function (e, datatable, row, showHide, update) {
        setEvents();
    });
    //Calls the setEvents function. 
    setEvents();
});