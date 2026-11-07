<?php
error_reporting(0);
error_reporting(E_ALL);

//// Display errors on the page
// ini_set('display_errors', 1);
session_start();

// Set error logging to file
ini_set('log_errors', 1);
ini_set('error_log', "error.txt");

require_once __DIR__ . '/../../include/connection.php';
require_once __DIR__ . '/../utils.php';
$utils = new utils();

require_once __DIR__ . '/../../include/getdata.php';
$getdata = new Getdata();

//remember cookies check
$getdata->CheckRememberMe();

if (!isset($_SESSION['is_admin'])) {
    header("location: index.php");
    exit();
}

$selsql = $utils->get_webinfo();
$resweb = mysqli_fetch_object($selsql);
$website_title = $resweb->name;
$user_name = $getdata->FetchUsername($_SESSION['user_id']);

$media_folder = media_folder;
