<?php
session_start();
require 'config.php';
require 'functions.php';
isAdminLoggedin();
/*
 * ---------------------------------------------------------------
 * light-version-user.php
 * ---------------------------------------------------------------
 * Lists all light version records for selected user.
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
    require 'error_light-version-user.php';
    exit();
} else {
    //Checks if the user to edit id exists.
    $query = "SELECT id, fullname, profile_img FROM users WHERE id = ?";
    $dbh = mf_connect_db();
    $sth = mf_do_query($query, array($id), $dbh);
    $row = mf_do_fetch_result($sth);
    $result = [];
    if ($row !== false) {
        $result = $row;
    }
    if (sizeof($result) === 0) {
        //Case where the light version user id does not exist.
        require 'error_light-version-user.php';
        exit();
    } else {
        $light_version_user = fixId($result["id"]);
        $query = "SELECT id, last_action, last_action_date FROM light WHERE light_version_id = ? ORDER BY id DESC";
        $dbh = mf_connect_db();
        $sth = mf_do_query($query, array($light_version_user), $dbh);
        $rows = mf_do_fetch_results($sth);
    }
}


//Page title.
$title = "Admin - Users";
//Names of CSS files to load.
$css = [
    "dataTables.bootstrap4",
    "responsive.bootstrap4",
    "admin",
    "octicons",
    "swipebox",
    "labels"
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
    "light"
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
                        <a href="#"><i class="icon octicon octicon-organization"></i>&nbsp;Light Version User</a>
                    </li>
                    <li class="breadcrumb-item active">Records List</li>
                </ol>
                <div class="card mb-3">
                    <div class="card-header">
                        <i class="octicon octicon-three-bars"></i>&nbsp;
                        List of all records</div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <div class="form-group">
                                <label>Profile Picture of <b><?= $result["fullname"]; ?></b></label>
                                <div class="input-group mb-3">
                                    <img src="<?= getWebRootUrl(); ?>images/users/<?= $result["profile_img"]; ?>" class="img-thumbnail" alt="<?= $result["fullname"]; ?>"> 
                                </div>
                            </div>
                            <div class="form-group">
                                <label>User ID:&nbsp;<b><?= $light_version_user; ?></b></label>
                            </div>
                            <table class="table table-bordered dt-responsive nowrap" id="dataTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Check In/Out</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if (!empty($rows)) {
                                        foreach ($rows as $row) {?>
                                            <tr>
                                                <td><?= $row["last_action"]; ?></td>
                                                <td><?= $row["last_action_date"]; ?></td>
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
</body>
</html>
