<?php
require 'functions.php';
isAdminLoggedin();
/*
 * ---------------------------------------------------------------
 * error-light-version-user.php
 * ---------------------------------------------------------------
 * Displays this page when the light version user id to display does not exist or the user was not found.
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
$title = "Admin - Light Version User";
//Email displayed in navigation section.
$email = $_SESSION['admin_email'];
//Names of CSS files to load.
$css = [
    "admin",
    "octicons"
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
    "",
    "",
    "show",
    "",
    "active",
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
                        <a href="#"><i class="icon octicon octicon-organization"></i>&nbsp;Light Version Users</a>
                    </li>
                    <li class="breadcrumb-item active">User Records</li>
                </ol>
                <div class="card mb-3">
                    <div class="card-header">
                        <i class="icon octicon octicon-pencil"></i>&nbsp;
                        Records for user</div>
                    <div class="card-body">
                        <h2><i class="iconBig octicon octicon-alert"></i>&nbsp;Light Version User Not Found!</h2>
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
