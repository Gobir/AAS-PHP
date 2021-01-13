<?php

session_start();
/*
 * ---------------------------------------------------------------
 * authentication.php
 * ---------------------------------------------------------------
 * This file is called via POST and used to authenticate the admin.
 */
require 'config.php';
require 'functions.php';
isPost("login.php");
//Creates POST variables
$email = $_POST["email"];
$password = $_POST["password"];
$dbh = mf_connect_db();
//Validates the email submitted.
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['login_failed'] = "Invalid email or password!";
} else {
    //Gets the admin encrypted stored password from the "admin" table. 
    $selectQuery = "SELECT id, email, password, role FROM admin WHERE email = ?";
    $params = array($email);
    $sth = mf_do_query($selectQuery, $params, $dbh);
    $result = mf_do_fetch_result($sth);
    if (!$result) {
        //Case where the admin encrypted stored password is not found. 
        $_SESSION['login_failed'] = "Invalid email or password!";
    } else {
        //Varifies the admin encrypted stored password against the password submitted. 
        if (password_verify($password, $result["password"])) {
            //Creates sessions in case passwords match.
            $_SESSION['id'] = $result["id"];
            if ($result["role"] == "superuser") {
                //SuperUser case
                $_SESSION['superuser_email'] = $email;
                $_SESSION['superuser'] = true;
                redirectToWebrootUrl("super-user.php");
                exit();
            } else {
                //Admin Case
                $_SESSION['admin_email'] = $email;
                $_SESSION['admin'] = true;
                $_SESSION["coordinates"] = json_encode(array());
                $_SESSION["activeDates"] = array();
                $_SESSION["info"] = array();
                redirectToWebrootUrl("settings.php");
                exit();
            }
        } else {
            //Creates sessions error message in case passwords do not match.
            $_SESSION['login_failed'] = "Invalid email or password!";
        }
    }
}
redirectToWebrootUrl("login.php");

