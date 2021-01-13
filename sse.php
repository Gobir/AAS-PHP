<?php

session_start();
require 'config.php';
require 'functions.php';
isAdminLoggedin();
/*
 * ---------------------------------------------------------------
 * sse.php
 * ---------------------------------------------------------------
 * Used to make a server sent event requests to get the tracking data from get-tracking-data.php to track the user in real time.
 */

//Sets headers required for server sent events requests.
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
//Gets the tracking data from "tracking" table for a selected user.
$dbh = mf_connect_db();
$queryTracking = "SELECT id, latitude, longitude, time, date, timezone FROM tracking WHERE email = ? AND date = ? ORDER BY timestamp ASC";
$sthTracking = mf_do_query($queryTracking, array($_SESSION["email"], $_SESSION["datepicker"]), $dbh);
$trackingData = mf_do_fetch_results($sthTracking);
if ($trackingData !== false) {
    //Case where tracking data was found. 
    foreach ($trackingData as $data) {
        $actualloc[] = array($data["time"], (float) $data["latitude"], (float) $data["longitude"], $data["date"], $data["timezone"], $data["id"]);
        //Creates a session with tracking data found as JSON.
        $_SESSION["coordinates"] = json_encode($actualloc, JSON_NUMERIC_CHECK);
    }
} else {
    //Case where no tracking data was found. 
    $_SESSION["coordinates"] = json_encode(array());
}
//Send the tracking data to the browser.
echo "data: {$_SESSION["coordinates"]}\n\n";
flush();
