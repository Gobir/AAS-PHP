var idUserToRemove = "";
var idUserToSendMailTo = "";
//Click events
function setEvents() {
    //Delete user click event.
    $('a.delete_user').on('click', function () {
        //Gets the target user id.
        idUserToRemove = $(this).attr("id");
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
                doAjax(idUserToRemove, "delete-user.php");
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
    //Initializes swipebox plugin
    $('.swipebox').swipebox();
});