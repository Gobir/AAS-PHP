<?php

session_start();
require 'config.php';
require 'functions.php';
isAdminLoggedin();
/*
 * ---------------------------------------------------------------
 * update-user.php
 * ---------------------------------------------------------------
 * This file is called via POST and used to update a user created in the page list-users.php.
 */
isPost("users.php");
$query = "SELECT * FROM users WHERE created_by = ?";
$dbh = mf_connect_db();
$sth = mf_do_query($query, array($_SESSION["admin_email"]), $dbh);
$rows = mf_do_fetch_results($sth);
$results = [];
if ($rows !== false) {
    $results = $rows;
}
//Gets the id of the user to update.
$id = $_POST["row_id"];
//Checks if the users to update exists or not.
if (sizeof($results) === 0) {
    //User to update does not exist.
    $_SESSION["edit_error"] = "An error occured! Try again please.";
} else {
    //Creates POST variables.
    $fullName = $_POST["fullname"];
    $email = $_POST["email"];
    //Creates FILES variable.
    $file = $_FILES;
    //Minimum size allowed for an uploaded image.
    $minSizeW = 400;
    $minSizeH = 400;
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
    } else if (isFieldTaken($email, "email", "users", $id)) {
        //Adds the error message to the errors array.
        array_push($errors, "-Email already used.");
    }
    //Validate tracking_time_interval.
    if (!isset($_POST["tracking_time_interval"])) {
        //Adds the error message to the errors array.
        array_push($errors, "-No tracking time interval selected.");
    } else if (!in_array($_POST["tracking_time_interval"], $allowedTimeTrackingValues)) {
        //Adds the error message to the errors array.
        array_push($errors, "-Tracking time interval selected is not valid.");
    }
    //Checks if validation errors exist.
    if (!empty($errors)) {
        //Creates sessions to display the POST form data sent and avoid reentering them.
        $_SESSION["edit_error"] = implode("<br>", $errors);
        if (isset($_POST["tracking_time_interval"])) {
            $_SESSION["tracking_time_interval"] = $_POST["tracking_time_interval"];
        }
        createPostSession(array(
            "fullname" => $fullName,
            "email" => $email
        ));
    } else {
        if ($file['file']['error'] === 4) {
            //No image uploaded. Keep the previous image profile.
            //Updates the user in "users" table.
            $updateQuery = "UPDATE users SET fullname = ?, email = ?, tracking_time_interval = ? WHERE id = ?;";
            mf_do_query($updateQuery, array($fullName, $email, $_POST["tracking_time_interval"], $id), $dbh);
            //Creates a success session message.
            $_SESSION["edit_success"] = "User updated successfully!";
        } else {
            //If a user image profile is uploaded.
            //Gets the file name of the image.
            $fileName = $_FILES['file']['name'];
            //Gets the location of the uploaded image.
            $filePath = $_FILES['file']['tmp_name'];
            //Gets the extension of the uploaded image.
            $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            //Checks if the uploaded image type and mime are valid.
            if (!in_array($_FILES["file"]["type"], $allowedMimesType) || !in_array($extension, $allowedExts)) {
                //Case where type and mime are not valid. 
                //Creates sessions to display the POST form data sent and avoid reentering them.
                $_SESSION['edit_error'] = "-Only 'jpg' images are allowed to be uploaded!";
                if (isset($_POST["tracking_time_interval"])) {
                    $_SESSION["tracking_time_interval"] = $_POST["tracking_time_interval"];
                }
                createPostSession(array(
                    "fullname" => $fullName,
                    "email" => $email
                ));
            } else {
                //Case where type and mime are valid.
                //Case where errors occured during the upload.
                if (!empty($errors)) {
                    //Creates sessions to display the POST form data sent and avoid reentering them.
                    $_SESSION["edit_error"] = implode("<br>", $errors);
                    if (isset($_POST["tracking_time_interval"])) {
                        $_SESSION["tracking_time_interval"] = $_POST["tracking_time_interval"];
                    }
                    createPostSession(array(
                        "fullname" => $fullName,
                        "email" => $email
                    ));
                } else {
                    //Case where no errors occured during the upload.
                    processUploadedImage($filePath, $minSizeW, $minSizeH, $extension, "U", $fullName, $email, $id);
                }
            }
        }
    }
}
redirectToWebrootUrl("edit-user.php?id=" . $id);
