<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Passport Guard
    |--------------------------------------------------------------------------
    |
    | Here you may specify which authentication guard Passport will use when
    | authenticating users. This value should correspond with one of your
    | guards that is already present in your "auth" configuration file.
    |
    */

    'guard' => 'web',

    /*
    |--------------------------------------------------------------------------
    | Encryption Keys
    |--------------------------------------------------------------------------
    |
    | Passport uses encryption keys while generating secure access tokens for
    | your application. By default, the keys are stored as local files but
    | can be set via environment variables when that is more convenient.
    |
    */

    'private_key' => env('PASSPORT_PRIVATE_KEY'),

    'public_key' => env('PASSPORT_PUBLIC_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Client UUIDs - PENTING: Set ke FALSE
    |--------------------------------------------------------------------------
    |
    | By default, Passport's clients will be identified by integers. If you
    | wish to use UUIDs instead, set this value to true. You must also
    | configure your client models to use UUIDs in that case.
    |
    */

    'client_uuids' => true,

    /*
    |--------------------------------------------------------------------------
    | Personal Access Client
    |--------------------------------------------------------------------------
    |
    | If you enable this option, Laravel will look for the "personal access"
    | client and use it when issuing personal access tokens. If this option
    | is disabled, Laravel will use the first client available.
    |
    */

    'personal_access_client' => [
        'id' => env('PASSPORT_PERSONAL_ACCESS_CLIENT_ID'),
        'secret' => env('PASSPORT_PERSONAL_ACCESS_CLIENT_SECRET'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Passport Database Connection
    |--------------------------------------------------------------------------
    |
    | By default, Passport's models will utilize your application's default
    | database connection. If you wish to use a different connection you
    | may specify the configured name of the database connection here.
    |
    */

    'connection' => null,

    /*
    |--------------------------------------------------------------------------
    | Client Identifier
    |--------------------------------------------------------------------------
    |
    | Here you may specify the column that should be used as the client
    | identifier. This value should correspond with one of your
    | client identifier columns in your "clients" database table.
    |
    */

    'client_id_column' => 'id',

    /*
    |--------------------------------------------------------------------------
    | Passport Storage Driver
    |--------------------------------------------------------------------------
    |
    | Here you may configure the storage driver that Passport will use to
    | store the clients and access tokens. By default, Passport will use
    | the "database" driver which stores everything in the database.
    |
    */

    'storage' => [
        'database' => [
            'connection' => env('DB_CONNECTION', 'mysql'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Authorization Code PKCE
    |--------------------------------------------------------------------------
    |
    | Here you may enable PKCE (Proof Key for Code Exchange) for authorization
    | codes. This is a security extension for OAuth 2.0 authorization code
    | flows. You should only disable this if you have a legacy client.
    |
    */

    'default_scope' => null,

    /*
    |--------------------------------------------------------------------------
    | Pruning
    |--------------------------------------------------------------------------
    |
    | By default, Passport will keep expired tokens and authorization codes
    | in your database. If you want to automatically delete them, you may
    | uncomment the following line and they will be deleted during pruning.
    |
    */

    'delete_expired_tokens' => false,
    'delete_expired_auth_codes' => false,

    /*
    |--------------------------------------------------------------------------
    | Hash Client Secrets
    |--------------------------------------------------------------------------
    |
    | Here you may specify whether the client secrets should be hashed when
    | they are stored in the database. If this option is enabled, you will
    | not be able to retrieve the plain-text value of the client secret.
    |
    */

    'hash_client_secrets' => false,
];
