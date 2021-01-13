<?php

session_start();
require 'config.php';
require 'functions.php';
isAdminLoggedinOrSupperUserLogin();
/*
 * ---------------------------------------------------------------
 * request-email.php
 * ---------------------------------------------------------------
 * This file is called via AJAX and used to send TrackMe mobile application credentials access the the required user by email.
 */
//If the script is called via AJAX.
isAjax("users.php");
//Converts the data sent from jQuery AJAX to this script to an array.
$data = json_decode($_POST["data"], true);
//Checks if the user exists in the "users" table by checking its id sent.
$query = "SELECT fullname, email FROM admin WHERE id = ?";
$dbh = mf_connect_db();
$sth = mf_do_query($query, array($data), $dbh);
$row = mf_do_fetch_result($sth);
if ($row !== false) {
    //Generates a random unique 16 characters string.
    $password = generateRandomToken(4);
    //Update admin record with the generated password hashed in the "admin" table.
    $query = "UPDATE admin SET password = ? WHERE id = ?";
    mf_do_query($query, array(password_hash($password, PASSWORD_DEFAULT), $data), $dbh);
    //Email body for admin credentials access.
    $emailBody = "<p>Dear <b>" . $row["fullname"] . "</b>,</p><p>Here are your Admin access credentials.</p> 
            <p>Email: <b>" . $row["email"] . "</b></p>
            <p>Password: <b>" . $password . "</b></p>
            <hr>";
    //Adds the email body to the email template and sends the email to the requested user.
    $status = sendMail($row["email"], "Your Admin Access Credentials", getEmailTemplate($emailBody));
    //Creates sessions on email submission success or failure.
    if ($status) {
        $_SESSION['mail_send_success'] = "Admin access credentials sent the the selected user.";
    } else {
        $_SESSION['mail_send_failed'] = "An error occured! Try again please.";
    }
} else {
    //Case where user id does not exist in "users" table.
    $_SESSION['mail_send_failed'] = "An error occured! Try agian please.";
}