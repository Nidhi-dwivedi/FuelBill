<?php
// Application configuration
define('APP_NAME', 'Petrofy');
define('APP_URL', 'http://localhost/petrol-pump-system');
define('APP_TIMEZONE', 'Asia/Kolkata');

// Email configuration
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'anonymousu2625@gmail.com');
define('SMTP_PASSWORD', 'cuqc ovud oiaw mcxf');
define('SMTP_FROM_EMAIL', 'noreply@petrolpump.com');
define('SMTP_FROM_NAME', 'Petrol Pump Billing');

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>