<?php
session_start();
require 'config.php';
require 'functions.php';
/*
 * ---------------------------------------------------------------
 * save-reset-password.php
 * ---------------------------------------------------------------
 * This file is called via POST and used to save the new password set by the admin.
 */
isPost("login.php");
//Creates POST variables.
$password = $_POST["password"];
$cpassword = $_POST["cpassword"];
$token = $_POST["token"];
//Validates the password set the admin
if (!isPasswordValid($password)) {
    //Case where pasword is not valid.
    $_SESSION['reset_failed'] = "-Password should not be less than 6 and more than 12 in length.";
    redirectToWebrootUrl("reset-password.php?token=" . $token);
} else if (strcmp($password, $cpassword) !== 0) {
    //Case where pasword is not valid.
    $_SESSION['reset_failed'] = "-Password and Confirm Password do not match.";
    redirectToWebrootUrl("reset-password.php?token=" . $token);
} else {
    $dbh = mf_connect_db();
    //Case where pasword is valid.
    //Checks if the reset password token exists.
    $query = "SELECT email FROM admin WHERE recovery_token = ?;";
    $sth = mf_do_query($query, array($token), $dbh);
    $row = mf_do_fetch_result($sth);
    $result = [];
    if ($row !== false) {
        $result = $row;
    }
    if (sizeof($result) === 0) {
        //Case where the reset password token doesn't exist.
        $_SESSION['reset_failed'] = "Invalid Password Reset Token!";
        redirectToWebrootUrl("reset-password?token=" . $token);
    } else {
        //Case where the reset password token exists.
        //Updates the admin password field in "admin" table with the new one set.
        $updateQuery = "UPDATE admin SET password = ?, used_token = ? WHERE recovery_token = ?";    
        mf_do_query($updateQuery, array(password_hash($password, PASSWORD_DEFAULT), 'Y', $token), $dbh);
        //Creates the password reset success email body.
        $emailBody = "<p>Hi <b>" . $result["email"] . "</b>,
                </p><p>Your new password was successfully set!</p> 
                <p>You can login with your new credentials.</p>";
        //Adds the email body to the email template and sends the email to admin.
        $status = sendMail($result["email"], "Reset Password", getEmailTemplate($emailBody));
        //Creates sessions on email submission success or failure.
        if ($status) {
            $_SESSION['reset_success'] = "Your new password was successfully set! You can login with your new credentials.";
            redirectToWebrootUrl("login.php");
        } else {
            $_SESSION['reset_failed'] = "An error occured! Try again please.";
            redirectToWebrootUrl("reset-password.php?token=" . $token);
        }
    }
}

