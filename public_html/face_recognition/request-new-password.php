<?php

session_start();
require 'config.php';
require 'functions.php';
/*
 * ---------------------------------------------------------------
 * request-new-password.php
 * ---------------------------------------------------------------
 * This file is called via POST and used to send by email a password recovery link to admin.
 */
isPost("forgot-password.php");
//Creates POST variable.
$email = $_POST["email"];
//Validates the email submitted.
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    //Case where email submitted is not valid.
    $_SESSION['recover_failed'] = "Invalid email!";
} else {
    $dbh = mf_connect_db();
    //Case where email submitted is valid, checks if it is registered in the "admin table.
    $query = "SELECT id FROM admin WHERE email = ?;";
    $sth = mf_do_query($query, array($email), $dbh);
    $rows = mf_do_fetch_result($sth);
    $results = [];
    if ($rows !== false) {
        $results = $rows;
    }
    if (sizeof($results) === 0) {
        //Case where email submitted is not found.
        $_SESSION['recover_failed'] = "Email not found!";
    } else {
        //Case where email submitted is found.
        //Generates a password recovery token. 
        $recoveryToken = generateRandomToken(16);
        //Creates the password recovery URL.
        $link = getWebRootUrl() . 'reset-password.php?token=' . $recoveryToken;
        //Updates the password recovery field with the token in the "admin" 
        //table and sets an expiry time for it of 48 hours.
        $updateQuery = "UPDATE admin SET recovery_token = ?, expiry_token = ?, used_token = ? WHERE id = ?;";
        mf_do_query($updateQuery, array($recoveryToken, strtotime("+48 hours"), 'N', $results["id"]), $dbh);
        //Email body for the password recovery email to send.
        $emailBody = "<p>Hi <b>" . $email . "</b>,
                </p><p>We got a request to reset your admin password.</p> 
                <p>To start the process, please click the following link:</p>
                <p><a href='" . $link . "'>" . $link . "</a></p>
                <p>If the above link does not work, copy and paste the URL in a new browser window. The URL will expire in 48 hours for security reasons. If you did not make this request, simply ignore this message.</p>";
        //Adds the email body to the email template and sends the email to admin.
        $status = sendMail($email, "Password Reset Request", getEmailTemplate($emailBody));
        //Creates sessions on email submission success or failure.
        if ($status) {
            $_SESSION['recover_success'] = "Password recovery link sent to the address email provided!";
        } else {
            $_SESSION['recover_failed'] = "An error occured! Try again please.";
        }
    }
}
redirectToWebrootUrl("forgot-password.php");
