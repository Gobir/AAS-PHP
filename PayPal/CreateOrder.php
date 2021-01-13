<?php

use PayPalCheckoutSdk\Orders\OrdersCreateRequest;

class CreateOrder {

    /**
     * Setting up the JSON request body for creating the Order with complete request body. The Intent in the
     * request body should be set as "AUTHORIZE" for authorize intent flow.
     * 
     */
    private static function buildRequestBody($returnUrl, $cancelUrl, $brandName, $referenceId, $description, $total, $currency, $unitPrice, $qte, $itemName) {
        return array(
            'intent' => 'AUTHORIZE',
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

    /**
     * This is the sample function which can be used to create an order. It uses the
     * JSON body returned by buildRequestBody() to create an new Order.
     */
    public function __construct($returnUrl, $cancelUrl, $brandName, $referenceId, $description, $total, $currency, $unitPrice, $qte, $itemName) {
        $request = new OrdersCreateRequest();
        $request->headers["prefer"] = "return=representation";
        $request->body = CreateOrder::buildRequestBody($returnUrl, $cancelUrl, $brandName, $referenceId, $description, $total, $currency, $unitPrice, $qte, $itemName);
        return $request;
    }
    
}

