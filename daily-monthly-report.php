<?php
session_start();
require 'config.php';
require 'functions.php';
isAdminLoggedin();
/*
 * ---------------------------------------------------------------
 * reports.php
 * ---------------------------------------------------------------
 * Reports for all users created.
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
//Email displayed in navigation section.
$email = $_SESSION['admin_email'];
$query = "SELECT * FROM users WHERE created_by = ?";
$dbh = mf_connect_db();
$sth = mf_do_query($query, array($email), $dbh);
$rows = mf_do_fetch_results($sth);
$results = [];
if ($rows !== false) {
    $results = $rows;
}
//Page title.
$title = "Admin - Reports";

//Names of CSS files to load.
$css = [
    "dataTables.bootstrap4",
    "responsive.bootstrap4",
    "admin",
    "octicons",
    "bootstrap-datepicker",
    "chart-pie",
    "anychart-ui.min",
    "anychart-font.min"
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
    "bootstrap-datepicker",
    "reports",
    "anychart-base.min",
    "anychart-ui.min",
    "anychart-exports.min"
];
//Part of the sidebar menue to show and highlight.
$active = [
    "",
    "",
    "",
    "",
    "",
    "",
    "",
    "",
    "show",
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
                        <a href="#"><i class="icon octicon octicon-organization"></i>&nbsp;Reports By User</a>
                    </li>
                    <li class="breadcrumb-item active">Reports</li>
                </ol>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="card mb-3">
                            <form id="form" action="<?= getWebRootUrl(); ?>get-daily-monthly-report.php" method="POST">
                                <div class="card-header">
                                    <p><i class="icon octicon octicon-settings"></i>&nbsp;Enter Parameters&nbsp;</p>
                                </div>
                                <div class="card-body">
                                    <div class="form-group" style="margin-bottom: -1em;">
                                        <div class="input-group mb-3">
                                            <div class="input-group-append">
                                                <span class="input-group-text icon octicon octicon-person rounded-0"></span>
                                                <select class="form-control form-control-sm w-100" id="users" name="users">
                                                    <option selected="selected" disabled="disabled" value="">--Select User--</option>
                                                    <?php foreach ($results as $result) { ?>
                                                        <option value="<?= $result["email"]."-".$result["fullname"]; ?>"<?php
                                                        if (isset($_SESSION["user_email"]) && $_SESSION["user_email"] == $result["email"]) {
                                                            echo " selected='selected'";
                                                        }
                                                        ?>><?= $result["email"] . " (" . $result["fullname"] . ")"; ?></option>
<?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <br>
                                    <div class="form-group" style="margin-bottom: -1rem;">
                                        <div class="input-group mb-3">
                                            <div class="input-group-append">
                                                <span class="input-group-text icon octicon octicon-clock rounded-0"></span>
                                                <input type="text" class="form-control form-control-sm w-100 datepicker" placeholder="--select month--" value="<?php
if (isset($_SESSION["user_datepicker"])) {
    echo $_SESSION["user_datepicker"];
}
?>" name="user_datepicker" id="datepicker">
                                            </div>
                                        </div>
                                    </div>

<?php
if (isset($_SESSION["user_reports"])) {
    $sum = 0;
    ?>
                                        <br>
                                        <hr>
                                        <p>Number Of Days Present&nbsp;:&nbsp;<?php
                                            if (isset($_SESSION["present"])) {
                                                echo $_SESSION["present"];
                                                $sum = $sum + $_SESSION["present"];
                                            } else {
                                                echo 0;
                                            }
                                            ?></p>
                                        <p>Number Of Days Absent&nbsp;:&nbsp;<?php
                                            if (isset($_SESSION["absent"])) {
                                                echo $_SESSION["absent"];
                                                $sum = $sum + $_SESSION["absent"];
                                            } else {
                                                echo 0;
                                            }
                                            ?></p>
                                        <p>Number Of Leave Days&nbsp;:&nbsp;<?php
                                            if (isset($_SESSION["leave_days_array"]) && !emptyElementExists($_SESSION["leave_days_array"])) {
                                                echo sizeof($_SESSION["leave_days_array"]);
                                                $sum = $sum + sizeof($_SESSION["leave_days_array"]);
                                            } else {
                                                echo 0;
                                            }
                                            ?></p>
                                        <p>Number Of Sick Days&nbsp;:&nbsp;<?php
                                            if (isset($_SESSION["sick_days_array"]) && !emptyElementExists($_SESSION["sick_days_array"])) {
                                                echo sizeof($_SESSION["sick_days_array"]);
                                                $sum = $sum + sizeof($_SESSION["sick_days_array"]);
                                            } else {
                                                echo 0;
                                            }
                                            ?></p>
                                        <br>
                                        <hr>
                                        <p>Total Payable Days&nbsp;:&nbsp;<?= $sum; ?></p>
                                <?php } ?>
                                </div>
                                <div class="card-footer small text-muted">
                                    <input id="report" type="submit" class="btn btn-primary" value="Load Report" />
                                </div>
<?= getHiddenInputString() . PHP_EOL ?>
                            </form>

                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="card mb-3">
                            <div class="card-header">
                                <p>
                                    <i class="icon octicon octicon-list-unordered"></i>Daily - Monthly Report&nbsp;
                                    <span id="notification" style="margin-left: 20px;font-weight: bold;"></span>
                                </p>
                            </div>
                            <div style="height: 670px !important;overflow: scroll;overflow-x: hidden;" class="card-body">
                                <table class="table table-bordered dt-responsive nowrap" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Dates</th>
                                            <th>In</th>
                                            <th>Out</th>
                                            <th>Duration</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if (isset($_SESSION["user_reports"])) {
                                            $tot = 0.00;
                                            foreach ($_SESSION["user_reports"] as $key => $val) {
                                                ?>
                                                <tr>
                                                    <td>
                                                        <?php
                                                        if (in_array($key, $_SESSION["sick_days_array"])) {
                                                            echo $key . " (Sick)";
                                                        } else if (in_array($key, $_SESSION["leave_days_array"])) {
                                                            echo $key . " (Leave)";
                                                        } else {
                                                            if (empty($val)) {
                                                                echo $key . " (Absent)";
                                                            } else {
                                                                echo $key . " (Present)";
                                                            }
                                                        }
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <?php
                                                        foreach ($val as $key1 => $val1) {
                                                            echo $val1[0] . "<br/>";
                                                        }
                                                        ?>
                                                    </td>
                                                    <td><?php
                                                foreach ($val as $key2 => $val2) {
                                                    echo $val2[1] . "<br/>";
                                                }
                                                        ?></td>
                                                    <td><?php
                                                        foreach ($val as $key3 => $val3) {
                                                            echo $val3[2] . "<br/>";
                                                            $tot = bcadd($tot, $val3[2], 2);
                                                        }
                                                        ?></td>
                                                </tr>
        <?php
    }
}
?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="card-footer small text-muted">
<?php if (isset($_SESSION["user_reports"])) { ?>
                                    <p>Total Hours Spent:&nbsp;<?= $tot; ?></p>
<?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="chart"></div>
<?php
//Requires the sidebar.
require 'footer.php';
?>
            </div>
        </div>
        <script>
<?php
if (isset($_SESSION["sick_days_array"]) && isset($_SESSION["leave_days_array"]) &&
        isset($_SESSION["absent"]) && isset($_SESSION["present"])) {
    ?>
                anychart.onDocumentReady(function () {
                    var chart = anychart.pie3d([
                        {x: 'PRESENT', value: <?= $_SESSION["present"]; ?>, normal: {fill: "#00ff00"}},
                        {x: 'ABSENT', value: <?= $_SESSION["absent"]; ?>, normal: {fill: "#ff0000"}},
                        {x: 'LEAVE', value: <?= sizeof($_SESSION["leave_days_array"]); ?>, normal: {fill: "#0080ff"}},
                        {x: 'SICK', value: <?= sizeof($_SESSION["sick_days_array"]); ?>, normal: {fill: "#ffa500"}}
                    ]);
                    chart.title('<?= $_SESSION["user_fullname"]; ?>').radius('43%').container('chart').draw();
                });
<?php } ?>
        </script>
</body>
</html>

