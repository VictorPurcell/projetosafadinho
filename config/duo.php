<?php

return [
    'client_id' => env('DUO_CLIENT_ID'),
    'client_secret' => env('DUO_CLIENT_SECRET'),
    'api_hostname' => env('DUO_API_HOSTNAME'),
    'redirect_uri' => env('DUO_REDIRECT_URI'),
    'http_proxy' => env('DUO_HTTP_PROXY'),
    'failmode' => env('DUO_FAILMODE', 'OPEN')
];