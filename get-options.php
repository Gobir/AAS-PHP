<?php 
session_start();
require 'config.php';
require 'functions.php';
isAdminLoggedin();
isPost("get-options.php");
$userEmail = $_POST["users"];
//Do Validation here
$dbh = mf_connect_db();
$selectQuery = "SELECT * FROM absent_leave WHERE email = ?";
$sth = mf_do_query($selectQuery, array($userEmail), $dbh);
$row = mf_do_fetch_result($sth);
if ($row !== false) {
    echo json_encode($row, true);
}else{
    echo "";
}
