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
$title = "Admin - General Report";

//Names of CSS files to load.
$css = [
    "dataTables.bootstrap4",
    "responsive.bootstrap4",
    "admin",
    "octicons",
    "chart-bar",
    "bootstrap-datepicker"
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
    "reports"
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
                        <a href="#"><i class="icon octicon octicon-organization"></i>&nbsp;Report For All Users</a>
                    </li>
                    <li class="breadcrumb-item active">General Report</li>
                </ol>
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card mb-3">
                            <form id="form" action="<?= getWebRootUrl(); ?>get-general-report.php" method="POST">
                                <div class="card-header">
                                    <p><i class="icon octicon octicon-settings"></i>&nbsp;Enter Month&nbsp;</p>
                                </div>
                                <div class="card-body">
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
                                    <input id="general_report" type="submit" class="btn btn-primary" value="Load General Report" />
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
                                            <th>Staff</th>
                                            <th>Present</th>
                                            <th>Absent</th>
                                            <th>Early</th>
                                            <th>Late</th>
                                            <th>Sick</th>
                                            <th>Leave</th>
                                            <th>Hours</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if (isset($_SESSION['general_report'])) {
                                            foreach ($_SESSION['general_report'] as $row) {
                                                ?>
                                                <tr>
                                                    <td><?= $row[0]; ?></td>
                                                    <td><?= $row[1]; ?></td>
                                                    <td><?= $row[2]; ?></td>
                                                    <td><?= $row[3]; ?></td>
                                                    <td><?= $row[4]; ?></td>
                                                    <td><?= $row[5]; ?></td>
                                                    <td><?= $row[6]; ?></td>
                                                    <td><?= $row[7]; ?></td>
                                                </tr>
                                            <?php } ?>
                                        <?php } ?>
                                    </tbody>
                                </table>
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
        <script src="https://cdn.anychart.com/releases/v8/js/anychart-base.min.js"></script>
        <script src="https://cdn.anychart.com/releases/v8/js/anychart-ui.min.js"></script>
        <script src="https://cdn.anychart.com/releases/v8/js/anychart-exports.min.js"></script>
        <script src="https://cdn.anychart.com/releases/v8/js/anychart-cartesian-3d.min.js"></script>
        <script src="https://cdn.anychart.com/releases/v8/themes/light_blue.min.js"></script>
        <link href="https://cdn.anychart.com/releases/v8/css/anychart-ui.min.css" type="text/css" rel="stylesheet">
        <link href="https://cdn.anychart.com/releases/v8/fonts/css/anychart-font.min.css" type="text/css" rel="stylesheet">
        <script>
<?php if (isset($_SESSION['js_reports'])) { ?>
                anychart.onDocumentReady(function () {
                    // create data set on our data
                    var dataSet = anychart.data.set(<?= json_encode($_SESSION['js_reports']); ?>);
                    var s1 = dataSet.mapAs({x: 0, value: 1});
                    var s2 = dataSet.mapAs({x: 0, value: 2});
                    var s3 = dataSet.mapAs({x: 0, value: 3});
                    var s4 = dataSet.mapAs({x: 0, value: 4});
                    var s5 = dataSet.mapAs({x: 0, value: 5});
                    var s6 = dataSet.mapAs({x: 0, value: 6});
                    var chart = anychart.column3d();
                    chart.animation(true);
                    chart.title('HARLEQUIN AVIATION STAFF ATTENDANCE');
                    // temp variable to store series instance
                    var series;
                    // helper function to setup label settings for all series
                    var setupSeries = function (series, name) {
                        series.name(name);
                        switch (name) {
                            case 'PRESENT':
                                series.normal().fill('#00ff00');
                                break;
                            case 'ABSENT':
                                series.normal().fill('#ff0000');
                                break;
                            case 'LEAVE':
                                series.normal().fill('#0080ff');
                                break;
                            case 'EARLY':
                                series.normal().fill('#ffff00');
                                break;
                            case 'LATE':
                                series.normal().fill('#8b4513');
                                break;
                            case 'SICK':
                                series.normal().fill('#ffa500');
                                break;
                            default:
                                series.normal().fill('#f48fb1');
                                break;
                        }
                    };
                    series = chart.column(s1);
                    series.xPointPosition(0.10);
                    setupSeries(series, 'PRESENT');
                    series = chart.column(s2);
                    series.xPointPosition(0.25);
                    setupSeries(series, 'ABSENT');
                    series = chart.column(s3);
                    series.xPointPosition(0.40);
                    setupSeries(series, 'EARLY');
                    series = chart.column(s4);
                    series.xPointPosition(0.55);
                    setupSeries(series, 'LATE');
                    series = chart.column(s5);
                    series.xPointPosition(0.70);
                    setupSeries(series, 'SICK');
                    series = chart.column(s6);
                    series.xPointPosition(0.85);
                    setupSeries(series, 'LEAVE');
                    chart.legend().enabled(true).fontSize(13).padding([0, 0, 20, 0]);
                    chart.interactivity().hoverMode('single');
                    chart.container('chart');
                    chart.draw();
                });
<?php } ?>
        </script>
</body>
</html>

