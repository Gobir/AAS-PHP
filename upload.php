<?php
session_start();
$target_dir = "images/users/";
$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
$uploadOk = 1;
$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

// Check if image file is a actual image or fake image
if(isset($_POST["submit"])) {
  $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
  if($check !== false) {
    $_SESSION["response"] = "File is an image - " . $check["mime"] . ".";
    $uploadOk = 1;
  } else {
    $_SESSION["response"]  = "File is not an image.";
    $uploadOk = 0;
  }
}

// Check if file already exists
if (file_exists($target_file)) {
  $_SESSION["response"] = "Sorry, file already exists.";
  $uploadOk = 0;
}

// Check file size
if ($_FILES["fileToUpload"]["size"] > 500000) {
  $_SESSION["response"] = "Sorry, your file is too large.";
  $uploadOk = 0;
}

// Allow certain file formats
if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
&& $imageFileType != "gif" ) {
  $_SESSION["response"] = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
  $uploadOk = 0;
}

// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
  $_SESSION["response"] = "Sorry, your file was not uploaded.";
// if everything is ok, try to upload file
} else {
  if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
    $_SESSION["response"] = "The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.";
  } else {
    $_SESSION["response"] = "Sorry, there was an error uploading your file.";
  }
}
header("Location: https://vmi338910.contaboserver.net/~devouss/face_recognition/uploader.php");
exit();
