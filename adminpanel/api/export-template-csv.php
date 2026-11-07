<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/error_log.txt');

session_set_cookie_params([
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Strict',
]);

session_start();
require_once __DIR__ . '/../../include/connection.php';

$conn = db();

if (!isset($_SESSION['is_admin'])) {
    http_response_code(401);
    header('Content-Type: text/plain; charset=utf-8');
    echo "Unauthorized";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    header('Content-Type: text/plain; charset=utf-8');
    echo "Method not allowed";
    exit();
}

$table = isset($_GET['table']) ? trim((string)$_GET['table']) : '';
if ($table === '') {
    http_response_code(400);
    header('Content-Type: text/plain; charset=utf-8');
    echo "Missing table parameter.";
    exit();
}

if (!preg_match('/^[A-Za-z0-9_]+$/', $table)) {
    http_response_code(400);
    header('Content-Type: text/plain; charset=utf-8');
    echo "Invalid table name.";
    exit();
}

$tableEscaped = $conn->real_escape_string($table);
$tableCheck = $conn->query("SHOW TABLES LIKE '{$tableEscaped}'");
if (!$tableCheck || $tableCheck->num_rows === 0) {
    http_response_code(404);
    header('Content-Type: text/plain; charset=utf-8');
    echo "Table not found.";
    exit();
}

$result = $conn->query("DESCRIBE `{$table}`");
if (!$result) {
    http_response_code(500);
    header('Content-Type: text/plain; charset=utf-8');
    echo "Failed to fetch table columns.";
    exit();
}

$columns = [];
while ($row = $result->fetch_assoc()) {
    if (isset($row['Field'])) {
        $columns[] = $row['Field'];
    }
}
$result->free();

if (empty($columns)) {
    http_response_code(500);
    header('Content-Type: text/plain; charset=utf-8');
    echo "No columns found for table.";
    exit();
}

$filename = $table . '_template.csv';
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

$out = fopen('php://output', 'w');
fputcsv($out, $columns, ',', '"', '\\', "\n");
fclose($out);
exit();

