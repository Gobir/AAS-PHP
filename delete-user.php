<?php

session_start();
require 'config.php';
require 'functions.php';
isAdminLoggedin();
/*
 * ---------------------------------------------------------------
 * delete-user.php
 * ---------------------------------------------------------------
 * This file is called via AJAX and used to delete a user from the grid of users in users.php.
 */

//If the script is called via AJAX.
isAjax("users.php");
$dbh = mf_connect_db();
//Converts the data sent from jQuery AJAX to this script to an array.
$data = json_decode($_POST['data'], true);
//Checks if the user to delete exists in the "users" table by checking its id sent.
$querySelect = "SELECT * FROM users WHERE id = ?";
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
    $queryDelete = "DELETE FROM users WHERE id = ?;";
    mf_do_query($queryDelete, array($data), $dbh);
    //Removes the image profile for the deleted user from the profiles images folder.
    unlink(getUploadDirectory() . $result["profile_img"]);
    //Removes previous session created for the current deleted user.
    if (isset($_SESSION['info']) && !empty($_SESSION['info']) && !is_null($_SESSION['info'])) {
        if ($_SESSION['info'][0]->id == $data) {
            $_SESSION['info'] = [];
            $_SESSION["coordinates"] = json_encode([]);
            unset($_SESSION["datepicker"]);
        }
    }
    $_SESSION['detele_success'] = "User successfully deleted!";
}
      
