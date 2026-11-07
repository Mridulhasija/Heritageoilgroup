<?php
require_once __DIR__ . '/components/config-header.php';

$conn = db();

$page_title = "Testimonial";
$page_subtitle = "Management";
$table = "tbl_testimonial";

$id_col = 'id';
$id_type = "i";

$tableContent = [
    "Name" => "name",
    "<center><i class='fa fa-picture-o' aria-hidden='true'></i></center>" => "image",
    "Designation" => "designation",
    "Status" => "boolean status",
];

// Define the actions and their enable/disable status
$actions = [
    'approve' => false,
    'add' => true,
    'edit' => true,
    'delete' => true
];

$editor_url = "testimonial-editor.php";
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
            $designation = isset($_POST['designation']) ? trim($_POST['designation']) : null;
            $company = isset($_POST['company']) ? trim($_POST['company']) : null;
            $description = isset($_POST['description']) ? trim($_POST['description']) : null;
            $image = isset($_POST['image']) ? trim($_POST['image']) : null;
            $status = isset($_POST['status']) ? trim($_POST['status']) : null;
            $stmt = null;

            if ($action == 'add') {
                $stmt = $conn->prepare(
                    "INSERT INTO $table (name, designation, company, description, image, status) VALUES (?, ?, ?, ?, ?, ?)"
                );

                $stmt->bind_param("sssssi", $name, $designation, $company, $description, $image, $status);
            } elseif ($action == 'edit' && isset($id)) {
                $stmt = $conn->prepare(
                    "UPDATE $table SET name = ?, designation = ?, company = ?, description = ?, image = ?, status = ? WHERE id = ?"
                );

                $stmt->bind_param("sssssii", $name, $designation, $company, $description, $image, $status, $id);
            }

            if ($stmt) {
                if ($stmt->execute()) {
                    $insertResult = $stmt->affected_rows > 0;

                    $_SESSION["success-msg"] = $insertResult ? "$page_title successfully " . ($action == 'add' ? 'added' : 'edited') : "Error in " . ($action == 'add' ? 'adding' : 'editing') . " page";
                } else {
                    // Error occurred during execution
                    $_SESSION["success-msg"] = "Error executing statement: " . $stmt->error;
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
