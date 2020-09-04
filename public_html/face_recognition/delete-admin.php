<?php

session_start();
require 'config.php';
require 'functions.php';
isSuperUserLoggedin();
/*
 * ---------------------------------------------------------------
 * delete-user.php
 * ---------------------------------------------------------------
 * This file is called via AJAX and used to delete an admin from the grid of admins in admins.php.
 */

//If the script is called via AJAX.
isAjax("super-user.php");
$dbh = mf_connect_db();
//Converts the data sent from jQuery AJAX to this script to an array.
$data = json_decode($_POST['data'], true);
//Checks if the admin to delete exists in the "admins" table by checking its id sent.
$querySelect = "SELECT * FROM admin WHERE id = ?";
$sth = mf_do_query($querySelect, array($data), $dbh);
$rows = mf_do_fetch_result($sth);
$result = [];
if ($rows !== false) {
    $result = $rows;
}
if (sizeof($result) === 0) {
    //Case where user id does not exist in "users" table.
    $_SESSION['detele_error'] = "An error occured! Try again please.";
} else {
    //Deletes the user from "users" table when it is found.
    $queryDelete = "DELETE FROM admin WHERE id = ?;";
    mf_do_query($queryDelete, array($data), $dbh);
    $_SESSION['detele_success'] = "Admin successfully deleted!";
}
      
