<?php
session_start();
require 'config.php';
require 'functions.php';
isAdminLoggedin();
isPost("save-options.php");
$sick_days = $_POST["sick_days"];
$leave_days = $_POST["leave_days"];
$userEmail = $_POST["users"];
$start_time = $_POST["start_time"];
$end_time = $_POST["end_time"];
$deduction_hour = $_POST["deduction_hour"];
$basic_salary = $_POST["basic_salary"];
$bonus = $_POST["bonus"];
$currency = $_POST["currency"];
$tax = $_POST["tax"];
$pension = $_POST["pension"];
//Do Validation here
$dbh = mf_connect_db();
$deleteQuery = "DELETE FROM absent_leave WHERE email = ?";
mf_do_query($deleteQuery, array($userEmail), $dbh);
$insertQuery = "INSERT INTO absent_leave (sick_days, leave_days, normal_start_time, normal_end_time, deduction_hour, basic_salary, bonus, currency, tax, pension, email) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
mf_do_query($insertQuery, array($sick_days, $leave_days, $start_time, $end_time, $deduction_hour, $basic_salary, $bonus, $currency, bcdiv($tax, "100", 4), bcdiv($pension, "100", 4), $userEmail), $dbh);
$insertId = (int) $dbh->lastInsertId();
if (is_integer($insertId)) {
    echo "Options successfully saved!";
} else {
    echo "An error occurred! Try again please.";
}


