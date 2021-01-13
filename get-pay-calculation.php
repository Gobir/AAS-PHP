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

isPost("get-pay-calculation.php");
//Creates POST and SESSION variables.
$exp = explode("-", $_POST["users"]);
$userEmail = $exp[0];
$userName = $exp[1];
$dbh = mf_connect_db();
$selectQuery = "SELECT * FROM absent_leave WHERE email = ?";
$sth = mf_do_query($selectQuery, array($userEmail), $dbh);
$data = mf_do_fetch_result($sth);
$_SESSION["user_email"] = $userEmail;
$_SESSION["user_fullname"] = $userName;
$reports = [];
$datepicker = $_POST["user_datepicker"];
$expDate = explode("-", $datepicker);
$_SESSION["user_datepicker"] = $datepicker;
$dates = getDatesForGivenMonth($expDate[0], $expDate[1]);
$absent = 0;
$present = 0;
$totalHours = 0;
$totMissedHours = 0;
$totOverWorkedHours = 0;
foreach ($dates as $date) {
    $query = "SELECT * FROM tracking WHERE action IN (?, ?) AND email = ? AND date = ? ORDER BY id ASC";
    $sth = mf_do_query($query, array("Check In", "Check Out", $userEmail, $date), $dbh);
    $rows = mf_do_fetch_results($sth);
    $timeIn = "";
    $timeOut = "";
    $totalDay = 0;
    $inOut = [];
    $missedHours = 0;
    $overWorkedHours = 0;
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
            $totalDay = bcadd($totalDay, bcdiv((string) $diff, "3600", 2), 2);
            $inOut = [];
        }
    }
    $subHours = bcsub("8", $totalDay, 2);
    if ($subHours > 0) {
        $missedHours = $subHours;
    } else if ($subHours < 0) {
        $overWorkedHours = $subHours;
    }

    $totMissedHours = bcadd($totMissedHours, $missedHours, 2);
    $totOverWorkedHours = bcadd($totOverWorkedHours, $overWorkedHours, 2);
    $totalHours = bcadd($totalHours, $totalDay, 2);
}
$dayMissedDeduction = bcmul($totMissedHours, $data["deduction_hour"], 2);
$salary = bcsub($data["basic_salary"], $dayMissedDeduction, 2);
$tax = bcmul($salary, $data["tax"], 2);
$pension = bcmul($salary, $data["pension"], 2);
$salaryAfterTax = bcsub($salary, $tax, 2);
$salaryAfterPension = bcsub($salaryAfterTax, $pension, 2);
$bonus = bcmul($totOverWorkedHours, $data["deduction_hour"], 2);
$totalPay = bcadd($bonus, $salaryAfterPension, 2);
array_push($reports,
        array(
            "missHours" => $totMissedHours,
            "overWorkedHours" => $totOverWorkedHours,
            "totalHours" => $totalHours,
            "dayMissedDeduction" => $dayMissedDeduction,
            "basicSalary" => $data["basic_salary"],
            "tax" => $tax,
            "pension" => $pension,
            "deduction_hour" => $data["deduction_hour"],
            "currency" => $data["currency"],
            "net" => $salaryAfterPension,
            "bonus" => $bonus,
            "totalPay" => $totalPay
));
$_SESSION["pay_reports"] = $reports;
redirectToWebrootUrl("pay-calculation.php");
