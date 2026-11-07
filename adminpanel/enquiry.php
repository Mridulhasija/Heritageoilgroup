<?php
require_once __DIR__ . '/components/config-header.php';

$conn = db();

$page_title = "Enquiry";
$page_subtitle = "Management";
$table = "tbl_enquiry";

$id_col = 'id';
$id_type = "i";

$tableContent = [
    "Name" => "name",
    "Phone Number" => "phone",
    "Email" => "email",
    "Location" => "location",
    "Message" => "message",
    "Created at" => "created_at",
];

// Define the actions and their enable/disable status
$actions = [
    'approve' => false,
    'edit' => false,
    'delete' => true
];

$currentUrl = $_SERVER['PHP_SELF'];


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_GET['action'])) {
    try {
        $action = $_GET['action'];
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
        }

        include('components/quick-process.php');
    } catch (Exception $e) {
        $_SESSION["error-msg"] = "Error: " . $e->getMessage();
    }
    exit();
} else {
    //show page
    include("components/header.php");
    include("components/manage.php");
}
