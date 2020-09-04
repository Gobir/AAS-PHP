<?php

session_start();
use PayPalCheckoutSdk\Orders\OrdersCreateRequest;

require 'config.php';
require 'functions.php';
isAdminLoggedin();
/*
 * ---------------------------------------------------------------
 * add-user.php
 * ---------------------------------------------------------------
 * Adds a user.
 * HTML source at: https://github.com/BlackrockDigital/startbootstrap-sb-admin
 */

/* The MIT License (MIT)
 * 
 * Copyright (C) 2013-2019 Blackrock Digital LLC
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy* 
 * of this software and associated documentation files (the "Software"), to deal* 
 * in the Software without restriction, including without limitation the rights* 
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell* 
 * copies of the Software, and to permit persons to whom the Software is* 
 * furnished to do so, subject to the following conditions:* 
 * 
 * The above copyright notice and this permission notice shall be included in* 
 * all copies or substantial portions of the Software.* 
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR* 
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,* 
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE* 
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER* 
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,* 
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN* 
 * THE SOFTWARE.
 */

function sendBackWithMessage($unsersNbr, $msg, $type) {
    createPostSession(array(
        "unsersNbr" => $unsersNbr
    ));
    if ($type == "success") {
        $_SESSION["make_payment_success"] = $msg;
    } else if ($type == "error") {
        $_SESSION["make_payment_error"] = $msg;
    } else if ($type == "warning") {
        $_SESSION["make_payment_warning"] = $msg;
    } else {
        $_SESSION["make_payment_error"] = $msg;
    }
    redirectToWebrootUrl("settings.php");
}

//If payment form for is submitted
if (isset($_POST["unsersNbr"])) {
    $unsersNbr = $_POST["unsersNbr"];
    $errors = [];
    //Validates admin user quota.
    if (!ctype_digit($unsersNbr)) {
        //Adds the error message to the errors array.
        array_push($errors, "-Number of users must be a digit.");
    } else if ($unsersNbr < 1) {
        //Adds the error message to the errors array.
        array_push($errors, "-Minimum number of users allowed is 1.");
    } else if ($unsersNbr > 100) {
        //Adds the error message to the errors array.
        array_push($errors, "-Maximum number of users allowed in one single payment is 100.");
    }
    //Checks if validation errors exist.
    if (!empty($errors)) {
        sendBackWithMessage($unsersNbr, implode("<br>", $errors), "error");
    } else {
        $query = "SELECT user_price FROM admin WHERE email = ? and role = ?";
        $dbh = mf_connect_db();
        $email = $_SESSION['admin_email'];
        $sth = mf_do_query($query, array($email, "admin"), $dbh);
        $row = mf_do_fetch_result($sth);
        if (empty($row)) {
            //Creates sessions to display the POST form data sent and avoid reentering them.
            sendBackWithMessage($unsersNbr, "An error occurred! Try again please.", "error");
        } else {
            $returnUrl = getWebRootUrl() . "authorize-order.php";
            $cancelUrl = getWebRootUrl() . "settings.php";
            $brandName = BRAND_NAME;
            $referenceId = generateRandomToken(12);
            $description = "Buying additional users.";
            $currency = "USD";
            $unitPrice = $row["user_price"];
            $total = bcmul($unsersNbr, $unitPrice, 2);
            $s = "";
            if ((int) $unsersNbr > 1) {
                $s = "s";
            }
            $itemName = " additional user" . $s . " each at ";
            requirePayPalFiles();
            require 'PayPal/PayPalCheckoutSdk/Orders/OrdersCreateRequest.php';
            $request = new OrdersCreateRequest();
            $request->headers["prefer"] = "return=representation";
            $request->body = buildRequestBody($returnUrl, $cancelUrl, $brandName, $referenceId, $description, $total, $currency, $unitPrice, $unsersNbr, $itemName);
            try {
                $client = PayPalClient::client();
                $response = $client->execute($request);
                if ($response->statusCode != 201) {
                    sendBackWithMessage($unsersNbr, "An error occurred! Try again please.<br>Status: " . $response->result->status, "error");
                } else {
                    foreach ($response->result->links as $link) {
                        if ($link->rel == "approve") {
                            $redirectTo = $link->href;
                        }
                    }
                    if (empty($redirectTo)) {
                        sendBackWithMessage($unsersNbr, "An error occurred! Try again please.", "error");
                    } else {
                        header("Location: " . $redirectTo);
                        exit();
                    }
                }
            } catch (PayPalHttp\HttpException $e) {
                print_r($e);die();
                sendBackWithMessage($unsersNbr, "An error occurred! Try again please.", "error");
            }
        }
    }
}else{
    sendBackWithMessage("", "An error occurred! Try again please.", "error");
}


    
    