<?php
session_start();
require 'config.php';
require 'functions.php';
isSuperUserLoggedin();
/*
 * ---------------------------------------------------------------
 * list-users.php
 * ---------------------------------------------------------------
 * Lists all users created.
 * HTML source at: https://github.com/BlackrockDigital/startbootstrap-sb-admin
 */

/* The MIT License (MIT)
 * 
 * Copyright (C) 2013-2019 Blackrock Digital LLC
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy* 
 * of this software and associated documentation files (the "Software"), to deal* 
 * in the Software without restriction, including without limitation the rights* 
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell* 
 * copies of the Software, and to permit persons to whom the Software is* 
 * furnished to do so, subject to the following conditions:* 
 * 
 * The above copyright notice and this permission notice shall be included in* 
 * all copies or substantial portions of the Software.* 
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR* 
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,* 
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE* 
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER* 
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,* 
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN* 
 * THE SOFTWARE.
 */

$query = "SELECT * FROM admin WHERE role = ?";
$dbh = mf_connect_db();
$sth = mf_do_query($query, array("admin"), $dbh);
$rows = mf_do_fetch_results($sth);
$results = [];
if ($rows !== false) {
    $results = $rows;
}
//Page title.
$title = "Super User - Admins";
//Email displayed in navigation section.
$email = $_SESSION['superuser_email'];
//Names of CSS files to load.
$css = [
    "dataTables.bootstrap4",
    "responsive.bootstrap4",
    "admin",
    "octicons"
];
//Names of JS files to load.
$js = [
    "jquery",
    "bootstrap.bundle",
    "jquery.easing",
    "jquery.dataTables",
    "dataTables.bootstrap4",
    "dataTables.responsive",
    "responsive.bootstrap4",
    "admin",
    "request-email"
];
//Part of the sidebar menue to show and highlight.
$active = [
    "",
    "",
    "show",
    "",
    "active",
    "",
    ""
];
//Requires the header.
require 'header.php';
?>
<body id="page-top">
    <?php
    //Requires the navigation.
    require 'navigation.php';
    ?>
    <div id="wrapper">
        <?php
        //Requires the sidebar.
        require 'sidebar.php';
        ?>
        <div id="content-wrapper">
            <div class="container-fluid">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="#"><i class="icon octicon octicon-organization"></i>&nbsp;Admins</a>
                    </li>
                    <li class="breadcrumb-item active">List</li>
                </ol>
                <div class="card mb-3">
                    <div class="card-header">
                        <i class="octicon octicon-three-bars"></i>&nbsp;
                        List of all admins</div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <?php if (isset($_SESSION['delete_error'])) { ?>
                                <div class="alert alert-danger">
                                    <strong>Error(s)!</strong><br>
                                    <?= $_SESSION['delete_error']; ?>
                                </div>    
                                <?php
                                unset($_SESSION['delete_error']);
                            } else if (isset($_SESSION['detele_success'])) {
                                ?>
                                <div class="alert alert-success">
                                    <strong>Success!</strong><br>
                                    <?= $_SESSION['detele_success']; ?>
                                </div>    
                                <?php
                                unset($_SESSION['detele_success']);
                            }
                            if (isset($_SESSION['mail_send_failed'])) {
                                ?>
                                <div class="alert alert-danger">
                                    <strong>Error!</strong><br>
                                    <?= $_SESSION['mail_send_failed']; ?>
                                </div>    
                                <?php
                                unset($_SESSION['mail_send_failed']);
                            } else if (isset($_SESSION['mail_send_success'])) {
                                ?>
                                <div class="alert alert-success">
                                    <strong>Success!</strong><br>
                                    <?= $_SESSION['mail_send_success']; ?>
                                </div>    
                                <?php
                                unset($_SESSION['mail_send_success']);
                            }
                            ?>
                            <table class="table table-bordered dt-responsive nowrap" id="dataTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Full Name</th>
                                        <th>Email</th>
                                        <th>Admin Quota</th>
                                        <th>User Price</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if (!empty($results)) {
                                        foreach ($results as $result) {
                                            ?>
                                            <tr>
                                                <td><?= $result["fullname"]; ?></td>
                                                <td><?= $result["email"]; ?></td>
                                                <td><?= $result["admin_quota"]; ?></td>
                                                <td><?= $result["user_price"]; ?></td>
                                                <td>
                                                    <a href="edit-admin.php?id=<?= $result["id"];?>"><i title="Edit" class="octicon octicon-pencil"></i></a>
                                                    <a href="#" data-toggle="modal" class="delete_admin" id="<?= $result["id"];?>"><i title="Delete Admin" class="octicon octicon-trashcan"></i></a>
                                                    
                                                    <a href="#" data-toggle="modal" class="mail_access" id="mail_<?= $result["id"];?>"><i title="Send Credentials By Email" class="octicon octicon-mail"></i></a>
                                                    
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <?php
            //Requires the sidebar.
            require 'footer.php';
            ?>
        </div>
    </div>
    <div class="modal fade" id="ajaxModalSendEmail" tabindex="-1" role="dialog" aria-labelledby="ajaxModalSendEmail" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Sending Admin Access Credentials!</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Please confirm you want to send to this admin access credentials?</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <button class="btn btn-primary" id="confirmSendAccess" type="button" data-dismiss="modal">Confirm</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="ajaxModal" tabindex="-1" role="dialog" aria-labelledby="ajaxModal" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Delete Admin!</h5>
                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">Please confirm you want to delete this admin?</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    <button class="btn btn-primary" id="confirmDelete" type="button" data-dismiss="modal">Confirm</button>
                </div>
            </div>
        </div>
    </div>
    <script>
        //Does an AJAX call to the privided POST URL.
        function doAjax(data, postURL) {
            $.ajax({
                url: postURL,
                cache: false,
                type: "POST",
                data: {
                    data: JSON.stringify(data),
                    csrf_token: '<?= getCsrfToken() ?>'
                },
                success: function () {
                    location.reload(true);
                },
                error: function () {
                    location.reload(true);
                }
            });
        }
    </script>
</body>
</html>
