<?php


return [
    /*
    |--------------------------------------------------------------------------
    | Local domain
    |--------------------------------------------------------------------------
    |
    | By default, selects a free address and port.
    | You can specify your own address, for example: 'project.localhost:8080'
    |
    */

    'domain' => '0.0.0.0:0',

    /*
    |--------------------------------------------------------------------------
    | Key generation parameters
    |--------------------------------------------------------------------------
    |
    | Settings options available see
    |
    | https://www.php.net/manual/en/function.openssl-csr-new.php
    |
    */

    'key' => [
        'private_key_bits' => 2048,
        'private_key_type' => OPENSSL_KEYTYPE_RSA,
    ],

    /*
    |--------------------------------------------------------------------------
    | Certificate Signing Request
    |--------------------------------------------------------------------------
    |
    | Settings options available see
    |
    | https://www.php.net/manual/en/function.openssl-csr-new.php
    |
    */

    'csr' => [
        'digest_alg' => 'sha256'
    ]
];