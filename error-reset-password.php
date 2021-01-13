<?php
/*
 * ---------------------------------------------------------------
 * error-reset-password.php
 * ---------------------------------------------------------------
 * Displays this page when the reset password token is missing.
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
                    <div class="alert alert-danger">
                        <p>An error occurred!</p>
                    </div> 
                </div>
                <div class="text-center">
                    <a class="small" style="margin-top:5px;" href="<?= getWebRootUrl("login.php"); ?>">Login Page</a>
                </div>
            </div>
        </div>
    </div>
</body>