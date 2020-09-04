<?php
session_start();
require 'config.php';
require 'functions.php';
isSuperUserLoggedin();
/*
 * ---------------------------------------------------------------
 * edit-user.php
 * ---------------------------------------------------------------
 * Edits a user.
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

//Gets id of the user to edit from the URL
$id = $_GET['id'];
//Validates the user id.
if (!ctype_digit($id)) {
    //Case where the user id to edit is not a number.
    require 'error_edit_admin.php';
    exit();
} else {
    //Checks if the user to edit id exists.
    $query = "SELECT * FROM admin WHERE id = ?";
    $dbh = mf_connect_db();
    $sth = mf_do_query($query, array($id), $dbh);
    $rows = mf_do_fetch_result($sth);
    $results = [];
    if ($rows !== false) {
        $results = $rows;
    }
    if (sizeof($results) === 0) {
        //Case where the user to edit id does not exist.
        require 'error-edit-admin.php';
        exit();
    }
}
//Page title.
$title = "Super User - Edit Admin";
//Email displayed in navigation section.
$email = $_SESSION['superuser_email'];
//Names of CSS files to load.
$css = [
    "admin",
    "octicons",
    "fileinput"
];
//Names of JS files to load.
$js = [
    "jquery",
    "bootstrap.bundle",
    "jquery.easing",
    "admin",
    "fileinput",
    "numbersonly",
    "decimalonly"
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
                    <li class="breadcrumb-item active">Edit Admin</li>
                </ol>
                <div class="card mb-3">
                    <div class="card-header">
                        <i class="icon octicon octicon-pencil"></i>&nbsp;
                        Editing admin</div>
                    <div class="card-body">
                        <?php if (isset($_SESSION['edit_error'])) { ?>
                            <div class="alert alert-danger">
                                <strong>Error(s)!</strong><br>
                                <?= $_SESSION['edit_error']; ?>
                            </div>    
                            <?php
                            unset($_SESSION['edit_error']);
                        } else if (isset($_SESSION['edit_success'])) {
                            ?>
                            <div class="alert alert-success">
                                <strong>Success!</strong><br>
                                <?= $_SESSION['edit_success']; ?>
                            </div>    
                            <?php
                            unset($_SESSION['edit_success']);
                        }
                        ?>
                        <form class="form-horizontal form-bordered" method="post" action="<?= getWebRootUrl(); ?>update-admin.php">
                            <div class="form-group">
                                <label>Full Name:<span class="required">&nbsp;*</span></label>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text icon octicon octicon-person rounded-0"></span>
                                    </div>
                                    <input type="text" class="form-control rounded-0" placeholder="Full Name" name="fullname" value="<?= showPostSession("fullname", $results["fullname"]); ?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Email:<span class="required">&nbsp;*</span></label>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text icon octicon octicon-mail rounded-0"></span>
                                    </div>
                                    <input type="text" class="form-control rounded-0" placeholder="Email" name="email" value="<?= showPostSession("email", $results["email"]); ?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Maximum Number of Users:<span class="required">&nbsp;*</span></label>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text icon octicon octicon-person rounded-0"></span>
                                    </div>
                                    <input type="text" class="form-control rounded-0 numbersOnly" placeholder="Number of Users Allowed" name="maxUsers" value="<?= showPostSession("maxUsers", $results["admin_quota"]); ?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Price Per User:<span class="required">&nbsp;*</span></label>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text icon octicon octicon-credit-card rounded-0"></span>
                                    </div>
                                    <input type="text" class="form-control rounded-0 numbersOnly" placeholder="Price Per User" name="priceUser" value="<?= showPostSession("priceUser", $results["user_price"]); ?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <label></label>
                                <div class="input-group mb-3">
                                    <?= getHiddenInputString() . PHP_EOL ?>
                                    <input type="submit" class="btn btn-primary" value="Update" />
                                    <input type="button" id="back" class="btn btn-warning" style="margin-left: 5px;" value="Back"/>
                                    <input type="hidden" name="row_id" class="form-control" value="<?= $results["id"]; ?>"/>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <?php
            //Requires the footer.
            require 'footer.php';
            ?>
        </div>
    </div>
    <script>
        //Back button click event.
        $('#back').click(function () {
            //Redirects to users page.
            window.location.href = "<?= getWebRootUrl(); ?>admins.php";
        });
    </script>
</body>
</html>
