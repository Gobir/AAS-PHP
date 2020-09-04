<?php
session_start();
require 'config.php';
require 'functions.php';
isAdminLoggedin();
/*
 * ---------------------------------------------------------------
 * add-user.php
 * ---------------------------------------------------------------
 * Adds a user.
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
$title = "Admin - Add User";
//Email displayed in navigation section.
$email = $_SESSION['admin_email'];
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
    "phonescript"
];
//Part of the sidebar menue to show and highlight.
$active = [
    "",
    "",
    "show",
    "active",
    "",
    "",
    ""
];
//Requires the header.
require 'header.php';
?>
<body id="page-top">
    <?php
    //Requires the navigation menue.
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
                        <a href="#"><i class="icon octicon octicon-organization"></i>&nbsp;Users</a>
                    </li>
                    <li class="breadcrumb-item active">Add User</li>
                </ol>
                <div class="card mb-3">
                    <div class="card-header">
                        <i class="icon octicon octicon-person"></i>&nbsp;
                        Adding new user</div>
                    <div class="card-body">
                        <?php if (isset($_SESSION['save_error'])) { ?>
                            <div class="alert alert-danger">
                                <strong>Error(s)!</strong><br>
                                <?= $_SESSION['save_error']; ?>
                            </div>    
                            <?php
                            unset($_SESSION['save_error']);
                        } else if (isset($_SESSION['save_success'])) {
                            ?>
                            <div class="alert alert-success">
                                <strong>Success!</strong><br>
                                <?= $_SESSION['save_success']; ?>
                            </div>    
                            <?php
                            unset($_SESSION['save_success']);
                        }
                        ?>
                        <form class="form-horizontal form-bordered" enctype="multipart/form-data" method="post" action="<?= getWebRootUrl(); ?>save-user.php">
                            <div class="form-group">
                                <label>Profile Picture (minimum 400 x 400 px)</label>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <input id="file" name="file" type="file" class="file" data-show-upload="false" data-show-preview="false" accept="image/jpg" data-msg-placeholder="Select file...">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Full Name:<span class="required">&nbsp;*</span></label>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text icon octicon octicon-person rounded-0"></span>
                                    </div>
                                    <input type="text" class="form-control rounded-0" placeholder="Full Name" name="fullname" value="<?= showPostSession("fullname", null); ?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Email:<span class="required">&nbsp;*</span></label>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text icon octicon octicon-mail rounded-0"></span>
                                    </div>
                                    <input type="text" class="form-control rounded-0" placeholder="Email" name="email" value="<?= showPostSession("email", null); ?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Tracking Time Interval:<span class="required">&nbsp;*</span></label>
                                <div class="input-group mb-3">
                                    <div class="input-group-append" data-area="true">
                                        <span class="input-group-text icon octicon octicon-clock rounded-0"></span>
                                        <select class="form-control form-control-sm w-100" name="tracking_time_interval">
                                            <?php
                                            $selectedTrackingTime = showPostSession("tracking_time_interval", null);
                                            ?>
                                            <option value=""<?= showSelectSavedValue($selectedTrackingTime, ""); ?> disabled="disabled">-- Select Tracking Time Interval --</option>
                                            <option value="1"<?= showSelectSavedValue($selectedTrackingTime, "1"); ?>>1 minutes</option>
                                            <option value="2"<?= showSelectSavedValue($selectedTrackingTime, "2"); ?>>2 minutes</option>
                                            <option value="10"<?= showSelectSavedValue($selectedTrackingTime, "10"); ?>>10 minutes</option>
                                            <option value="20"<?= showSelectSavedValue($selectedTrackingTime, "20"); ?>>20 minutes</option>
                                            <option value="30"<?= showSelectSavedValue($selectedTrackingTime, "30"); ?>>30 minutes</option>
                                            <option value="40"<?= showSelectSavedValue($selectedTrackingTime, "40"); ?>>40 minutes</option>
                                            <option value="50"<?= showSelectSavedValue($selectedTrackingTime, "50"); ?>>50 minutes</option>
                                            <option value="60"<?= showSelectSavedValue($selectedTrackingTime, "60"); ?>>60 minutes</option>
                                            <option value="70"<?= showSelectSavedValue($selectedTrackingTime, "70"); ?>>70 minutes</option>
                                            <option value="80"<?= showSelectSavedValue($selectedTrackingTime, "80"); ?>>80 minutes</option>
                                            <option value="90"<?= showSelectSavedValue($selectedTrackingTime, "90"); ?>>90 minutes</option>
                                            <option value="100"<?= showSelectSavedValue($selectedTrackingTime, "100"); ?>>100 minutes</option>
                                            <option value="110"<?= showSelectSavedValue($selectedTrackingTime, "110"); ?>>110 minutes</option>
                                            <option value="120"<?= showSelectSavedValue($selectedTrackingTime, "120"); ?>>120 minutes</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label></label>
                                <div class="input-group mb-3">
                                    <?= getHiddenInputString() . PHP_EOL ?>
                                    <input type="submit" class="btn btn-primary" value="Save" />
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
</body>
</html>