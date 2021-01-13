<?php

session_start();
require 'config.php';
require 'functions.php';
isSuperUserLoggedin();
/*
 * ---------------------------------------------------------------
 * update-user.php
 * ---------------------------------------------------------------
 * This file is called via POST and used to update an admin created in the page admins.php.
 */
isPost("admins.php");
$query = "SELECT * FROM admin";
$dbh = mf_connect_db();
$sth = mf_do_query($query, array(), $dbh);
$rows = mf_do_fetch_results($sth);
$results = [];
if ($rows !== false) {
    $results = $rows;
}
//Gets the id of the admin to update.
$id = $_POST["row_id"];
//Checks if the admin to update exists or not.
if (sizeof($results) === 0) {
    //Admin to update does not exist.
    $_SESSION["edit_error"] = "An error occured! Try again please.";
} else {
    //Creates POST variables.
    $fullName = $_POST["fullname"];
    $email = $_POST["email"];
    $maxUsers = $_POST["maxUsers"];
    $priceUser = $_POST["priceUser"];
    //Array to hold errors.
    $errors = [];
    //Validates fullname.
    if (!isFullNameValid($fullName)) {
        //Adds the error message to the errors array.
        array_push($errors, "-Full Name should only contain letters and spaces.");
    }
    //Validates email.
    if (!isEmailValid($email)) {
        //Adds the error message to the errors array.
        array_push($errors, "-Invalid email address.");
        //Validates if email is already used.
    } else if (isFieldTaken($email, "email", "admin", $id)) {
        //Adds the error message to the errors array.
        array_push($errors, "-Email already used by another admin.");
    }
    //Validates admin user quota.
    if (!ctype_digit($maxUsers)) {
        //Adds the error message to the errors array.
        array_push($errors, "-Maximum number of users must be a digit.");
    } else if ($maxUsers <= 0) {
        //Adds the error message to the errors array.
        array_push($errors, "-Maximum number of users must be a greater than 0.");
    }
    //Validate user price
    if (!is_numeric($priceUser)) {
        //Adds the error message to the errors array.
        array_push($errors, "-Price per user must be a valid.");
    }
    //Checks if validation errors exist.
    if (!empty($errors)) {
        //Creates sessions to display the POST form data sent and avoid reentering them.
        $_SESSION["edit_error"] = implode("<br>", $errors);
        createPostSession(array(
            "fullname" => $fullName,
            "email" => $email,
            "maxUsers" => $maxUsers,
            "priceUser" => $priceUser
        ));
    } else {
        //Updates the admin in "admins" table.
        $updateQuery = "UPDATE admin SET fullname = ?, email = ?, admin_quota = ?, user_price = ? WHERE id = ?;";
        mf_do_query($updateQuery, array($fullName, $email, $maxUsers, $priceUser, $id), $dbh);
        //Creates a success session message.
        $_SESSION["edit_success"] = "Admin updated successfully!";
    }
}
redirectToWebrootUrl("edit-admin.php?id=" . $id);
