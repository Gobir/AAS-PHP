<?php

use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use PayPalCheckoutSdk\Core\ProductionEnvironment;

class PayPalClient {

    /**
     * Returns PayPal HTTP client instance with environment which has access
     * credentials context. This can be used invoke PayPal API's provided the
     * credentials have the access to do so.
     */
    public static function client() {
        return new PayPalHttpClient(self::environment());
    }

    /**
     * Setting up and Returns PayPal SDK environment with PayPal Access credentials.
     * For demo purpose, we are using SandboxEnvironment. In production this will be
     * ProductionEnvironment.
     */
    public static function environment() {
        // Creating an environment
        $query = "SELECT * FROM paypal_keys";
        $dbh = mf_connect_db();
        $sth = mf_do_query($query, array(), $dbh);
        $row = mf_do_fetch_result($sth);
        if ($row["status"] == 'checked="checked"') {
            $clientId = $row["livebox_client_id"];
            $clientSecret = $row["livebox_secret_id"];
            $environment = new ProductionEnvironment($clientId, $clientSecret);
        } else {
            $clientId = $row["sandbox_client_id"];
            $clientSecret = $row["sandbox_secret_id"];
            $environment = new SandboxEnvironment($clientId, $clientSecret);
        }
        return $environment;
    }

}
