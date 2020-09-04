<?php

session_start();
require 'config.php';
require 'functions.php';
isAdminLoggedin();

use PayPalCheckoutSdk\Orders\OrdersGetRequest;
use PayPalCheckoutSdk\Orders\OrdersCaptureRequest;

requirePayPalFiles();
require 'PayPal/PayPalCheckoutSdk/Orders/OrdersGetRequest.php';
require 'PayPal/PayPalCheckoutSdk/Orders/OrdersCaptureRequest.php';

function sendBackWithMessage($msg, $type) {
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

if (isset($_GET["token"])) {
    $token = $_GET["token"];
    $dbh = mf_connect_db();
    $client = PayPalClient::client();
    $responseGetOrder = $client->execute(new OrdersGetRequest($token));
    if ($responseGetOrder->result->status == "APPROVED") {
        //Save approved order.
        $qte = $responseGetOrder->result->purchase_units[0]->items[0]->quantity;
        $description = $responseGetOrder->result->purchase_units[0]->items[0]->name;
        $refId = $responseGetOrder->result->purchase_units[0]->reference_id;
        $buyerEmail = $responseGetOrder->result->purchase_units[0]->payee->email_address;
        $unitPrice = $responseGetOrder->result->purchase_units[0]->items[0]->unit_amount->value;
        $currency = $responseGetOrder->result->purchase_units[0]->amount->currency_code;
        $amount = $responseGetOrder->result->purchase_units[0]->amount->value;
        $orderId = $responseGetOrder->result->id;
        $query = "INSERT INTO orders (order_id, order_status, order_reference_id, order_price, order_currency, order_payee_email, order_name, order_qte, order_payer_firstname, order_payer_lastname, order_create_time, order_intent, order_raw_json) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        mf_do_query($query, array(
            $orderId,
            $responseGetOrder->result->status,
            $refId,
            $amount,
            $currency,
            $buyerEmail,
            $description,
            $qte,
            $responseGetOrder->result->payer->name->given_name,
            $responseGetOrder->result->payer->name->surname,
            $responseGetOrder->result->create_time,
            $responseGetOrder->result->intent,
            json_encode($responseGetOrder->result)),
                $dbh);
        $insertId = (int) $dbh->lastInsertId();
        if (is_int($insertId)) {
            $requestCaptureOrder = new OrdersCaptureRequest($token);
            $responseCaptureOrder = $client->execute($requestCaptureOrder);
            $captureStatus = $responseCaptureOrder->result->purchase_units[0]->payments->captures[0]->status;
            $name = $responseCaptureOrder->result->purchase_units[0]->shipping->name->full_name;
            $address = $responseCaptureOrder->result->purchase_units[0]->shipping->address->address_line_1 . " "
                    . $responseCaptureOrder->result->purchase_units[0]->shipping->address->admin_area_2 . " "
                    . $responseCaptureOrder->result->purchase_units[0]->shipping->address->admin_area_1 . " "
                    . $responseCaptureOrder->result->purchase_units[0]->shipping->address->postal_code . " "
                    . $responseCaptureOrder->result->purchase_units[0]->shipping->address->country_code;
            $paymentId = $responseCaptureOrder->result->purchase_units[0]->payments->captures[0]->id;
            $paymentStatus = $responseCaptureOrder->result->purchase_units[0]->payments->captures[0]->status;
            //Save payment.
            $query = "INSERT INTO payments (payment_id, payment_status, payment_reference_id, payment_full_name, payment_address, payment_amount, payment_captures_id, payment_captures_status, payment_captures_reason, payment_currency_code, payment_seller_protection, payment_create_time, payment_update_time, payment_raw_json) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            mf_do_query($query, array(
                $responseCaptureOrder->result->id,
                $responseCaptureOrder->result->status,
                $responseCaptureOrder->result->purchase_units[0]->reference_id,
                $name,
                $address,
                $responseCaptureOrder->result->purchase_units[0]->payments->captures[0]->amount->value,
                $responseCaptureOrder->result->purchase_units[0]->payments->captures[0]->id,
                $captureStatus,
                $responseCaptureOrder->result->purchase_units[0]->payments->captures[0]->status_details->reason,
                $responseCaptureOrder->result->purchase_units[0]->payments->captures[0]->amount->currency_code,
                $responseCaptureOrder->result->purchase_units[0]->payments->captures[0]->seller_protection->status,
                $responseCaptureOrder->result->purchase_units[0]->payments->captures[0]->create_time,
                $responseCaptureOrder->result->purchase_units[0]->payments->captures[0]->update_time,
                json_encode($responseCaptureOrder->result)),
                    $dbh);
            if ($responseCaptureOrder->result->status == "COMPLETED") {
                $emailBody = "<h4>Order Information:</h4>"
                        . "<p style='line-height:0.2;'>Description: " . $qte . " " . $fixedDesc . "</p>"
                        . "<p style='line-height:0.2;'>Invoice Number: " . $refId . "</p>"
                        . "<h4>Order Details:</h4>"
                        . "<p style='line-height:0.2;'>Description: " . $fixedDesc . "</p>"
                        . "<p style='line-height:0.2;'>Quantity: " . $qte . "</p>"
                        . "<p style='line-height:0.2;'>Unit Price: " . $unitPrice . " " . $currency . "</p>"
                        . "<p style='line-height:0.2;'>Total Price: " . $amount . "</p>"
                        . "<h4>Billing Information:</h4>"
                        . "<p style='line-height:0.2;'>Name: " . $name . "</p>"
                        . "<p style='line-height:0.2;'>Address: " . $address . "</p>"
                        . "<p style='line-height:0.2;'>Email: " . $buyerEmail . "</p>"
                        . "<p style='line-height:0.2;'>Order Id: " . $orderId . "</p>"
                        . "<p style='line-height:0.2;'>Payment ID: " . $paymentId . "</p>"
                        . "<p style='line-height:0.2;'>Payment Status: " . $paymentStatus . "</p>"
                        . "<hr>";
                if ($captureStatus == "COMPLETED") {
                    //Update subscription, send email and success message.
                    $email = $_SESSION["admin_email"];
                    $query = "SELECT admin_quota FROM admin WHERE email = ? and role = ?";
                    $sth = mf_do_query($query, array($email, "admin"), $dbh);
                    $row = mf_do_fetch_result($sth);
                    $newQuota = bcadd($qte, $row["admin_quota"], 0);
                    $updateQuery = "UPDATE admin SET admin_subscription = ?, admin_quota = ? WHERE email = ?;";
                    mf_do_query($updateQuery, array("Paid", $newQuota, $email), $dbh);
                    $fixedDesc = str_replace("each at", "", $description);
                    sendMail($email, "Your Payment Receipt With Automated Attendance System", getEmailTemplate($emailBody));
                    sendBackWithMessage("Payment Approved!", "success");
                } else if ($captureStatus == "DECLINED") {
                    sendBackWithMessage("Payment Declined!", "error");
                    //Send error message.
                } else if ($captureStatus == "PENDING") {
                    sendMail($email, "Your Payment Receipt With Automated Attendance System", getEmailTemplate($emailBody));
                    sendBackWithMessage("Payment Pending!", "warning");
                    //Send warning message
                } else {
                    sendBackWithMessage("Payment Declined!", "error");
                    //Send error message.
                }
            } else {
                sendBackWithMessage("An error occurred! Try again please. <br>Payment status returned as" . $responseCaptureOrder->result->status . ".", "error");
            }
        } else {
            sendBackWithMessage("An error occurred! Try again please. <br> Order could not be saved.", "error");
        }
    } else {
        sendBackWithMessage("An error occurred! Try again please. <br> Payment status returned as " . $responseGetOrder->result->status . ".", "error");
    }
}else{
    sendBackWithMessage("An error occurred! Try again please.", "error");
}
