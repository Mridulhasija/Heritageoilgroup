<?php
require_once __DIR__ . '/components/config-header.php';

$conn = db();

$page_title = "Team Members";
$page_subtitle = "Management";
$table = "tbl_member";

$id_col = 'id';
$id_type = "i";

$tableContent = [
    "Name" => "name",
    "URL" => "url",
    "Status" => "status",
];

// Define the actions and their enable/disable status
$actions = [
    'approve' => false,
    'add' => true,
    'edit' => true,
    'delete' => true
];

$editor_url = "member-editor.php";
$currentUrl = $_SERVER['PHP_SELF'];


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_GET['action'])) {
    try {
        $action = $_GET['action'];
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
        }

        include('components/quick-process.php');


        if ($action === "add" || $action === "edit") {
            $name = isset($_POST['name']) ? trim($_POST['name']) : null;

            $url = isset($_POST['url']) ? trim($_POST['url']) : null;
            $url = empty($url) ? $name : $url; // if empty then use title as url
            $url = $utils->stringToUrl($url);

            $designation = isset($_POST['designation']) ? trim($_POST['designation']) : null;
            $meta_title = isset($_POST['meta_title']) ? trim($_POST['meta_title']) : null;
            $meta_keyword = isset($_POST['meta_keyword']) ? trim($_POST['meta_keyword']) : null;
            $meta_desc = isset($_POST['meta_desc']) ? trim($_POST['meta_desc']) : null;
            $description = isset($_POST['description']) ? trim($_POST['description']) : null;
            $image = isset($_POST['image']) ? trim($_POST['image']) : null;
            $status = isset($_POST['status']) ? trim($_POST['status']) : null;
            $stmt = null;

            if ($action == 'add') {
                $stmt = $conn->prepare(
                    "INSERT INTO $table (name, url, designation, meta_title, meta_keyword, meta_desc, description, image, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
                );

                $stmt->bind_param("ssssssssi", $name, $url, $designation, $meta_title, $meta_keyword, $meta_desc, $description, $image, $status);
            } elseif ($action == 'edit' && isset($id)) {
                $stmt = $conn->prepare(
                    "UPDATE $table SET name = ?, url = ?, designation = ?, meta_title = ?, meta_keyword = ?, meta_desc = ?, description = ?, image = ?, status = ? WHERE id = ?"
                );

                $stmt->bind_param("ssssssssii", $name, $url, $designation, $meta_title, $meta_keyword, $meta_desc, $description, $image, $status, $id);
            }

            if ($stmt) {
                if ($stmt->execute()) {
                    $_SESSION["success-msg"] = $page_title . " successfully ";
                } else {
                    // Error occurred during execution
                    $_SESSION["success-msg"] = $page_title . " - Error executing statement: " . $stmt->error;
                }
            } else {
                // Error occurred during preparation
                $_SESSION["success-msg"] = "Error preparing statement: " . $conn->error;
            }

            $stmt->close();

            header('Location: ' . strtok($currentUrl, '?'));
        }
    } catch (Exception $e) {
        $_SESSION["error-msg"] = "Error: " . $e->getMessage();
    }
    exit();
} else {
    //show page
    include("components/header.php");
    include("components/manage.php");
}
