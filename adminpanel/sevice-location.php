<?php
require_once __DIR__ . '/components/config-header.php';

$conn = db();

$page_title = "Sevice Locations";
$page_subtitle = "Management";
$table = "tbl_location";

$id_col = 'id';
$id_type = "i";

$tableContent = [
    "Title" => "title",
    "Status" => "boolean status",
];

// Define the actions and their enable/disable status
$actions = [
    'approve' => false,
    'add' => true,
    'edit' => true,
    'delete' => true
];

$editor_url = "sevice-location-editor.php";
$currentUrl = $_SERVER['PHP_SELF'];
$normalizeLocationValues = static function ($values): array {
    if (!is_array($values)) {
        return [];
    }
    $values = array_map(static fn($value) => trim((string) $value), $values);
    return array_values(array_unique(array_filter($values, static fn($value) => $value !== '')));
};


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_GET['action'])) {
    try {
        $action = $_GET['action'];
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
        }

        include('components/quick-process.php');


        if ($action === "add" || $action === "edit") {
            $title = isset($_POST['title']) ? trim($_POST['title']) : null;

            $url = isset($_POST['url']) ? trim($_POST['url']) : null;
            $url = empty($url) ? $title : $url; // if empty then use title as url
            $url = $utils->stringToUrl($url);

            $locationData = json_decode($_POST['data'] ?? '', true);
            if (!is_array($locationData)) {
                throw new InvalidArgumentException('Location data must be valid JSON.');
            }
            $locationData = [
                'primary_commercial_hub' => is_scalar($locationData['primary_commercial_hub'] ?? null)
                    ? trim((string) $locationData['primary_commercial_hub'])
                    : '',
                'major_sub_locations' => $normalizeLocationValues($locationData['major_sub_locations'] ?? null),
                'industrial_hubs' => $normalizeLocationValues($locationData['industrial_hubs'] ?? null),
                'trade_and_logistics_hubs' => $normalizeLocationValues($locationData['trade_and_logistics_hubs'] ?? null),
            ];
            $dataJson = json_encode($locationData, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
            $status = (int) ($_POST['status'] ?? 0);
            $stmt = null;

            if ($action == 'add') {
                $stmt = $conn->prepare(
                    "INSERT INTO $table (title, url, data, status) VALUES (?, ?, ?, ?)"
                );

                $stmt->bind_param("sssi", $title, $url, $dataJson, $status);
            } elseif ($action == 'edit' && isset($id)) {
                $stmt = $conn->prepare(
                    "UPDATE $table SET title = ?, url = ?, data = ?, status = ? WHERE id = ?"
                );

                $stmt->bind_param("sssii", $title, $url, $dataJson, $status, $id);
            }

            if ($stmt) {
                if ($stmt->execute()) {
                    $insertResult = $stmt->affected_rows > 0;

                    $_SESSION["success-msg"] = $insertResult ? "Page successfully " . ($action == 'add' ? 'added' : 'edited') : "Error in " . ($action == 'add' ? 'adding' : 'editing') . " page";
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
