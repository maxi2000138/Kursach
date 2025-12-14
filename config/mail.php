<?php

return [
    'smtp_host' => getenv('SMTP_HOST') ?: 'smtp.gmail.com',
    'smtp_port' => (int)(getenv('SMTP_PORT') ?: 587),
    'smtp_username' => getenv('SMTP_USERNAME') ?: '',
    'smtp_password' => getenv('SMTP_PASSWORD') ?: '',
    'smtp_encryption' => getenv('SMTP_ENCRYPTION') ?: 'tls',
    'from_email' => getenv('FROM_EMAIL') ?: '',
    'from_name' => getenv('FROM_NAME') ?: 'User Management System',
];


