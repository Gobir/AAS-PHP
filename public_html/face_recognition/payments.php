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

$query = "SELECT * FROM payments";
$dbh = mf_connect_db();
$sth = mf_do_query($query, array(), $dbh);
$rows = mf_do_fetch_results($sth);
$results = [];
if ($rows !== false) {
    $results = $rows;
}
//Page title.
$title = "Super User - Payments";
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
    "admin"
];
//Part of the sidebar menue to show and highlight.
$active = [
    "",
    "",
    "",
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
                        <a href="#"><i class="icon octicon octicon-credit-card"></i>&nbsp;Payments</a>
                    </li>
                    <li class="breadcrumb-item active">List</li>
                </ol>
                <div class="card mb-3">
                    <div class="card-header">
                        <i class="octicon octicon-three-bars"></i>&nbsp;
                        List of all payments</div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered dt-responsive nowrap" id="dataTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Payment ID</th>
                                        <th>Payment Status</th>
                                        <th>Payment Reference</th>
                                        <th>Payer Name</th>
                                        <th>Total</th>
                                        <th>Create Time</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    if (!empty($results)) {
                                        foreach ($results as $result) {
                                            ?>
                                            <tr>
                                                <td><?= $result["payment_id"]; ?></td>
                                                <td><?= $result["payment_captures_status"]; ?></td>
                                                <td><?= $result["payment_reference_id"]; ?></td>
                                                <td><?= $result["payment_full_name"]; ?></td>
                                                <td><?= $result["payment_amount"] . " " . $result["payment_currency_code"]; ?></td>
                                                <td><?= $result["payment_create_time"]; ?></td>
                                                <td>

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
</body>
</html>
