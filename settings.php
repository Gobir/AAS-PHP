<?php
session_start();
require 'config.php';
require 'functions.php';
isAdminLoggedin();
/*
 * ---------------------------------------------------------------
 * settings.php
 * ---------------------------------------------------------------
 * Admin access login and email settings page.
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


//Creates a hidden input with CSFR token to use for both forms to have same value in session too for CSRF validation.
$hiddenInput = getHiddenInputString() . PHP_EOL;
//Email displayed in navigation section.
$email = $_SESSION['admin_email'];
//Gets the saved sattings fro "admin" table.
$query = "SELECT latitude, longitude, premises, admin_subscription, admin_quota, user_price FROM admin WHERE email = ? and role = ?";
$dbh = mf_connect_db();
$sth = mf_do_query($query, array($email, "admin"), $dbh);
$row = mf_do_fetch_result($sth);
//Page title.
$title = "Admin - Settings";
//Names of CSS files to load.
$css = [
    "admin",
    "octicons",
    "switch",
    "labels"
];
//Names of JS files to load.
$js = [
    "jquery",
    "bootstrap.bundle",
    "jquery.easing",
    "admin",
    "decimalonly",
    "numbersonly",
];
//Part of the sidebar menue to show and highlight.
$active = [
    "active",
    "",
    "",
    "",
    "",
    "",
    "",
    "",
    "",
    "",
    "",
    "",
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
                        <a href="settings.php"><i class="icon octicon octicon-settings"></i>&nbsp;Settings</a>
                    </li>
                </ol>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="card mb-3">
                            <div class="card-header">
                                <i class="icon octicon octicon-sign-in"></i>
                                Login Credentials</div>
                            <div class="card-body">
                                <?php if (isset($_SESSION['login_cred_error'])) { ?>
                                    <div class="alert alert-danger">
                                        <strong>Error(s)!</strong><br>
                                        <?= $_SESSION['login_cred_error']; ?>
                                    </div>    
                                    <?php
                                    unset($_SESSION['login_cred_error']);
                                } else if (isset($_SESSION['login_cred_success'])) {
                                    ?>
                                    <div class="alert alert-success">
                                        <strong>Success!</strong><br>
                                        <?= $_SESSION['login_cred_success']; ?>
                                    </div>    
                                    <?php
                                    unset($_SESSION['login_cred_success']);
                                }
                                ?>
                                <form class="form-horizontal form-bordered" method="post" action="<?= getWebRootUrl(); ?>save-settings.php">
                                    <div class="form-group">
                                        <label>Login Email<span>&nbsp;*</span></label>
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text icon octicon octicon-mail rounded-0"></span>
                                            </div>
                                            <input type="text" class="form-control rounded-0" placeholder="Email" name="email" value="<?= showPostSession("email", $email); ?>">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>Old Password<span>&nbsp;*</span></label>
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text icon octicon octicon-lock rounded-0"></span>
                                            </div>
                                            <input type="password" class="form-control rounded-0" placeholder="Old Password" name="oldpassword" value="<?= showPostSession("oldpassword", null); ?>" autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>New Password<span>&nbsp;*</span></label>
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text icon octicon octicon-lock rounded-0"></span>
                                            </div>
                                            <input type="password" class="form-control rounded-0" placeholder="New Password" name="newpassword" value="<?= showPostSession("newpassword", null); ?>">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>Confirm New Password<span>&nbsp;*</span></label>
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text icon octicon octicon-lock rounded-0"></span>
                                            </div>
                                            <input type="password" class="form-control rounded-0" placeholder="Confirm New Password" name="cnewpassword" value="<?= showPostSession("cnewpassword", null); ?>" autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="input-group mb-3">
                                            <?= $hiddenInput; ?>
                                            <input type="submit" name="login_credentials" class="btn btn-primary" value="Save" />
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="card mb-3">
                            <div class="card-header">
                                <i class="icon octicon octicon-pin"></i>
                                Office Position</div>
                            <div class="card-body">
                                <?php if (isset($_SESSION['office_sett_error'])) { ?>
                                    <div class="alert alert-danger">
                                        <strong>Error(s)!</strong><br>
                                        <?= $_SESSION['office_sett_error']; ?>
                                    </div>    
                                    <?php
                                    unset($_SESSION['office_sett_error']);
                                } else if (isset($_SESSION['office_sett_success'])) {
                                    ?>
                                    <div class="alert alert-success">
                                        <strong>Success!</strong><br>
                                        <?= $_SESSION['office_sett_success']; ?>
                                    </div>    
                                    <?php
                                    unset($_SESSION['office_sett_success']);
                                }
                                ?>
                                <form class="form-horizontal form-bordered" method="post" action="<?= getWebRootUrl(); ?>save-settings.php">
                                    <div class="form-group">
                                        <label>Latitude<span>&nbsp;* (Enter a value with 8 decimals for better precision)</span></label>
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text icon octicon octicon-pin rounded-0"></span>
                                            </div>
                                            <input type="text" class="form-control rounded-0 decimal" placeholder="Office Latitude" name="officeLatitude" value="<?= showPostSession("officeLatitude", $row["latitude"]); ?>">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>Longitude<span>&nbsp;* (Enter a value with 8 decimals for better precision)</span></label>
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text icon octicon octicon-pin rounded-0"></span>
                                            </div>
                                            <input type="text" class="form-control rounded-0 decimal" placeholder="Office Latitude" name="officeLongitude" value="<?= showPostSession("officeLongitude", $row["longitude"]); ?>">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>Premises distance (meters)<span>&nbsp;*</span></label>
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text icon octicon octicon-horizontal-rule rounded-0"></span>
                                            </div>
                                            <input type="text" class="form-control rounded-0 decimal" placeholder="Office Premises" name="officePremises" value="<?= showPostSession("officePremises", $row["premises"]); ?>">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="input-group mb-3">
                                            <?= $hiddenInput; ?>
                                            <input type="submit" name="gps" class="btn btn-primary" value="Save" />
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="card mb-3">
                            <div class="card-header">
                                <i class="icon octicon octicon-package"></i>
                                Subscription</div>
                            <div class="card-body">
                                <div class="form-group">
                                    <?php
                                    if ($row["admin_subscription"] == "Free") {
                                        $class = "label label-warning";
                                    } else {
                                        $class = "label label-success";
                                    }
                                    ?>
                                    <label>Type:&nbsp;<span class="<?= $class; ?>"><?= $row["admin_subscription"]; ?></span></label>
                                </div>
                                <div class="form-group">
                                    <label>Maximum Number of users you have:&nbsp;<span class="badge"><?= $row["admin_quota"]; ?></span></label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="card mb-3">
                            <div class="card-header">
                                <i class="icon octicon octicon-person"></i>
                                Buy Users&nbsp;<label><span class="label label-primary">1 User is at $<?= $row["user_price"]; ?></span></label>
                            </div>
                            <div class="card-body">
                                <?php if (isset($_SESSION['make_payment_error'])) { ?>
                                    <div class="alert alert-danger">
                                        <strong>Error(s)!</strong><br>
                                        <?= $_SESSION['make_payment_error']; ?>
                                    </div>    
                                    <?php
                                    unset($_SESSION['make_payment_error']);
                                } else if (isset($_SESSION['make_payment_warning'])) {
                                    ?>
                                    <div class="alert alert-warning">
                                        <strong>Warning!</strong><br>
                                    <?= $_SESSION['make_payment_warning']; ?>
                                    </div>    
                                    <?php
                                    unset($_SESSION['make_payment_warning']);
                                } else if (isset($_SESSION['make_payment_success'])) {
                                    ?>
                                    <div class="alert alert-success">
                                        <strong>Success!</strong><br>
                                    <?= $_SESSION['make_payment_success']; ?>
                                    </div>    
                                    <?php
                                    unset($_SESSION['make_payment_success']);
                                }
                                ?>
                                <form class="form-horizontal form-bordered" method="post" action="<?= getWebRootUrl(); ?>order-proceed.php">
                                    <label>Increase my number of users by<span></span></label>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text icon octicon octicon-list-ordered rounded-0"></span>
                                        </div>
                                        <input type="number" min="1" max="100" class="form-control rounded-0 numbersOnly" placeholder="Select or enter a number" name="unsersNbr" id="unsersNbr" value="<?= showPostSession("unsersNbr", null); ?>">
                                    </div>
                                    <div class="form-group">
                                        <div class="input-group mb-3">
                                            <input type="image" src="https://www.paypalobjects.com/webstatic/en_US/i/buttons/checkout-logo-medium.png" alt="Check Out With PayPal">
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php
            //Requires the footer.
            require 'footer.php';
            ?>
        </div>
    </div>
</body>
</html>
