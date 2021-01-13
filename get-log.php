<?php
session_start();
require 'config.php';
require 'functions.php';
isAdminLoggedin();
/*
 * ---------------------------------------------------------------
 * get-log.php
 * ---------------------------------------------------------------
 * This file is called via AJAX and used to display the TrackMe mobile application crashes or errors logs.
 */
isAjax("mobile-app-logs.php");
//Creats POST variables and SESSIONS
$email = $_POST["email"];
$datepicker = $_POST["datepicker"];
$_SESSION["email"] = $email;
$_SESSION["logdatepicker"] = $datepicker;
$dbh = mf_connect_db();
//Gets all saved logs from "logs" table for a particular user email and date.
$query = "SELECT data, date, time, timezone, errorType FROM app_errors WHERE email = ? AND date = ?";
$sth = mf_do_query($query, array($email, $datepicker), $dbh);
$rows = mf_do_fetch_results($sth);
$results = [];
if ($rows !== false) {
    $results = $rows;
}
$logs = "";
if (sizeof($results) !== 0) {
    foreach ($results as $result) {
        //Applies a formating to each log found for displaying purpose. 
        $logs .= "[" . $result["errorType"] . "] " . $result["date"] . " " . $result["time"] . " " . $result["timezone"] . "\n" . $result["data"] . "\n";
    }
}
//Creates session variable that holds all logs.
$_SESSION["Logs"] = $logs;
//Gets all saved dates from "logs" table for a particular email without duplicates.

$queryDate = "SELECT DISTINCT date from app_errors WHERE email = ?;";
$sthDates = mf_do_query($queryDate, array($email), $dbh);
$rowsDates = mf_do_fetch_results($sthDates);
$dates = [];
if ($rowsDates !== false) {
    $dates = $rowsDates;
}
$activeDates = array();
foreach ($dates as $date) {
    array_push($activeDates, $date->date);
}
//Active logs dates to highlight in the calander in track-user page.
$_SESSION["activeLogDates"] = json_encode($activeDates);
redirectToWebrootUrl("mobile-app-logs.php");
