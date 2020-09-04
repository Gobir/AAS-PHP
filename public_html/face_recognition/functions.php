<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';

//Makes the below variables declared in functions.php available here.
$allowedTimeTrackingValues = ["1", "2", "10", "20", "30", "40", "50", "60", "70", "80", "90", "100", "110", "120"];
//Allowed file types and mimes for images upload.
$allowedExts = array("jpg", "jpeg");
$allowedMimesType = array(
    'image/jpeg',
    'image/jpg'
);

function mf_connect_db() {
    try {
        $dbh = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASSWORD,
                array(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true)
        );
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $dbh->query("SET NAMES utf8");
        $dbh->query("SET sql_mode = ''");
        return $dbh;
    } catch (PDOException $e) {
        die("Error connecting to the database: " . $e->getMessage());
    }
}

function mf_do_query($query, $params, $dbh) {
    $sth = $dbh->prepare($query);
    try {
        $sth->execute($params);
    } catch (PDOException $e) {
        $sth->debugDumpParams();
        die("Query Failed: " . $e->getMessage());
    }

    return $sth;
}

function mf_do_fetch_result($sth) {
    return $sth->fetch(PDO::FETCH_ASSOC);
}

function mf_do_fetch_results($sth) {
    return $sth->fetchAll(PDO::FETCH_ASSOC);
}

function distance($lat1, $lon1, $lat2, $lon2) {
    if (($lat1 == $lat2) && ($lon1 == $lon2)) {
        return 0;
    } else {
        $theta = $lon1 - $lon2;
        $dist = rad2deg(acos(sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta))));
        $meters = $dist * 60 * 1.1515 * 1.609344 * 1000;
        return number_format($meters, 2, ".", "");
    }
}

function correctImageOrientation($filename) {
    if (function_exists('exif_read_data')) {
        $exif = exif_read_data($filename);
        if ($exif && isset($exif['Orientation'])) {
            $orientation = $exif['Orientation'];
            if ($orientation != 1) {
                $img = imagecreatefromjpeg($filename);
                $deg = 0;
                switch ($orientation) {
                    case 3:
                        $deg = 180;
                        break;
                    case 6:
                        $deg = 270;
                        break;
                    case 8:
                        $deg = 90;
                        break;
                }
                if ($deg) {
                    $img = imagerotate($img, $deg, 0);
                }
                // then rewrite the rotated image back to the disk as $filename 
                imagejpeg($img, $filename, 95);
            } // if there is some rotation necessary
        } // if have the exif orientation info
    } // if function exists      
}

function addQuotes($string) {
    return "'" . $string . "'";
}

/**
 * Get the web root URL 
 * @return string The web root URL
 */
function getWebRootUrl() {
    //Checks if the URL can be reached via HTTPS.
    $ssl_suffix = null;
    if (!empty($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] != 'off')) {
        $ssl_suffix = 's';
    } else if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
        $ssl_suffix = 's';
    } else if (isset($_SERVER['HTTP_FRONT_END_HTTPS']) && $_SERVER['HTTP_FRONT_END_HTTPS'] == 'on') {
        $ssl_suffix = 's';
    } else {
        $ssl_suffix = '';
    }
    $webURL = "http" . $ssl_suffix . "://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']);
    return rtrim($webURL, "/") . "/";
}

/**
 * Redirects to a specific page
 * @param string $page  Page to redirect to
 */
function redirectToWebrootUrl($page = null) {
    header("Location: " . getWebRootUrl() . $page);
    exit();
}

/**
 * Generate a hidden input for forms CSRF protection. 
 * @return string
 */
function getHiddenInputString() {
    //Generate a CSRF token and its time validity in session. 
    generatesCsrfTokenInSession(32);
    //Returns HTML code for hidden input with CSRF token.
    return '<input type="hidden" name="csrf_token" value="' . $_SESSION['token'] . '"/>';
}

/**
 * Generate a CSRF token and its time validity in session. 
 * @param int length of token
 */
function generatesCsrfTokenInSession($length) {
    // Generates the token
    $_SESSION['token'] = generateRandomToken($length);
    // Creates a time validity of the generated token of 1 hour
    $_SESSION['token_expire'] = time() + 3600;
}

/**
 * Generate a unique random token. 
 * @param int length of token
 * @return string
 */
