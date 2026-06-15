<?php

return [
    /**
     * Username of the merchant.
     */
    'username' => env('FONEPAY_USERNAME', 'labasam'),
    /**
     * Password of the merchant
     */
    'password' => env('FONEPAY_PASSWORD', 'F0nepay@123#'),
    /**
     * API Base URL
     */
    'base_url' => env('FONEPAY_BASE_URL', 'https://dev-external-gateway-new.fonepay.com/merchantThirdparty'),
    /**
     * API BASE PATH
     */
    'base_path' => env('FONEPAY_BASE_PATH', '/api/merchant/third-party/v2'),
    /**
     * Terminal ID
     */
    'terminal_id' => env('FONEPAY_TERMINAL_ID', '4271423331147924'),
    /**
     * Private Key Path
     */
    'private_key_path' => storage_path('keys/private.pem'),
];
