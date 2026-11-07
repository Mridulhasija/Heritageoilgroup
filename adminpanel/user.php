<?php
require_once __DIR__ . '/components/config-header.php';

$conn = db();

$page_title = "Users";
$page_subtitle = "Management";
$table = "tbl_user";

$tableContent = [
    "Username" => "username",
    "Name" => "first_name last_name",
    "Email" => "email",
    "Admin" => "boolean is_admin",
    "Email Verified" => "boolean is_email_verified",
];

$id_col = 'user_id';
$id_type = "s";

// Define the actions and their enable/disable status
$actions = [
    'approve' => false,
    'add' => true,
    'edit' => true,
    'delete' => true
];

$editor_url = "user-editor.php";
$currentUrl = $_SERVER['PHP_SELF'];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_GET['action'])) {
    try {
        $action = $_GET['action'];
        $id = isset($_GET['id']) ? $_GET['id'] : null;

        include('components/quick-process.php');

        if ($action === "add" || $action === "edit") {
            $username = isset($_POST['username']) ? trim($_POST['username']) : null;
            $first_name = isset($_POST['first_name']) ? trim($_POST['first_name']) : null;
            $last_name = isset($_POST['last_name']) ? trim($_POST['last_name']) : null;
            $phone_number = isset($_POST['phone_number']) ? trim($_POST['phone_number']) : null;
            $email = isset($_POST['email']) ? trim($_POST['email']) : null;
            $country = isset($_POST['country']) ? trim($_POST['country']) : null;
            $address_1 = isset($_POST['address_1']) ? trim($_POST['address_1']) : null;
            $address_2 = isset($_POST['address_2']) ? trim($_POST['address_2']) : null;
            $city = isset($_POST['city']) ? trim($_POST['city']) : null;
            $state = isset($_POST['state']) ? trim($_POST['state']) : null;
            $zip_code = isset($_POST['zip_code']) ? trim($_POST['zip_code']) : null;
            $is_email_verified = isset($_POST['is_email_verified']) ? trim($_POST['is_email_verified']) : 0;
            $is_admin = isset($_POST['is_admin']) ? trim($_POST['is_admin']) : 0;

            $stmt = null;

            if ($action == 'add') {
                $stmt = $conn->prepare(
                    "INSERT INTO $table (username, first_name, last_name, phone_number, email, country, address_1, address_2, city, state, zip_code, is_email_verified, is_admin) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
                );
            } elseif ($action == 'edit' && isset($id)) {
                $stmt = $conn->prepare(
                    "UPDATE $table SET username = ?, first_name = ?, last_name = ?, phone_number = ?, email = ?, country = ?, address_1 = ?, address_2 = ?, city = ?, state = ?, zip_code = ?, is_email_verified = ?, is_admin = ? WHERE $id_col = ?"
                );
            }

            if (!$stmt) {
                throw new Exception("Error preparing statement: " . $conn->error);
            }

            if ($action == 'add') {
                $stmt->bind_param("sssssssssssii", $username, $first_name, $last_name, $phone_number, $email, $country, $address_1, $address_2, $city, $state, $zip_code, $is_email_verified, $is_admin);
            } elseif ($action == 'edit' && isset($id)) {
                $stmt->bind_param("sssssssssssiis", $username, $first_name, $last_name, $phone_number, $email, $country, $address_1, $address_2, $city, $state, $zip_code, $is_email_verified, $is_admin, $id);
            }

            if (!$stmt->execute()) {
                throw new Exception("Error executing statement: " . $stmt->error);
            }

            $stmt->close();

            // Fetch the user_id of new record
            if ($action == 'add') {
                $selectStmt = $conn->prepare("SELECT user_id FROM $table WHERE username = ?");
                if (!$selectStmt)
                    throw new Exception("Error preparing statement: " . $conn->error);

                $selectStmt->bind_param("s", $username);
                if (!$selectStmt->execute())
                    throw new Exception("Error executing statement: " . $selectStmt->error);

                $selectStmt->bind_result($fetchedUserId);
                if ($selectStmt->fetch()) {
                    $id = $fetchedUserId;
                } else {
                    throw new Exception("User not found after insertion.");
                }
                $selectStmt->close();
            }

            //password update
            $password = isset($_POST['password']) ? trim($_POST['password']) : null;
            if ($password) {
                $stmt = null;

                $confirm_password = isset($_POST['confirm_password']) ? trim($_POST['confirm_password']) : null;
                if ($password !== $confirm_password) {
                    die("Passwords do not match");
                }

                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                $stmt = $conn->prepare(
                    "UPDATE $table SET password_hash = ? WHERE $id_col = ?"
                );

                if (!$stmt) {
                    throw new Exception("Error preparing statement: " . $conn->error);
                }

                $stmt->bind_param("ss", $hashed_password, $id);

                if (!$stmt->execute()) {
                    throw new Exception("Error executing statement: " . $stmt->error);
                }
                $stmt->close();
            }

            $_SESSION["success-msg"] = $page_title . " successfully " . ($action == 'add' ? 'added' : 'edited');
        }
    } catch (Exception $e) {
        $_SESSION["error-msg"] = "Error: " . $e->getMessage();
    }
    header('Location: ' . strtok($currentUrl, '?'));
    exit();
} else {
    //show page
    include("components/header.php");
    include("components/manage.php");
}
