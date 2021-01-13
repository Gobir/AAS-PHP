<?php

session_start();
require 'config.php';
require 'functions.php';
isAdminLoggedin();
/*
 * ---------------------------------------------------------------
 * save-user.php
 * ---------------------------------------------------------------
 * This file is called via POST and used to save a user created in the page list-users.php.
 */
isPost("add-user.php");
//Creates POST variables.
$fullName = $_POST["fullname"];
$email = $_POST["email"];
$lighversion = $_POST["lighversion"];
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
//Validates email
if (!isEmailValid($email)) {
    //Adds the error message to the errors array.
    array_push($errors, "-Invalid email address.");
//Validates if email is already used.
} else if (isFieldTaken($email, "email", "users", null)) {
    //Adds the error message to the errors array.
    array_push($errors, "-Email already used.");
}
//Validates tracking_time_interval.
if (!isset($_POST["tracking_time_interval"])) {
    //Adds the error message to the errors array.
    array_push($errors, "-No tracking time interval selected.");
} else if (!in_array($_POST["tracking_time_interval"], $allowedTimeTrackingValues)) {
    //Adds the error message to the errors array.
    array_push($errors, "-Tracking time interval selected is not valid.");
}
if ($file['file']['error'] === 4) {
    //Adds the error message to the errors array.
    array_push($errors, "-User picture is required.");
}
//Check if users limit is reached
if(!userLimitReached($_SESSION["admin_email"])){
    //Adds the error message to the errors array.
    array_push($errors, "-You reached the maximum number of users you can add. Please consider buying more users to increase your limit.");
}
//Checks if validation errors exist.
if (!empty($errors)) {
    //Creates sessions to display the POST form data sent and avoid reentering them.
    $_SESSION["save_error"] = implode("<br>", $errors);
    if (isset($_POST["tracking_time_interval"])) {
        $_SESSION["tracking_time_interval"] = $_POST["tracking_time_interval"];
    }
    createPostSession(array(
        "fullname" => $fullName,
        "email" => $email,
        "lighversion" => $lighversion
    ));
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
        $_SESSION['save_error'] = "-Only 'jpg, jpeg' images are allowed to be uploaded!";
        if (isset($_POST["tracking_time_interval"])) {
            $_SESSION["tracking_time_interval"] = $_POST["tracking_time_interval"];
        }
        createPostSession(array(
            "fullname" => $fullName,
            "email" => $email,
            "lighversion" => $lighversion
        ));
    } else {
        //Case where type and mime are valid.
        //Case where errors occured during the upload.
        if (!empty($errors)) {
            //Creates sessions to display the POST form data sent and avoid reentering them.
            $_SESSION["save_error"] = implode("<br>", $errors);
            if (isset($_POST["tracking_time_interval"])) {
                $_SESSION["tracking_time_interval"] = $_POST["tracking_time_interval"];
            }
            createPostSession(array(
                "fullname" => $fullName,
                "email" => $email,
                "lighversion" => $lighversion
            ));
        } else {
            //Case where no errors occured during the upload.
            processUploadedImage($filePath, $minSizeW, $minSizeH, $extension, "I", $fullName, $email, $lighversion, null);
        }
    }
}
redirectToWebrootUrl("add-user.php");


