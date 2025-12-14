<?php

return [
    'app_name' => 'User Management System',
    'base_url' => getenv('BASE_URL') ?: 'http://localhost',
    'timezone' => 'Europe/Minsk',
    'session_lifetime' => 3600, // 1 час
    'verification_code_length' => 6,
    'verification_code_expiry' => 3600, // 1 час
    'password_reset_expiry' => 3600, // 1 час
    'upload_path' => __DIR__ . '/../public/uploads/',
    'max_upload_size' => 5 * 1024 * 1024, // 5MB
];


