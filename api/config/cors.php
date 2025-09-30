<?php
return [
    'paths' => ['api/*', 'storage/*'],
    'allowed_methods' => ['*'],
    'allowed_origins' => [
        'http://localhost:5173', // admin (Vite 기본)
        'http://localhost:5174', // front (두 번째 Vite 포트 가정)
        'http://localhost:3000', // 필요 시
    ],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => false,
];
