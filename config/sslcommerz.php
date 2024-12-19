<?php
// SSLCommerz configuration
return [
    'projectPath' => env('PROJECT_PATH'),
    // For Sandbox, use "https://sandbox.sslcommerz.com"
    // For Live, use "https://securepay.sslcommerz.com"
    'apiDomain' => env("API_DOMAIN_URL", "https://sandbox.sslcommerz.com"),
//    'apiDomain' => "https://securepay.sslcommerz.com",
    'apiCredentials' => [
        'store_id' => env("STORE_ID", "texon626162619f17f"),
        'store_password' => env("STORE_PASSWORD", "texon626162619f17f@ssl"),
//        'store_id' => "shukhimartcombd0live",
//        'store_password' => "65AE477AC42BA91527",
    ],
    'apiUrl' => [
        'make_payment' => "/gwprocess/v4/api.php",
        'transaction_status' => "/validator/api/merchantTransIDvalidationAPI.php",
        'order_validate' => "/validator/api/validationserverAPI.php",
        'refund_payment' => "/validator/api/merchantTransIDvalidationAPI.php",
        'refund_status' => "/validator/api/merchantTransIDvalidationAPI.php",
    ],
    'connect_from_localhost' => true, // For Sandbox, use "true", For Live, use "false"
//    'connect_from_localhost' => env("IS_LOCALHOST", true), // For Sandbox, use "true", For Live, use "false"
    'success_url' => '/success',
    'failed_url' => '/fail',
    'cancel_url' => '/cancel',
    'ipn_url' => '/ipn',
];

