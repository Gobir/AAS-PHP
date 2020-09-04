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

isPost("track-user.php");
//Creates POST and SESSION variables.
$email = $_POST["email"];
$datepicker = $_POST["datepicker"];
$_SESSION["datepicker"] = $datepicker;
$_SESSION["email"] = $email;
$actualloc = array();
$dbh = mf_connect_db();
//Gets the selected user info to display in the track-user page and put them in a session.
$queryInfo = "SELECT id, fullname, email, profile_img, tracking_time_interval FROM users WHERE email = ?";
$sth = mf_do_query($queryInfo, array($email), $dbh);
$results = mf_do_fetch_result($sth);
if ($results !== false) {
    $_SESSION['info'] = $results;
} else {
    $_SESSION['info'] = [];
}

//Gets the selected user info to display in the track-user page and put them in a session.
$queryTracking = "SELECT latitude, longitude, time, date, timezone FROM tracking WHERE email = ? AND date = ? ORDER BY timestamp ASC";
$sthTracking = mf_do_query($queryTracking, array($email, $datepicker), $dbh);
$rows = mf_do_fetch_results($sthTracking);
if ($rows !== false) {
    $trackingData = $rows;
} else {
    $trackingData = [];
}

//Gets the selected user all available tracking dates without duplicates.
$queryDates = "SELECT DISTINCT date FROM tracking WHERE email = ?";
$sthDates = mf_do_query($queryDates, array($email), $dbh);
$rowsDates = mf_do_fetch_results($sthDates);
if ($rowsDates !== false) {
    $dates = $rowsDates;
} else {
    $dates = [];
}

//Sets the active dates to highlight in the calander in track_user page
$activeDates = array();
foreach ($dates as $date) {
    array_push($activeDates, $date->date);
}
$_SESSION["activeDates"] = $activeDates;
//Creates a JSON with all tracking data to show on the map and the textarea in track_user page.
if ($trackingData !== false) {
    foreach ($trackingData as $data) {
        $actualloc[] = array($data["time"], (float) $data["latitude"], (float) $data["longitude"], $data["date"], $data["timezone"], $data["id"]);
    }
    $_SESSION["coordinates"] = json_encode($actualloc);
} else {
    $_SESSION["coordinates"] = json_encode(array());
}
redirectToWebrootUrl("track-user.php");
