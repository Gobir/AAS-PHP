<?php
session_start();
require 'config.php';
require 'functions.php';
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

//Page title.
$title = "Super User - Settings";
//Email displayed in navigation section.
$email = $_SESSION['superuser_email'];
$query = "SELECT * FROM paypal_keys";
$dbh = mf_connect_db();
$sth = mf_do_query($query, array(), $dbh);
$row = mf_do_fetch_result($sth);
//Names of CSS files to load.
$css = [
    "admin",
    "octicons",
    "switch"
];
//Names of JS files to load.
$js = [
    "jquery",
    "bootstrap.bundle",
    "jquery.easing",
    "admin"
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
                        <a href="settings.php"><i class="icon octicon octicon-settings"></i>&nbsp;Super User Settings</a>
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
                                <i class="icon octicon octicon-credit-card"></i>
                                PayPal Credentials</div>
                            <div class="card-body">
                                <?php if (isset($_SESSION['paypal_cred_error'])) { ?>
                                    <div class="alert alert-danger">
                                        <strong>Error(s)!</strong><br>
                                        <?= $_SESSION['paypal_cred_error']; ?>
                                    </div>    
                                    <?php
                                    unset($_SESSION['paypal_cred_error']);
                                } else if (isset($_SESSION['paypal_cred_success'])) {
                                    ?>
                                    <div class="alert alert-success">
                                        <strong>Success!</strong><br>
                                        <?= $_SESSION['paypal_cred_success']; ?>
                                    </div>    
                                    <?php
                                    unset($_SESSION['paypal_cred_success']);
                                }
                                ?>
                                <form class="form-horizontal form-bordered" method="post" action="<?= getWebRootUrl(); ?>save-settings.php">
                                    <div class="form-group">
                                        <label>SandBox Client ID<span>&nbsp;*</span></label>
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text icon octicon octicon-lock rounded-0"></span>
                                            </div>
                                            <input type="text" class="form-control rounded-0" placeholder="Sand Box Client ID" name="sandboxClientId" value="<?= showPostSession("sandboxClientId", $row["sandbox_client_id"]); ?>">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>SandBox Secret ID<span>&nbsp;*</span></label>
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text icon octicon octicon-lock rounded-0"></span>
                                            </div>
                                            <input type="text" class="form-control rounded-0" placeholder="Sand Box Secret ID" name="sandboxSecretId" value="<?= showPostSession("sandboxSecretId", $row["sandbox_secret_id"]); ?>" autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>Live Client ID<span>&nbsp;*</span></label>
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text icon octicon octicon-lock rounded-0"></span>
                                            </div>
                                            <input type="text" class="form-control rounded-0" placeholder="Live Box Client ID" name="liveboxClientId" value="<?= showPostSession("liveboxClientId", $row["livebox_client_id"]); ?>">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>Live Secret ID<span>&nbsp;*</span></label>
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text icon octicon octicon-lock rounded-0"></span>
                                            </div>
                                            <input type="text" class="form-control rounded-0" placeholder="Live Box Secret ID" name="liveboxSecretId" value="<?= showPostSession("liveboxSecretId", $row["livebox_secret_id"]); ?>" autocomplete="off">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <span class="switch switch-md">
                                            <label for="switch_box">SandBox&nbsp;</label>
                                            <input type="checkbox" class="switch" name="switch_box" id="switch_box" <?= showPostSession("switch_box", $row["status"]); ?>>
                                            <label for="switch_box">&nbsp;Live</label>
                                        </span>
                                    </div>
                                    <div class="form-group">
                                        <div class="input-group mb-3">
                                            <?= $hiddenInput; ?>
                                            <input type="submit" name="paypal" class="btn btn-primary" value="Save" />
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
