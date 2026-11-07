<?php
error_reporting(0);
error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', 'error_log.txt');

// Display errors on the page
// ini_set('display_errors', 1);

session_set_cookie_params([
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Strict',
]);
session_start();
require_once __DIR__ . '/../include/connection.php';
require_once __DIR__ . '/../include/getdata.php';
$getdata = new Getdata();

$getdata->logout();

// Redirect to the home page
header('Location: '.SITE_URL);
exit();
?>
