<?php

return [

    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        '*'  // Add HTTP version too
    ],

    'allowed_origins_patterns' => [
        '/^https?:\/\/.*\.test$/', // Allow all .test domains
    ],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,

];
