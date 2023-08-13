<?php

return [
    'SampleService' => [
        'host' => env('SAMPLE_SERVER_HOST'),
        'authentication' => env('SAMPLE_SERVER_AUTHENTICATION','insecure'),
        'cert' => env('SAMPLE_SERVER_CERT','')
    ],
];