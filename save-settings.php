<?php

session_start();
require 'config.php';
require 'functions.php';
isAdminLoggedinOrSupperUserLogin();
/*
 * ---------------------------------------------------------------
 * save-settings.php
 * ---------------------------------------------------------------
 * This file is called via POST and used to save the login access and email settings for the admin.
 */
$dbh = mf_connect_db();
$id = $_SESSION["id"];
//If login access for is submitted
if (isset($_POST["login_credentials"])) {
    //Array to store email and password processing errors.
    $credentialsSettingsErrors = [];
    //Creates POST variables.
    $email = $_POST["email"];
    $oldpassword = $_POST["oldpassword"];
    $newpassword = $_POST["newpassword"];
    $cnewpassword = $_POST["cnewpassword"];
    $updateEmail = false;
    $updatePassword = false;
    //Validates the login email submitted.
    if (!isEmailValid($email)) {
        //Case where the email is not valid.
        array_push($credentialsSettingsErrors, "-Invalid email address.");
    } else if (isFieldTaken($email, "email", "admin", $id)) {
        //Case where the email entered belongs to another admin or superuser.
        array_push($credentialsSettingsErrors, "-Email address is already taken by another admin user.");
    } else {
        //Email entered is valid.
        $updateEmail = true;
    }
    //Case where password needs to be updated (filled them on submission).
    if (!empty($oldpassword) || !empty($newpassword) || !empty($cnewpassword)) {
        $querySelect = "SELECT password FROM admin WHERE id = ?";
        $sth = mf_do_query($querySelect, array($id), $dbh);
        $result = mf_do_fetch_result($sth);
        if ($result !== false) {
            //Verifies if the encrypted password saved in database, matches with the encrypted admin password provided.
            if (!password_verify($oldpassword, $result["password"])) {
                //Case where they do not match. 
                //Adds the error message to the email and passwords errors array.
                array_push($credentialsSettingsErrors, "-Old Password and the value in the database do not match.");
            } else if (!isPasswordValid($newpassword)) {
                //Case where new password entered is not valid.
                //Adds the error message to the email and passwords errors array.
                array_push($credentialsSettingsErrors, "-New Password should not be less than 6 and more than 12 in length.");
            } else if (strcmp($newpassword, $cnewpassword) !== 0) {
                //Case where new password entered and confirmed new password entered do not match.
                //Adds the error message to the email and passwords errors array.
                array_push($credentialsSettingsErrors, "-Password and Confirm Password do not match.");
            } else {
                //Password entered is valid.
                $updatePassword = true;
            }
        }
    }
    //If the errors array is not empty (errors exist).
    if (!empty($credentialsSettingsErrors)) {
        //Sets a session with a failure message.
        $_SESSION["login_cred_error"] = implode("<br>", $credentialsSettingsErrors);
        //Creates sessions to display the POST form data sent by the admin and avoid reentering them.
        createPostSession(array(
            "email" => $email,
            "oldpassword" => $oldpassword,
            "newpassword" => $newpassword,
            "cnewpassword" => $cnewpassword
        ));
    } else {
        if ($updateEmail) {
            //Case where the email is valid.
            //Updates the admin or superuser email.
            $updateQuery = "UPDATE admin SET email = ? WHERE id = ?;";
            mf_do_query($updateQuery, array($email, $id), $dbh);
            //Updates admin email in session.
            $_SESSION['admin_email'] = $email;
            //Creates a success session message.
            $_SESSION["login_cred_success"] = "Login credentials saved successfully!";
        }
        if ($updatePassword) {
            //Case where all passwords are OK.
            //Updates the admin or superuser password field in "admin" table with the new one set.
            $updateQuery = "UPDATE admin SET password = ? WHERE id = ?;";
            mf_do_query($updateQuery, array(password_hash($newpassword, PASSWORD_DEFAULT), $id), $dbh);
            //Sets a session with a success message.
            $_SESSION["login_cred_success"] = "Login credentials saved successfully!";
        }
    }
} else if (isset($_POST["gps"])) {
    $gpsSettingsErrors = [];
    $latitude = $_POST["officeLatitude"];
    $longitude = $_POST["officeLongitude"];
    $limit = $_POST["officePremises"];
    if (!isValidLatLng($latitude, $longitude)) {
        array_push($gpsSettingsErrors, "-Latitude or Longitude is wrong.");
    }
    if (!is_numeric($limit)) {
        array_push($gpsSettingsErrors, "-The premises distance is not valid.");
    }
    if (!empty($gpsSettingsErrors)) {
        //Sets a session with a failure message.
        $_SESSION["office_sett_error"] = implode("<br>", $gpsSettingsErrors);
        //Creates sessions to display the POST form data sent by the admin and avoid reentering them.
        createPostSession(array(
            "officeLatitude" => $latitude,
            "officeLongitude" => $longitude,
            "officePremises" => $limit
        ));
    } else {
        $updateQuery = "UPDATE admin SET latitude = ?, longitude = ?, premises = ? WHERE id = ?;";
        mf_do_query($updateQuery, array($latitude, $longitude, $limit, $id), $dbh);
        //Sets a session with a success message.
        $_SESSION["office_sett_success"] = "Office Position saved successfully!";
    }
} else if (isset($_POST["paypal"])) {
    $paypalErrors = [];
    $sandboxClientId = $_POST["sandboxClientId"];
    $sandboxSecretId = $_POST["sandboxSecretId"];
    $liveboxClientId = $_POST["liveboxClientId"];
    $liveboxSecretId = $_POST["liveboxSecretId"];
    if (isset($_POST["switch_box"])) {
        $switchBox = 'checked="checked"';
    } else {
        $switchBox = "";
    }
    if (empty($sandboxClientId)) {
        array_push($paypalErrors, "-Sand Box Client ID is required.");
    }
    if (empty($sandboxSecretId)) {
        array_push($paypalErrors, "-Sand Box Secret ID is required");
    }
    if (empty($liveboxClientId)) {
        array_push($paypalErrors, "-Live Box Client ID is required.");
    }
    if (empty($liveboxSecretId)) {
        array_push($paypalErrors, "-Live Box Secret ID is required.");
    }
    if (!empty($paypalErrors)) {
        //Sets a session with a failure message.
        $_SESSION["paypal_cred_error"] = implode("<br>", $paypalErrors);
        //Creates sessions to display the POST form data sent by the admin and avoid reentering them.
        createPostSession(array(
            "sandboxClientId" => $sandboxClientId,
            "sandboxSecretId" => $sandboxSecretId,
            "liveboxClientId" => $liveboxClientId,
            "liveboxSecretId" => $liveboxSecretId,
            "switch_box" => $switchBox
        ));
    } else {
        //Save PayPal API keys
        $queryDel = "DELETE FROM paypal_keys";
        mf_do_query($queryDel, array(), $dbh);
        $insertQuery = "INSERT INTO paypal_keys (sandbox_client_id, sandbox_secret_id, livebox_client_id, livebox_secret_id, status) VALUES (?, ?, ?, ?, ?);";
        $dbh = mf_connect_db();
        mf_do_query($insertQuery, array($sandboxClientId, $sandboxSecretId, $liveboxClientId, $liveboxSecretId, $switchBox), $dbh);
        $insertId = (int) $dbh->lastInsertId();
        if (is_int($insertId)) {
            //Creates a success session message.    
            $_SESSION["paypal_cred_success"] = "PayPal keys successfully saved!";
        } else {
            //Creates a success session message.
            $_SESSION["paypal_cred_error"] = "PayPal keys could not be saved!";
        }
    }
}
if ($_SESSION['superuser']) {
    redirectToWebrootUrl("super-user.php");
} else {
    redirectToWebrootUrl("settings.php");
}