function generateRandomToken($length) {
    return bin2hex(openssl_random_pseudo_bytes($length));
}

/**
 * Displays POST or SESSION value for a parameter
 * @param string $post POST value if available
 * @param string $session SESSION value if available
 * @return string
 */
function showPostSession($post, $session) {
    $postSession = "";
    if (isset($_SESSION[$post])) {
        $postSession = $_SESSION[$post];
        unset($_SESSION[$post]);
    } else if (!is_null($session)) {
        $postSession = $session;
    }
    return $postSession;
}

/**
 * Sends the path of the upload folder. 
 * @return string
 */
function getUploadDirectory() {
    //return $_SERVER["DOCUMENT_ROOT"] . rtrim(dirname($_SERVER["PHP_SELF"]), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'users' . DIRECTORY_SEPARATOR;
    return "/home/devouss/public_html/face_recognition/images/users/";
}

/**
 * Creates the upload folder if it does not exists 
 * @param string $path Path of the upload folder to create
 */
function makeUploadDirectory($path) {
    /* if (!file_exists($path)) {
      mkdir($path, 0755, true);
      } */
}

/**
 * Sends the path of the application installation folder. 
 * @return string
 */
function getDirectory() {
    return $_SERVER["DOCUMENT_ROOT"] . rtrim(dirname($_SERVER["PHP_SELF"]), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
}

/**
 * Sends the path of the application installation folder. 
 * @return string
 */
function getWebRootDirectory() {
    return $_SERVER['DOCUMENT_ROOT'] . rtrim(dirname($_SERVER['PHP_SELF']), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
}

/**
 * Process uploaded image 
 * @param string $filePath Image uploaded temporary name
 * @param string $minSize Image uploaded minimum size
 * @param string $extension Image type JPG, JPEG, PNG
 * @param string $queryType U for update and I for insert
 * @param string $fullName Full name
 * @param string $email Email address
 * @param string $id Id of table row
 */
function processUploadedImage($filePath, $minSizeW, $minSizeH, $extension, $queryType, $fullName, $email, $id) {
    //Gets size of the image uploaded.
    $image_info = getimagesize($filePath);
    $imageWidth = $image_info[0];
    $imageHeight = $image_info[1];
    //Checks the minimum image allowed size.
    if ($imageWidth < $minSizeW || $imageHeight < $minSizeH) {
        $msg = "-The image uploaded should have a minimum of 640 x 480 px in width and height!";
        if ($queryType === "I") {
            $_SESSION['save_error'] = $msg;
        } else if ($queryType === "U") {
            $_SESSION['edit_error'] = $msg;
        }
    } else {
        //Creates upload folder if it does not exist.
        makeUploadDirectory(getUploadDirectory());
        //Generates a new unique name for the uploaded image.
        $imgName = generateRandomToken(16) . '.' . $extension;
        //Path where to save the image.
        $saveTo = getUploadDirectory() . $imgName;
        //Move image to the upload folder
        move_uploaded_file($filePath, $saveTo);
        $dbh = mf_connect_db();
        if ($queryType === "I") {
            //Creates a new user.
            $insertQuery = "INSERT INTO users (fullname, email, profile_img, tracking_time_interval, created_by) VALUES (?, ?, ?, ?, ?);";
            $dbh = mf_connect_db();
            mf_do_query($insertQuery, array($fullName, $email, $imgName, $_POST["tracking_time_interval"], $_SESSION["admin_email"]), $dbh);
            $insertId = (int) $dbh->lastInsertId();
            $queryDel = "DELETE FROM faces WHERE email = ?";
            mf_do_query($queryDel, array($email), $dbh);
            $query = "INSERT INTO faces (image_path, email, light_version_id) VALUES (?, ?, ?)";
            mf_do_query($query, array($imgName, $email, fixId($insertId)), $dbh);
            $insertFace = (int) $dbh->lastInsertId();
            if (is_int($insertId) && is_int($insertFace)) {
                //Creates a success session message.    
                $_SESSION["save_success"] = "User Created Successfully!";
            } else {
                //Creates a success session message.
                $_SESSION["save_error"] = "User Could not be Created!";
            }
        } else if ($queryType === "U") {
            //Updates an existing user.
            $updateQuery = "UPDATE users SET fullname = ?, email = ?,  profile_img = ?, tracking_time_interval = ? WHERE id = ?;";
            $dbh = mf_connect_db();
            mf_do_query($updateQuery, array($fullName, $email, $imgName, $_POST["tracking_time_interval"], $id), $dbh);
            $queryUpd = "UPDATE faces SET image_path = ? WHERE email = ?";
            mf_do_query($queryUpd, array($imgName, $email), $dbh);
            $_SESSION["edit_success"] = "User Updated Successfully!";
        }
    }
}

/**
 * Displays the POST selected value for an HTML select
 * @param string $post POST value selected if available
 * @param string $value Value selected if available
 * @return string
 */
function showSelectSavedValue($post, $value) {
    $selected = "";
    if ($post == $value) {
        $selected = ' selected="selected"';
    }
    return $selected;
}

/**
 * Returns CSRF token saved in session. 
 * @return string
 */
function getCsrfToken() {
    return $_SESSION['token'];
}

/**
 * Get email template to use with body.
 * @param string $body  Email body
 * @return string Email template to use with body
 */
function getEmailTemplate($body) {
    return '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" style="margin: 0;padding: 0;font-size: 100%;font-family: \'Avenir Next\', &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;line-height: 1.65;">
    <head style="margin: 0;padding: 0;font-size: 100%;font-family: \'Avenir Next\', &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;line-height: 1.65;">
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" style="margin: 0;padding: 0;font-size: 100%;font-family: \'Avenir Next\', &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;line-height: 1.65;">
        <meta name="viewport" content="width=device-width" style="margin: 0;padding: 0;font-size: 100%;font-family: \'Avenir Next\', &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;line-height: 1.65;">
        <style type="text/css" style="margin: 0;padding: 0;font-size: 100%;font-family: \'Avenir Next\', &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;line-height: 1.65;">
            * { margin: 0; padding: 0; font-size: 100%; font-family: \'Avenir Next\', "Helvetica Neue", "Helvetica", Helvetica, Arial, sans-serif; line-height: 1.65; }
            img { max-width: 100%; margin: 0 auto; display: block; }
            body, .body-wrap { width: 100% !important; height: 100%; background: #f8f8f8; }
            a { color: #0000b2; text-decoration: none; }
            a:hover { text-decoration: underline; }
            .text-center { text-align: center; }
            .text-right { text-align: right; }
            .text-left { text-align: left; }
            .button { display: inline-block; color: white; background: #71bc37; border: solid #71bc37; border-width: 10px 20px 8px; font-weight: bold; border-radius: 4px; }
            .button:hover { text-decoration: none; }
            h1, h2, h3, h4, h5, h6 { margin-bottom: 20px; line-height: 1.25; }
            h1 { font-size: 32px; }
            h2 { font-size: 28px; }
            h3 { font-size: 24px; }
            h4 { font-size: 20px; }
            h5 { font-size: 16px; }
            p, ul, ol { font-size: 16px; font-weight: normal; margin-bottom: 20px; }
            .container { display: block !important; clear: both !important; margin: 0 auto !important; max-width: 580px !important; }
            .container table { width: 100% !important; border-collapse: collapse; }
            .container .masthead { padding: 80px 0; background: #71bc37; color: white; }
            .container .masthead h1 { margin: 0 auto !important; max-width: 90%; text-transform: uppercase; }
            .container .content { background: white; padding: 30px 35px; }
            .container .content.footer { background: none; }
            .container .content.footer p { margin-bottom: 0; color: #888; text-align: center; font-size: 14px; }
            .container .content.footer a { color: #888; text-decoration: none; font-weight: bold; }
            .container .content.footer a:hover { text-decoration: underline; }
        </style>
    </head>
    <body style="margin: 0;padding: 0;font-size: 100%;font-family: \'Avenir Next\', &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;line-height: 1.65;height: 100%;background: #f8f8f8;width: 100% !important;">
        <table class="body-wrap" style="margin: 0;padding: 0;font-size: 100%;font-family: \'Avenir Next\', &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;line-height: 1.65;height: 100%;background: #f8f8f8;width: 100% !important;">
            <tr style="margin: 0;padding: 0;font-size: 100%;font-family: \'Avenir Next\', &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;line-height: 1.65;">
                <td class="container" style="margin: 0 auto !important;padding: 0;font-size: 100%;font-family: \'Avenir Next\', &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;line-height: 1.65;display: block !important;clear: both !important;max-width: 580px !important;">
                    <!-- Message start -->
                    <table style="margin: 0;padding: 0;font-size: 100%;font-family: \'Avenir Next\', &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;line-height: 1.65;border-collapse: collapse;width: 100% !important;">
                        <tr style="margin: 0;padding: 0;font-size: 100%;font-family: \'Avenir Next\', &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;line-height: 1.65;">
                            <td class="content" style="margin: 0;padding: 30px 35px;font-size: 100%;font-family: \'Avenir Next\', &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;line-height: 1.65;background: white;">
                                <p style="margin: 0;padding: 0;font-size: 16px;font-family: \'Avenir Next\', &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;line-height: 1.65;font-weight: normal;margin-bottom: 20px;">' . $body . '</p>
                                <p style="margin: 0;padding: 0;font-size: 16px;font-family: \'Avenir Next\', &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;line-height: 1.65;font-weight: normal;margin-bottom: 20px;">Thanks you!</p>
                                            <p style="margin: 0;padding: 0;font-size: 16px;font-family: \'Avenir Next\', &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;line-height: 1.65;font-weight: normal;margin-bottom: 20px;"><em style="margin: 0;padding: 0;font-size: 100%;font-family: \'Avenir Next\', &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;line-height: 1.65;">&#x2D; Administrator.</em></p>
                                            </td>
                                            </tr>
                                            </table>
                                            </td>
                                            </tr>
                                            </table>
                                            </body>
                                            </html>';
}

/**
 * Send email with/without SMTP
 * @param string $to Email receiver
 * @param string $subject  Email subject
 * @param string $body  Email body
 * @return boolean
 */
function sendMail($to, $subject, $body) {
    $response = true;
    $mail = new PHPMailer();
    try {
        //Recipients
        $mail->setFrom("admin@admin.com", "Admin");
        $mail->addAddress($to);
        //Sets email to be HTML.
        $mail->isHTML(true);
        //Sets the email subject.
        $mail->Subject = $subject;
        //Sets the email body.
        $mail->Body = $body;
        //Sends the email.
        $mail->send();
    } catch (Exception $e) {
        $response = false;
    }
    return $response;
}

/**
 * Validates email address
 * @param string $email Email address
 * @return boolean
 */
function isEmailValid($email) {
    $check = false;
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $check = true;
    }
    return $check;
}

/**
 * Validates full name
 * @param string $fullName Full name
 * @return boolean
 */
function isFullNameValid($fullName) {
    $check = false;
    if (preg_match("/^([a-zA-Z' ]+)$/", $fullName)) {
        $check = true;
    }
    return $check;
}

/**
 * Validates password
 * @param string $password Password
 * @return boolean
 */
function isPasswordValid($password) {
    $check = true;
    //Password must be no less than 6 chars and no more than 12.
    if (strlen($password) < 6 || strlen($password) > 12) {
        $check = false;
    }
    return $check;
}

/**
 * Validates a field if empty
 * @param string $value Field value
 * @return boolean
 */
function isNotEmpty($value) {
    $check = false;
    if (empty($value) || is_null($value)) {
        $check = true;
    }
    return $check;
}

/**
 * Validates email address if already taken
 * @param string $fieldValue Target field value
 * @param string $fieldName Target field name
 * @param string $id Id of table row
 * @return boolean
 */
function isFieldTaken($fieldValue, $fieldName, $table, $id = null) {
    $dbh = mf_connect_db();
    $check = false;
    //Checks if the id of the target row exists.
    $query = "SELECT id FROM " . $table . " WHERE " . $fieldName . " = ?;";
    $sth = mf_do_query($query, array($fieldValue), $dbh);
    $rows = mf_do_fetch_result($sth);
    $result = [];
    if ($rows !== false) {
        $result = $rows;
    }
    //Case where we need to check only if the id of the row exits.
    if (is_null($id)) {
        if (sizeof($result) !== 0) {
            $check = true;
        }
    } else {
        //Case where we need to check if the id of the row exits and matches with the id passed.
        if (sizeof($result) !== 0) {
            if ($result["id"] != $id) {
                $check = true;
            }
        }
    }
    return $check;
}

/**
 * Creates SESSION from a POST value 
 * @param string $posts POST values submitted
 */
function createPostSession($posts) {
    foreach ($posts as $key => $value) {
        $_SESSION[$key] = $value;
    }
}

/**
 * Decides what JavaScript code for Server Sent Event to add to the page track_user.php 
 * @param string $date Date in  "m/d/Y" format 
 * @return boolean
 */
function isSseAllowed($date) {
    //Object representing the current date/time
    $today = new DateTime();
    //Resets time part, to prevent partial comparison
    $today->setTime(0, 0, 0);
    $match_date = DateTime::createFromFormat("m/d/Y", $date);
    //Resets time part, to prevent partial comparison
    $match_date->setTime(0, 0, 0);
    $diff = $today->diff($match_date);
    //Extracts days count in interval
    $diffDays = (integer) $diff->format("%R%a");
    $isSseAllowed = false;
    switch ($diffDays) {
        case 0: //Today
            $isSseAllowed = true;
            break;
        case -1://User time is one day behind admin time 
            $isSseAllowed = true;
            break;
        case +1://User time is one day ahead of admin time 
            $isSseAllowed = true;
            break;
        default:
            $isSseAllowed = false;
    }
    return $isSseAllowed;
}

function getTrackingInfo($email) {
    $query = "SELECT tracking_time_interval, created_by FROM users WHERE email = ?";
    $dbh = mf_connect_db();
    $sth = mf_do_query($query, array($email), $dbh);
    $row = mf_do_fetch_result($sth);
    return $row;
}

function getOfficeInfo($created_by) {
    $query = "SELECT latitude, longitude, premises FROM admin WHERE email = ? AND role = ?";
    $dbh = mf_connect_db();
    $sth = mf_do_query($query, array($created_by, "admin"), $dbh);
    $row = mf_do_fetch_result($sth);
    return $row;
}

/**
 * Validates a given coordinate
 *
 * @param float|int|string $lat Latitude
 * @param float|int|string $long Longitude
 * @return bool `true` if the coordinate is valid, `false` if not
 */
function isValidLatLng($lat, $long) {
    return preg_match('/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?),[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/', $lat . ',' . $long);
}

/**
 * Checks if admin is logged in, if not, redirects to login page
 */
function isAdminLoggedin() {
    if (!isset($_SESSION['admin']) || !$_SESSION['admin']) {
        redirectToWebrootUrl("login.php");
    }
}

function isAlreadyLoggedIn() {
    if (isset($_SESSION['superuser']) && $_SESSION['superuser']) {
        redirectToWebrootUrl("super-user.php");
    } else if (isset($_SESSION['admin']) && $_SESSION['admin']) {
        redirectToWebrootUrl("settings.php");
    }
}

/**
 * Checks if superuser is logged in, if not, redirects to login page
 */
function isSuperUserLoggedin() {
    if (!isset($_SESSION['superuser']) || !$_SESSION['superuser']) {
        redirectToWebrootUrl("login.php");
    }
}

function isAdminLoggedinOrSupperUserLogin() {
    if (!isset($_SESSION['superuser']) && !$_SESSION['superuser'] && !isset($_SESSION['admin']) && !$_SESSION['admin']) {
        redirectToWebrootUrl("login.php");
    }
}

function buildRequestBody($returnUrl, $cancelUrl, $brandName, $referenceId, $description, $total, $currency, $unitPrice, $qte, $itemName) {
    return array(
        'intent' => 'CAPTURE',
        'application_context' =>
        array(
            'return_url' => $returnUrl,
            'cancel_url' => $cancelUrl,
            'brand_name' => $brandName,
            'locale' => 'en-US',
            'landing_page' => 'BILLING',
            'user_action' => 'PAY_NOW',
        ),
        'purchase_units' =>
        array(
            0 =>
            array(
                'reference_id' => $referenceId,
                'description' => $description,
                'amount' =>
                array(
                    'currency_code' => $currency,
                    'value' => $total,
                    'breakdown' =>
                    array(
                        'item_total' =>
                        array(
                            'currency_code' => $currency,
                            'value' => $total,
                        )
                    ),
                ),
                'items' =>
                array(
                    0 =>
                    array(
                        'name' => $itemName,
                        'unit_amount' =>
                        array(
                            'currency_code' => $currency,
                            'value' => $unitPrice,
                        ),
                        'quantity' => $qte
                    ),
                )
            )
        )
    );
}

function userLimitReached($email) {
    $return = true;
    $query = "SELECT admin_quota FROM admin WHERE email = ?";
    $dbh = mf_connect_db();
    $sth = mf_do_query($query, array($email), $dbh);
    $row = mf_do_fetch_result($sth);
    if (empty($row)) {
        $return = false;
    } else {
        $usersQuota = (int) $row["admin_quota"];
        $queryCount = "SELECT COUNT(id) FROM users WHERE created_by = ?";
        $sthCount = mf_do_query($queryCount, array($email), $dbh);
        $rowCount = mf_do_fetch_result($sthCount);
        if (empty($rowCount)) {
            $return = false;
        } else if ((int) $rowCount["COUNT(id)"] < $usersQuota) {
            $return = true;
        } else {
            $return = false;
        }
    }
    return $return;
}

function isPost($redirectUrl) {
    if ($_SERVER['REQUEST_METHOD'] != 'POST') {
        redirectToWebrootUrl($redirectUrl);
    }
}

function isAjax($redirectUrl) {
    if (empty($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {
        redirectToWebrootUrl($redirectUrl);
    }
}

function requirePayPalFiles() {
    $requireArray = ['PayPal/PayPalHttp/HttpClient.php',
        'PayPal/PayPalHttp/HttpRequest.php',
        'PayPal/PayPalHttp/Environment.php',
        'PayPal/PayPalHttp/Encoder.php',
        'PayPal/PayPalHttp/Serializer.php',
        'PayPal/PayPalHttp/Serializer/Text.php',
        'PayPal/PayPalHttp/Serializer/Multipart.php',
        'PayPal/PayPalHttp/Serializer/Form.php',
        'PayPal/PayPalHttp/Serializer/FormPart.php',
        'PayPal/PayPalHttp/Serializer/Json.php',
        'PayPal/PayPalHttp/Injector.php',
        'PayPal/PayPalHttp/Curl.php',
        'PayPal/PayPalHttp/HttpResponse.php',
        'PayPal/PayPalHttp/IOException.php',
        'PayPal/PayPalHttp/HttpException.php',
        'PayPal/PayPalCheckoutSdk/Core/PayPalEnvironment.php',
        'PayPal/PayPalCheckoutSdk/Core/PayPalHttpClient.php',
        'PayPal/PayPalCheckoutSdk/Core/SandboxEnvironment.php',
        'PayPal/PayPalCheckoutSdk/Core/ProductionEnvironment.php',
        'PayPal/PayPalCheckoutSdk/Core/GzipInjector.php',
        'PayPal/PayPalCheckoutSdk/Core/FPTIInstrumentationInjector.php',
        'PayPal/PayPalCheckoutSdk/Core/AuthorizationInjector.php',
        'PayPal/PayPalCheckoutSdk/Core/AccessTokenRequest.php',
        'PayPal/PayPalCheckoutSdk/Core/UserAgent.php',
        'PayPal/PayPalCheckoutSdk/Core/Version.php',
        'PayPal/PayPalCheckoutSdk/Core/AccessToken.php',
        'PayPal/PayPalClient.php'];
    foreach ($requireArray as $file) {
        require $file;
    }
}

function findRecord($email, $query) {
    $dbh = mf_connect_db();
    $sth = mf_do_query($query, array($email), $dbh);
    $row = mf_do_fetch_result($sth);
    return $row;
}

function doInsertUpdate($id, $lastAction) {
    $action = "";
    if ($lastAction == "Check In") {
        $action = "IN";
    } else if ($lastAction == "Check Out") {
        $action = "OUT";
    }
    $insertQuery = "INSERT INTO light (light_version_id, last_action, date_limit) VALUES (?, ?, ?)";
    $updateQuery = "UPDATE users SET light_version_user = ? WHERE id = ?";
    $dbh = mf_connect_db();
    mf_do_query($insertQuery, array($id, $action, date("Y-m-d") . " 23:59:59"), $dbh);
    mf_do_query($updateQuery, array( "Yes", unfixId($id)), $dbh);
}

function isJson($string) {
    $return = false;
    json_decode($string);
    if (json_last_error() == JSON_ERROR_NONE) {
        $return = true;
    }
    return $return;
}

function fixId($id) {
    $return = $id;
    if (strlen($id) == 1) {
        $return = "00" . $id;
    } else if (strlen($id) == 2) {
        $return = "0" . $id;
    }
    return $return;
}

function unfixId($id){
    return (int) $id;
}
