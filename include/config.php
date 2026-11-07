<?php

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../'); // Load .env from root
$dotenv->load();

// Check environment (localhost or live)
if ($_SERVER['HTTP_HOST'] == "localhost") {
    define("DB_HOST", $_ENV['DB_HOST']);
    define("DB_USER", $_ENV['DB_USER']);
    define("DB_PASSWORD", $_ENV['DB_PASSWORD']);
    define("DB_DATABASE", $_ENV['DB_DATABASE']);
    define('SITE_URL', $_ENV['LOCAL_SITE_URL']);
} else {
    define("DB_HOST", $_ENV['LIVE_DB_HOST']);
    define("DB_USER", $_ENV['LIVE_DB_USER']);
    define("DB_PASSWORD", $_ENV['LIVE_DB_PASSWORD']);
    define("DB_DATABASE", $_ENV['LIVE_DB_DATABASE']);
    define('SITE_URL', $_ENV['LIVE_SITE_URL']);
}

// Other configurations
define("media_folder", SITE_URL . $_ENV['MEDIA_FOLDER']);
define("MAIL_HOST", $_ENV['MAIL_HOST']);
define("MAIL_PORT", $_ENV['MAIL_PORT']);
define("MAIL_USER", $_ENV['MAIL_USER']);
define("MAIL_PASSWORD", $_ENV['MAIL_PASSWORD']);
