<?php

session_start();
require 'config.php';
require 'functions.php';
isSuperUserLoggedin();
/*
 * ---------------------------------------------------------------
 * save-user.php
 * ---------------------------------------------------------------
 * This file is called via POST and used to save a user created in the page list-users.php.
 */
isPost("add-admin.php");
//Creates POST variables.
$fullName = $_POST["fullname"];
$email = $_POST["email"];
$maxUsers = $_POST["maxUsers"];
$priceUser = $_POST["priceUser"];
$errors = [];
//Validates fullname.
if (!isFullNameValid($fullName)) {
    //Adds the error message to the errors array.
    array_push($errors, "-Full Name should only contain letters and spaces.");
}
//Validates email
if (!isEmailValid($email)) {
    //Adds the error message to the errors array.
    array_push($errors, "-Invalid email address.");
//Validates if email is already used.
} else if (isFieldTaken($email, "email", "admin", null)) {
    //Adds the error message to the errors array.
    array_push($errors, "-Email already taken by another admin.");
}
//Validates admin user quota.
if (!ctype_digit($maxUsers)) {
    //Adds the error message to the errors array.
    array_push($errors, "-Maximum number of users must be a digit.");
}else if($maxUsers <= 0){
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
    $_SESSION["save_error"] = implode("<br>", $errors);
    createPostSession(array(
        "fullname" => $fullName,
        "email" => $email,
        "maxUsers" => $maxUsers,
        "priceUser" => $priceUser
    ));
} else {
    //Add admin to admin table
    $dbh = mf_connect_db();
    $query = "INSERT INTO admin (fullname, password, email, admin_quota, user_price) VALUES (?, ?, ?, ?, ?)";
    mf_do_query($query, array($fullName, password_hash(generateRandomToken(4), PASSWORD_DEFAULT), $email, $maxUsers, $priceUser), $dbh);
    $insertAdmin = (int) $dbh->lastInsertId();
    if (is_int($insertAdmin)) {
        //Creates a success session message.    
        $_SESSION["save_success"] = "Admin Created Successfully!";
    } else {
        //Creates a success session message.
        $_SESSION["save_error"] = "Admin Could not be Created!";
    }
}
redirectToWebrootUrl("add-admin.php");


