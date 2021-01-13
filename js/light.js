$(document).ready(function () {
    //Initializes DataTable with responsive mode active.
    $('#dataTable').DataTable({
        responsive: true,
        order: [],
        columnDefs: [{
                targets: 'no-sort',
                orderable: false
            }]
    });
});
