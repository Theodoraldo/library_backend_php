<?php

return [

    'paths' => ['api/*', 'sanctum/csrf-cookie', 'signup', 'signin', 'signout', 'user'],
    'allowed_methods' => ['*'],
    'allowed_origins' => ['*'],
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true,
];
