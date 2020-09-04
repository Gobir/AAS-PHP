<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (array_key_exists('method', $_POST)) {
        $method = $_POST["method"];
        $target_dir = "images/users/";
        $response = array();
        require 'config.php';
        require 'functions.php';
        switch ($method) {
            case "upload":
                if (isset($_FILES["file"])) {
                    $target_name = basename($_FILES["file"]["name"]);
                    $target_file_name = $target_dir . $target_name;
                    // Check if image file is an actual image or fake image  
                    if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file_name)) {
                        correctImageOrientation($target_file_name);
                        $success = true;
                        $message = "Successfully Uploaded";
                    } else {
                        $success = false;
                        $message = "Error while uploading";
                    }
                    $response["success"] = $success;
                    $response["message"] = $message;
                    header('Content-Type: application/json');
                    echo json_encode($response);
                } else {
                    echo 'An error occured! Try again.';
                }
                break;
            case "doFaceRecognition":
                if (isset($_POST['image_name']) && isset($_POST['email'])) {
                    require './faceModel.php';
                    $target_name = $_POST["image_name"];
                    $email = $_POST["email"];
                    $query = "SELECT image_path FROM faces WHERE email = ?";
                    $row = findRecord($email, $query);
                    if ($row !== false) {
                        $root = getcwd() . DIRECTORY_SEPARATOR;
                        $probability = compare($root . $target_dir . $row["image_path"], $root . $target_dir . $target_name);
                        echo $probability;
                    } else {
                        echo "The email entered does not belong to any user!";
                    }
                } else {
                    echo 'An error occured! Try again.';
                }
                break;
            case "checkPosition":
                if (isset($_POST["latitude"]) && isset($_POST["longitude"]) && isset($_POST["email"])) {
                    $latitude = $_POST["latitude"];
                    $longitude = $_POST["longitude"];
                    $email = $_POST["email"];
                    $row = getTrackingInfo($email);
                    $response["timeInterval"] = $row["tracking_time_interval"];
                    $officeInfo = getOfficeInfo($row["created_by"]);
                    $premisesWithin = distance($officeInfo["latitude"], $officeInfo["longitude"], $latitude, $longitude);
                    if ($premisesWithin > $officeInfo["premises"]) {
                        $response["premisesWithin"] = $premisesWithin;
                        $response["result"] = "Failed!";
                    } else {
                        $response["premisesWithin"] = $premisesWithin;
                        $response["result"] = "pass";
                    }
                    header('Content-Type: application/json');
                    echo json_encode($response);
                } else {
                    echo 'An error occured! Try again.';
                }
                break;
            case "verify":
                if (isset($_POST["action"]) && isset($_POST["id"])) {
                    $action = $_POST["action"];
                    $id = $_POST["id"];
                    $query = "SELECT image_path FROM faces WHERE light_version_id = ?";
                    $row = findRecord($id, $query);
                    if ($row !== false) {
                        $result = findRecord($id, "SELECT * FROM light WHERE light_version_id = ? ORDER BY id DESC LIMIT 1");
                        if ($result !== false) {
                            if ($action == "Check In") {
                                if ($result["last_action"] == "IN") {
                                    //If 24H passed do Check In
                                    if (strtotime(date("Y-m-d H:i:s")) > strtotime($result["date_limit"])) {
                                        $response["message"] = "Check Out process started. Please wait...";
                                        $response["status"] = "pass";
                                    } else {
                                        $response["message"] = "Error! You need to Check Out first, before you can attempt to Check In.";
                                        $response["status"] = "Failed!";
                                    }
                                } else if ($result["last_action"] == "OUT") {
                                    $response["message"] = "Check In process started. Please wait...";
                                    $response["status"] = "pass";
                                }
                            }if ($action == "Check Out") {
                                if ($result["last_action"] == "OUT") {
                                    //If 24H passed do Check Out
                                    if (strtotime(date("Y-m-d H:i:s")) > strtotime($result["date_limit"])) {
                                        $response["message"] = "Check In process started. Please wait...";
                                        $response["status"] = "pass";
                                    } else {
                                        $response["message"] = "Error! You need to Check In first, before you can attempt to Check Out.";
                                        $response["status"] = "Failed!";
                                    }
                                } else if ($result["last_action"] == "IN") {
                                    $response["message"] = "Check Out process started. Please wait...";
                                    $response["status"] = "pass";
                                }
                            }
                        } else {
                            //First time.
                            if ($action == "Check Out") {
                                $response["message"] = "Error! You need to Check In first, before you can attempt to Check Out.";
                                $response["status"] = "Failed!";
                            } else if ($action == "Check In") {
                                $response["message"] = "Check In process started. Please wait...";
                                $response["status"] = "pass";
                            } else {
                                $response["message"] = "Error occured! Try again.";
                                $response["status"] = "Failed!";
                            }
                        }
                    } else {
                        $response["message"] = "Error! The id entered does not belong to any user.";
                        $response["status"] = "Failed!";
                    }
                } else {
                    $response["message"] = "Error occured! Try again.";
                    $response["status"] = "Failed!";
                }
                header('Content-Type: application/json');
                echo json_encode($response);
                break;
            case "register":
                if (isset($_POST['image_name']) && isset($_POST['id']) && isset($_POST['action'])) {
                    require './faceModel.php';
                    $target_name = $_POST["image_name"];
                    $id = $_POST["id"];
                    $query = "SELECT image_path FROM faces WHERE light_version_id = ?";
                    $row = findRecord($id, $query);
                    if ($row !== false) {
                        $root = getcwd() . DIRECTORY_SEPARATOR;
                        $probability = compare($root . $target_dir . $row["image_path"], $root . $target_dir . $target_name);
                        if(isJson($probability)){
                            $decodedJson = json_decode($probability, true);
                            if($decodedJson["match"]){
                                doInsertUpdate($id, $_POST['action']);
                            }
                        }
                        echo $probability;
                    } else {
                        echo "Error! The ID entered does not belong to any user!";
                    }
                } else {
                    echo 'Error occured! Try again.';
                }
                break;
            case "savePosition":
                if (isset($_POST["latitude"]) && isset($_POST["longitude"]) &&
                        isset($_POST["email"]) && isset($_POST["date"]) &&
                        isset($_POST["timezone"]) && isset($_POST["time"])) {
                    $latitude = $_POST["latitude"];
                    $longitude = $_POST["longitude"];
                    $email = $_POST["email"];
                    $time = $_POST["time"];
                    $timezone = $_POST["timezone"];
                    $date = $_POST["date"];
                    $insertQuery = "INSERT INTO tracking (email, latitude, longitude, time, date, timestamp, timezone) VALUES (?, ?, ?, ?, ?, ?, ?);";
                    $dbh = mf_connect_db();
                    mf_do_query($insertQuery, array($email, $latitude, $longitude, $time, $date, time(), $timezone), $dbh);
                    $insertId = (int) $dbh->lastInsertId();
                    $row = getTrackingInfo($email);
                    $response["timeInterval"] = $row["tracking_time_interval"];
                    if (is_int($insertId)) {
                        $response["message"] = "GPS position successfully saved!";
                    } else {
                        $response["message"] = "GPS position could not be saved!";
                    }
                    header('Content-Type: application/json');
                    echo json_encode($response);
                } else {
                    echo 'An error occured! Try again.';
                }
                break;
            case "savePositionOffline":
                if (isset($_POST["dataRead"])) {
                    $noEmptyData = array_filter(explode(PHP_EOL, $_POST['dataRead']));
                    $insertQuery = "INSERT INTO tracking (email, latitude, longitude, time, date, timestamp, timezone) VALUES ";
                    $insertData = [];
                    $insertValues = [];
                    //Prepares the data to be inserted using Bulk insertion.
                    foreach ($noEmptyData as $value) {
                        $line = explode(",", $value);
                        $timestamp = strtotime(date($line[4] . " " . $line[3]));
                        $insertData['email'] = addQuotes($line[0]);
                        $insertData['latitude'] = addQuotes($line[1]);
                        $insertData['longitude'] = addQuotes($line[2]);
                        $insertData['time'] = addQuotes($line[3]);
                        $insertData['date'] = addQuotes($line[4]);
                        $insertData['timestamp'] = addQuotes($timestamp);
                        $insertData['timezone'] = addQuotes($line[5]);
                        $values = implode(",", $insertData);
                        array_push($insertValues, "(" . $values . ")");
                    }
                    //Inserts the data to "tracking" table.
                    $insertQuery .= implode(",", $insertValues);
                    $dbh = mf_connect_db();
                    mf_do_query($insertQuery, array(), $dbh);
                    $insertId = (int) $dbh->lastInsertId();
                    $row = getTrackingInfo($email);
                    $response["timeInterval"] = $row["tracking_time_interval"];
                    if (is_int($insertId)) {
                        $response["message"] = "GPS position successfully saved!";
                    } else {
                        $response["message"] = "GPS position could not be saved!";
                    }
                    header('Content-Type: application/json');
                    echo json_encode($response);
                } else {
                    echo 'An error occured! Try again.';
                }
                break;
            case "errors";
                if (isset($_POST["data"]) && (isset($_POST["email"]) || isset($_POST["id"])) &&
                        isset($_POST["time"]) && isset($_POST["date"]) &&
                        isset($_POST["timezone"]) && isset($_POST["errorType"])) {
                    $data = $_POST["data"];
                    if(isset($_POST["email"])){
                        $email = $_POST["email"];
                    }else if(isset($_POST["id"])){
                        $email = $_POST["id"];
                    }
                    $time = $_POST["time"];
                    $timezone = $_POST["timezone"];
                    $date = $_POST["date"];
                    $errorType = $_POST["errorType"];
                    $insertQuery = "INSERT INTO app_errors (data, email, time, date, timezone, errorType) VALUES (?, ?, ?, ?, ?, ?);";
                    $dbh = mf_connect_db();
                    mf_do_query($insertQuery, array($data, $email, $time, $date, $timezone, $errorType), $dbh);
                    $insertId = (int) $dbh->lastInsertId();
                    if (is_int($insertId)) {
                        echo 'Logs successfully saved!';
                    } else {
                        echo 'Logs could not be saved!';
                    }
                } else {
                    echo 'An error occured! Try again.';
                }
                break;
            default:
                break;
        }
    } else {
        echo 'API only accessible via POST request!';
    }
}
