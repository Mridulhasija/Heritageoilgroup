<?php
require_once __DIR__ . '/components/config-header.php';

$conn = db();

$page_title = "Page";
$page_subtitle = "Management";
$table = "tbl_page";

$id_col = 'id';
$id_type = "i";

$tableContent = [
    "Title" => "title",
    "URL" => "url",
    "Status" => "boolean status",
];

// Define the actions and their enable/disable status
$actions = [
    'approve' => false,
    'add' => true,
    'edit' => true,
    'delete' => true
];

$editor_url = "page-editor.php";
$currentUrl = $_SERVER['PHP_SELF'];


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_GET['action'])) {
    try {
        $action = $_GET['action'];
        $id = isset($_GET['id']) ? $_GET['id'] : null;

        include('components/quick-process.php');

        if ($action === "add" || $action === "edit") {
            $title = isset($_POST['title']) ? trim($_POST['title']) : null;
            $url = isset($_POST['url']) ? trim($_POST['url']) : null;
            $url = empty($url) ? $title : $url; // if empty then use title as url
            $url = $utils->stringToUrl($url);
            $meta_title = isset($_POST['meta_title']) ? trim($_POST['meta_title']) : null;
            $meta_title = empty($meta_title) ? $title : $meta_title; // if empty then use title as meta_title
            $meta_keyword = isset($_POST['meta_keyword']) ? trim($_POST['meta_keyword']) : null;
            $meta_desc = isset($_POST['meta_desc']) ? trim($_POST['meta_desc']) : null;
            $description = isset($_POST['description']) ? trim($_POST['description']) : null;
            $image = isset($_POST['image']) ? trim($_POST['image']) : null;
            $status = isset($_POST['status']) ? trim($_POST['status']) : null;
            $stmt = null;

            if ($action == 'add') {
                // Generate unique URL
                $url = $utils->generateUniqueURL($conn, $table, $url);
                $stmt = $conn->prepare(
                    "INSERT INTO $table (title, url, meta_title, meta_keyword, meta_desc, description, image, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
                );
            } elseif ($action == 'edit' && isset($id)) {
                $existing_url = $utils->getURLById($conn, $table, $id);
                if ($url != $existing_url) {
                    // Generate a unique URL if the provided URL is different
                    $url = $utils->generateUniqueURL($conn, $table, $url);
                }
                $stmt = $conn->prepare(
                    "UPDATE $table SET title = ?, url = ?, meta_title = ?, meta_keyword = ?, meta_desc = ?, description = ?, image = ?, status = ? WHERE id = ?"
                );
            }

            if (!$stmt) {
                throw new Exception("Error preparing statement: " . $conn->error);
            }

            if ($action == 'add') {
                $stmt->bind_param("sssssssi", $title, $url, $meta_title, $meta_keyword, $meta_desc, $description, $image, $status);
            } elseif ($action == 'edit' && isset($id)) {
                $stmt->bind_param("sssssssii", $title, $url, $meta_title, $meta_keyword, $meta_desc, $description, $image, $status, $id);
            }

            if (!$stmt->execute()) {
                throw new Exception("Error executing statement: " . $stmt->error);
            }

            $insertResult = $stmt->affected_rows > 0;
            $stmt->close();

            $_SESSION["success-msg"] = $page_title . " successfully " . ($action == 'add' ? 'added' : 'edited');

            header('Location: ' . strtok($currentUrl, '?'));
            exit();
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
