<?php
session_start();
require 'config.php';
require 'functions.php';
isAdminLoggedin();
/*
 * ---------------------------------------------------------------
 * mobile-app-logs.php
 * ---------------------------------------------------------------
 * Displays the logs errors / crashes saved by the TrackMe mobile application.
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

//Gets all users fullname and email
$query = "SELECT id, fullname, email FROM users WHERE created_by = ?";
$dbh = mf_connect_db();
$sth = mf_do_query($query, array($_SESSION["admin_email"]), $dbh);
$row = mf_do_fetch_results($sth);
$results = [];
if ($row !== false) {
    $results = $row;
}
//Case where no logs dates are selected yet.
if (!isset($_SESSION["activeLogDates"])) {
    $_SESSION["activeLogDates"] = json_encode(array());
}
//Page title.
$title = "Admin - Mobile App Logs";
//Email displayed in navigation section.
$email = $_SESSION['admin_email'];
//Names of CSS files to load.
$css = [
    "admin",
    "octicons",
    "switch",
    "bootstrap-datepicker"
];
//Names of JS files to load.
$js = [
    "jquery",
    "bootstrap.bundle",
    "jquery.easing",
    "admin",
    "autosize",
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
    "",
    "",
    "",
    "",
    "",
    "active"
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
                        <a href="<?= getWebRootUrl(); ?>mobile_app_logs.php"><i class="icon octicon octicon-bug"></i>&nbsp;Mobile App Logs</a>
                    </li>
                </ol>
                <div class="card mb-3">
                    <div class="card-header">
                        <i class="octicon octicon-bug"></i>&nbsp;Logs
                    </div>
                    <div class="card-body">
                        <form id="form" action="get-log.php" method="POST">
                            <div class="form-group">
                                <div class="input-group mb-3">
                                    <div class="input-group-append">
                                        <span class="input-group-text icon octicon octicon-device-mobile rounded-0"></span>
                                        <select class="form-control form-control-sm w-100" id="email" name="email">
                                            <option selected="selected" disabled="disabled" value="">--Select Email</option>
                                            <?php
                                            //Checks if there is an email submitted or email is in the session.
                                            $postValue = "";
                                            if (isset($_POST["email"])) {
                                                $postValue = $_POST["email"];
                                            } else if (isset($_SESSION["email"])) {
                                                $postValue = $_SESSION["email"];
                                            }
                                            foreach ($results as $result) {
                                                if ($result["email"] === $postValue) {
                                                    ?>
                                                    <option selected="selected" value="<?= $result['email']; ?>"><?= $result["email"] . " (" . $result["fullname"] . ")"; ?></option>
                                                <?php } else { ?>
                                                    <option value="<?= $result['email']; ?>"><?= $result['email'] . " (" . $result["fullname"] . ")"; ?></option>
                                                    <?php
                                                }
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="input-group mb-3">
                                    <div class="input-group-append">
                                        <span class="input-group-text icon octicon octicon-clock rounded-0"></span>
                                        <?php
                                        //Checks if there is a dropdown selected date submitted or dropdown selected date is in the session.
                                        if (isset($_POST["datepicker"])) {
                                            $postValue = $_POST["datepicker"];
                                        } else if (isset($_SESSION["logdatepicker"])) {
                                            $postValue = $_SESSION["logdatepicker"];
                                            ?>
                                            <input type="text" class="form-control form-control-sm w-100 datepicker" value="<?= $postValue; ?>" name="datepicker" id="datepicker">
                                        <?php } else { ?>
                                            <input type="text" class="form-control form-control-sm w-100 datepicker" value="<?= date("m/d/Y"); ?>" name="datepicker" id="datepicker">
                                        <?php }
                                        ?>
                                    </div>
                                </div>
                            </div>
                            <br>
                            <?= getHiddenInputString() . PHP_EOL ?>
                        </form>
                        <textarea class="form-control" id="log" placeholder="No logs available...">
                            <?php
                            if (isset($_SESSION["Logs"])) {
                                echo $_SESSION["Logs"];
                            }
                            ?>
                        </textarea>                               
                    </div>
                </div>
            </div>
            <?php
//Requires the footer.
            require 'footer.php';
            ?>
        </div>
    </div>
    <script>
        //Auto sizes the textarea with the text it contains.
        autosize(document.getElementById('log'));
        var availableDates = <?= $_SESSION["activeLogDates"]; ?>;
        //Datepicker settings.
        $('#datepicker').datepicker({
            autoclose: true,
            endDate: '+1d',
            beforeShowDay: function (dt) {
                //Shows in green the available logs dates to select.
                var dmy = ('0' + (dt.getMonth() + 1)).slice(-2) + '/' + ('0' + dt.getDate()).slice(-2) + '/' + dt.getFullYear();
                if ($.inArray(dmy, availableDates) !== -1) {
                    return {
                        tooltip: 'Log data available',
                        classes: 'green'
                    };
                }
            }
        }).on('changeDate', function (e) {
            //Submits the form when datepicker date is selected and email has already a value selected.
            if ($('#email option:selected').val() !== '') {
                $('#form').submit();
            }
        });
        //Submits the form on email dropdown change.
        $('#email').on('change', function () {
            $('#form').submit();
        });
    </script>
</body>
</html>
