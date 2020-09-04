<?php
session_start();
require 'config.php';
require 'functions.php';
/*
 * ---------------------------------------------------------------
 * reset-password.php
 * ---------------------------------------------------------------
 * Reset password page.
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

//Gets the password reset token from the URL.
if(!isset($_GET['token'])){
    require 'error-reset-password.php';
    exit();
}
$token = $_GET['token'];
//Checks if the password recovery token exists.
$dbh = mf_connect_db();
$query = "SELECT expiry_token, used_token FROM admin WHERE recovery_token = ?;";
$sth = mf_do_query($query, array($token), $dbh);
$row = mf_do_fetch_result($sth);
$result = [];
if ($row !== false) {
    $result = $row;
}
if (sizeof($result) === 0) {
    //Case where the password recovery token does not exist.
    $_SESSION['error'] = "Invalid password reset token!";
} else if ($result["used_token"] === "Y") {
    //Case where the password recovery token is already used.
    $_SESSION['error'] = "This token is no longer valid!";
} else if ($result["expiry_token"] < time()) {
    //Case where the password recovery token has expired.
    $link = getWebRootUrl() . "forgot-password.php";
    //Creates a session message password recovery token reset.
    $_SESSION['error'] = "Password reset token expired! Please request a new one here: <a href='" . $link . "'>" . $link . "</a>";
}
//Page title.
$title = "Admin - Password Reset";
//Names of CSS files to load.
$css = [
    "admin"
];
//Names of JS files to load.
$js = [
    "jquery",
    "bootstrap.bundle",
    "jquery.easing"
];
//Requires the header.
require 'header.php';
?>
<body class="bg-dark">
    <div class="container">
        <div class="card card-login mx-auto mt-5">
            <div class="card-header">Admin - Reset Password</div>
            <div class="card-body">
                <div class="text-center mb-4">
                    <h5>Reset your password.</h5>
                    <?php if (isset($_SESSION["error"])) { ?>
                        <div class="alert alert-danger">
                            <?= $_SESSION['error']; ?>
                        </div> 
                        <?php
                        unset($_SESSION["error"]);
                    } else {
                        ?>
                        <p>Enter your new password below and confirm it.</p>
                    </div>
                    <?php if (isset($_SESSION['reset_failed'])) { ?>
                        <div class="alert alert-danger">
                            <?= $_SESSION['reset_failed']; ?>
                        </div> 
                        <?php
                        unset($_SESSION['reset_failed']);
                    }
                    ?>
                    <form action="<?= getWebRootUrl(); ?>save-reset-password.php" method="POST">
                        <div class="form-group">
                            <div class="form-label-group">
                                <input type="password" id="password" name="password" class="form-control" placeholder="Enter your new password" autocomplete="off">
                                <label for="password">Enter your new password</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-label-group">
                                <input type="password" id="cpassword" name="cpassword" class="form-control" placeholder="Confirm your new password" autocomplete="off">
                                <label for="cpassword">Confirm your new password</label>
                            </div>
                        </div>
                        <input type="hidden" name="token" class="form-control" value="<?= $token; ?>"/>
                        <?= getHiddenInputString() . PHP_EOL ?>
                        <input class="btn btn-primary btn-block" style="margin-top:5px;" type="submit" value="Save Password">
                    </form>
                <?php } ?>
                <div class="text-center">
                    <a class="small" style="margin-top:5px;" href="<?= getWebRootUrl()."login.php"; ?>">Login Page</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
