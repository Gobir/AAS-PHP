<?php

session_start();
require 'config.php';
require 'functions.php';
isAdminLoggedin();
/*
 * ---------------------------------------------------------------
 * get-tracking-data.php
 * ---------------------------------------------------------------
 * This file is called via POST and used to get the tracking data for the selected user.
 */

isPost("get-general-report.php");
//Creates POST and SESSION variables.
$datepicker = $_POST["user_datepicker"];
$_SESSION["user_datepicker"] = $datepicker;
$dbh = mf_connect_db();
$email = $_SESSION['admin_email'];
$querySelect = "SELECT * FROM users WHERE created_by = ?";
$sthSelect = mf_do_query($querySelect, array($email), $dbh);
$rows = mf_do_fetch_results($sthSelect);
$users = [];
if ($rows !== false) {
    $users = $rows;
}
$reports = [];
$js_reports = [];
$expDate = explode("-", $datepicker);
foreach ($users as $user) {
    $selectQuery = "SELECT * FROM absent_leave WHERE email = ?";
    $sth = mf_do_query($selectQuery, array($user["email"]), $dbh);
    $row = mf_do_fetch_result($sth);
    $sick_days_array = [];
    $leave_days_array = [];
    if ($row !== false) {
        $sick_days_array = explode(",", $row["sick_days"]);
        $leave_days_array = explode(",", $row["leave_days"]);
        $startTime = DateTime::createFromFormat('H:i', $row["normal_start_time"]);
        $endTime = DateTime::createFromFormat('H:i', $row["normal_end_time"]);
    } else {
        $startTime = DateTime::createFromFormat('H:i', "08:00");
        $endTime = DateTime::createFromFormat('H:i', "17:00");
    }
    //Do Validation here
    $_SESSION["sick_days_array"] = excludeDates($sick_days_array, $expDate[0], $expDate[1]);
    $_SESSION["leave_days_array"] = excludeDates($leave_days_array, $expDate[0], $expDate[1]);
    $dates = getDatesForGivenMonth($expDate[0], $expDate[1]);
    $absent = 0;
    $present = 0;
    $late = 0;
    $early = 0;
    $tot = 0.00;
    foreach ($dates as $date) {
        $query = "SELECT * FROM tracking WHERE action IN (?, ?) AND email = ? AND date = ? ORDER BY id ASC";
        $sth = mf_do_query($query, array("Check In", "Check Out", $user["email"], $date), $dbh);
        $rows = mf_do_fetch_results($sth);
        if (empty($rows) && !in_array($date, $sick_days_array) && !in_array($date, $leave_days_array)) {
            $absent++;
        } else if (!empty($rows) && !in_array($date, $sick_days_array) && !in_array($date, $leave_days_array)) {
            $present++;
        }
        $timeIn = "";
        $timeOut = "";
        $inOut = [];
        foreach ($rows as $row) {
            if ($row["action"] == "Check In") {
                $timeIn = $row["time"];
                $currentStartTime = DateTime::createFromFormat('H:i', $timeIn);
                if ($currentStartTime < $startTime) {
                    $early++;
                }
                if ($currentStartTime > $endTime) {
                    $late++;
                }
                array_push($inOut, str_replace("/", "-", $row["date"]) . " " . $timeIn);
            }
            if ($row["action"] == "Check Out") {
                $timeOut = $row["time"];
                $currentEndTime = DateTime::createFromFormat('H:i', $timeOut);
                if ($currentEndTime > $endTime || $currentEndTime < $startTime) {
                    $late++;
                }
                array_push($inOut, str_replace("/", "-", $row["date"]) . " " . $timeOut);
            }
            if (sizeof($inOut) == 2) {
                $diff = getSecondsDiff($inOut[0], $inOut[1]);
                $tot = bcadd($tot, bcdiv((string) $diff, "3600", 2), 2);
                $inOut = [];
            }
        }
        if (!emptyElementExists($_SESSION["sick_days_array"])) {
            $sick = sizeof($_SESSION["sick_days_array"]);
        }
        if (!emptyElementExists($_SESSION["leave_days_array"])) {
            $leave = sizeof($_SESSION["leave_days_array"]);
        }
    }
    array_push($reports, [$user["fullname"], $present, $absent, $early, $late, $sick, $leave, $tot]);
    array_push($js_reports, [$user["fullname"], $present, $absent, $early, $late, $sick, $leave]);
}
$_SESSION['general_report'] = $reports;
$_SESSION['js_reports'] = $js_reports;
redirectToWebrootUrl("general-report.php");
