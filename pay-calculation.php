<?php
session_start();
require 'config.php';
require 'functions.php';
isAdminLoggedin();
/*
 * ---------------------------------------------------------------
 * reports.php
 * ---------------------------------------------------------------
 * General reports for all users created.
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
//Page title.
$title = "Admin - Pay Calculation";

//Names of CSS files to load.
$css = [
    "dataTables.bootstrap4",
    "responsive.bootstrap4",
    "admin",
    "octicons",
    "bootstrap-datepicker",
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
    "bootstrap-datepicker"
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
    "",
    "",
    "active",
    ""
];
$query = "SELECT * FROM users WHERE created_by = ?";
$dbh = mf_connect_db();
$sth = mf_do_query($query, array($email), $dbh);
$rows = mf_do_fetch_results($sth);
$results = [];
if ($rows !== false) {
    $results = $rows;
}

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
                        <a href="#"><i class="icon octicon octicon-organization"></i>&nbsp;Pay Calculation By User</a>
                    </li>
                    <li class="breadcrumb-item active">Pay Calculation</li>
                </ol>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card mb-3">
                            <form id="form" action="<?= getWebRootUrl(); ?>get-pay-calculation.php" method="POST">
                                <div class="card-header">
                                    <p><i class="icon octicon octicon-settings"></i>&nbsp;Select User&nbsp;</p>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <div class="input-group mb-3">
                                            <div class="input-group-append" data-area="true">
                                                <span class="input-group-text icon octicon octicon-person rounded-0"></span>
                                                <select class="form-control form-control-sm w-100" id="users" name="users">
                                                    <option selected="selected" disabled="disabled" value="">--Select User--</option>
                                                    <?php foreach ($results as $result) { ?>
                                                        <option <?php if (isset($_SESSION["user_email"]) && $_SESSION["user_email"] == $result["email"]) {
                                                        echo " selected='selected'";
                                                    } ?> value="<?= $result["email"] . "-" . $result["fullname"]; ?>"><?= $result["email"] . " (" . $result["fullname"] . ")"; ?></option>
<?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>  
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
                                </div>
                                <div class="card-footer small text-muted">
                                    <input id="general_report" type="submit" class="btn btn-primary" value="Load Pay Calculation" />
                                </div>
<?= getHiddenInputString() . PHP_EOL ?>
                            </form>

                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="card mb-3">
                            <div class="card-header">
                                <p>
                                    <i class="icon octicon octicon-list-unordered"></i>General - Report&nbsp;
                                    <span id="notification" style="margin-left: 20px;font-weight: bold;"></span>
                                </p>
                            </div>
                            <div class="card-body">
                                <table class="table table-bordered dt-responsive nowrap" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Deduction Per Hour</th>
                                            <th>Hours Missed</th>
                                            <th>Extra Hours</th>
                                            <th>Hours Missed Deduction</th>
                                            <th>Basic Salary</th>
                                            <th>Tax</th>
                                            <th>Pension</th>
                                            <th>Net Salary</th>
                                            <th>Bonus</th>
                                            <th>Total Days Pay</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if (isset($_SESSION['pay_reports'])) {
                                            foreach ($_SESSION['pay_reports'] as $result) {
                                                ?>
                                                <tr>
                                                    <td><?= $_SESSION["user_fullname"]; ?></td>
                                                    <td><?= $result["deduction_hour"] . " " . $result["currency"]; ?></td>
                                                    <td><?= $result["missHours"]; ?></td>
                                                    <td><?= $result["overWorkedHours"]; ?></td>
                                                    <td><?= $result["dayMissedDeduction"] . " " . $result["currency"]; ?></td>
                                                    <td><?= $result["basicSalary"] . " " . $result["currency"]; ?></td>
                                                    <td><?= $result["tax"] . " " . $result["currency"]; ?></td>
                                                    <td><?= $result["pension"] . " " . $result["currency"]; ?></td>
                                                    <td><?= $result["net"] . " " . $result["currency"]; ?></td>
                                                    <td><?= $result["bonus"] . " " . $result["currency"]; ?></td>
                                                    <td><?= $result["totalPay"] . " " . $result["currency"]; ?></td>
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
        <script>
            $('#datepicker').datepicker({
                autoclose: true,
                format: "mm-yyyy",
                viewMode: "months",
                minViewMode: "months"
            });
        </script>
</body>
</html>

