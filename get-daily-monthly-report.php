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

isPost("get-daily-monthly-report.php");
//Creates POST and SESSION variables.
$exp = explode("-", $_POST["users"]); 
$userEmail = $exp[0];
$userName = $exp[1];
$datepicker = $_POST["user_datepicker"];
$dbh = mf_connect_db();
$selectQuery = "SELECT * FROM absent_leave WHERE email = ?";
$sth = mf_do_query($selectQuery, array($userEmail), $dbh);
$row = mf_do_fetch_result($sth);
$sick_days_array = [];
$leave_days_array = [];
if ($row !== false) {
    $sick_days_array = explode(",", $row["sick_days"]);
    $leave_days_array = explode(",", $row["leave_days"]);
}
//Do Validation here
$expDate = explode("-", $datepicker);
$_SESSION["user_datepicker"] = $datepicker;
$_SESSION["user_email"] = $userEmail;
$_SESSION["user_fullname"] = $userName;
$_SESSION["sick_days_array"] = excludeDates($sick_days_array, $expDate[0], $expDate[1]);
$_SESSION["leave_days_array"] = excludeDates($leave_days_array, $expDate[0], $expDate[1]);
$email = $_SESSION['admin_email'];
$reports = [];
$dates = getDatesForGivenMonth($expDate[0], $expDate[1]);
$absent = 0;
$present = 0;
foreach ($dates as $date) {
    $query = "SELECT * FROM tracking WHERE action IN (?, ?) AND email = ? AND date = ? ORDER BY id ASC";
    $sth = mf_do_query($query, array("Check In", "Check Out", $userEmail, $date), $dbh);
    $rows = mf_do_fetch_results($sth);
    if (empty($rows) && !in_array($date, $sick_days_array) && !in_array($date, $leave_days_array)) {
        $absent++;
    } else if (!empty($rows) && !in_array($date, $sick_days_array) && !in_array($date, $leave_days_array)) {
        $present++;
    }
    $inFound = false;
    $outFound = false;
    $tracking = [];
    $timeIn = "";
    $timeOut = "";
    $total = 0;
    $inOut = [];
    $reports[$date] = [];
    foreach ($rows as $row) {
        if ($row["action"] == "Check In") {
            $timeIn = $row["time"];
            array_push($inOut, str_replace("/", "-", $row["date"]) . " " . $timeIn);
        }
        if ($row["action"] == "Check Out") {
            $timeOut = $row["time"];
            array_push($inOut, str_replace("/", "-", $row["date"]) . " " . $timeOut);
        }
        if (sizeof($inOut) == 2) {
            $diff = getSecondsDiff($inOut[0], $inOut[1]);
            array_push($reports[$date], [$timeIn, $timeOut, bcdiv((string) $diff, "3600", 2)]);
            $inOut = [];
        }
    }
}
$_SESSION["user_reports"] = $reports;
$_SESSION["absent"] = $absent;
$_SESSION["present"] = $present;
redirectToWebrootUrl("daily-monthly-report.php");
