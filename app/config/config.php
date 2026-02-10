<?php
return [
    // ===== Database Configuration =====
    'DB_HOST' => 'db',        // Docker service name for MySQL
    'DB_NAME' => 'reidhub',
    'DB_USER' => 'reidhubuser',
    'DB_PASS' => 'reidhubpass',

    // ===== Email Configuration =====
    // For development, uses PHP's native mail() function
    // For production, switch to SMTP (Mailgun, SendGrid, etc.)
    'MAIL_DRIVER' => 'mail',  // 'mail' (native) or 'smtp'
    'MAIL_FROM_EMAIL' => 'noreply@reidhub.com',
    'MAIL_FROM_NAME' => 'ReidHub',

    // SMTP Configuration (only if MAIL_DRIVER is 'smtp')
    'SMTP_HOST' => env('SMTP_HOST', 'smtp.mailtrap.io'),
    'SMTP_PORT' => env('SMTP_PORT', 587),
    'SMTP_USERNAME' => env('SMTP_USERNAME', ''),
    'SMTP_PASSWORD' => env('SMTP_PASSWORD', ''),
    'SMTP_ENCRYPTION' => env('SMTP_ENCRYPTION', 'tls'), // 'tls' or 'ssl'

    // ===== OTP Configuration =====
    'OTP_LENGTH' => 6,              // Number of digits in OTP
    'OTP_EXPIRY_MINUTES' => 10,     // OTP validity period in minutes
    'OTP_MAX_ATTEMPTS' => 5,        // Maximum failed verification attempts
    'OTP_RESEND_WAIT_SECONDS' => 30, // Wait time before allowing resend
];

// Helper function to get environment variables (for future use)
function env($key, $default = null) {
    return $_ENV[$key] ?? $default;
}