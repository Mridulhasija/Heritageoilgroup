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
require_once __DIR__ . '/../../include/getdata.php';

$getdata = new Getdata();
$conn = db();

if (!isset($_SESSION['is_admin'])) {
    header('Content-Type: application/json');
    die(json_encode(['error' => 'user is not logged in']));
}

// -------------------------------------------------------------------
// Helper: get a unique progress file path for this session (export)
// -------------------------------------------------------------------
function getExportProgressFilePath()
{
    $tmpDir = sys_get_temp_dir();
    $sessionId = session_id();
    return $tmpDir . DIRECTORY_SEPARATOR . "csv_export_progress_{$sessionId}.json";
}

// -------------------------------------------------------------------
// Helper: Write progress data to file
// -------------------------------------------------------------------
function writeExportProgress($progressFile, $percent, $msg = '', $file = '', $errors = [], $status = 'running')
{
    $data = [
        'progress' => round($percent, 1),
        'status' => $status,
        'msg' => $msg,
        'file' => $file,
        'errors' => $errors,
    ];
    file_put_contents($progressFile, json_encode($data), LOCK_EX);
}

// -------------------------------------------------------------------
// Helper: Generate unique file name
// -------------------------------------------------------------------
function getUniqueFileName($directory, $baseName, $extension)
{
    $counter = 0;
    $directory = rtrim($directory, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
    $fileName = $directory . $baseName . $extension;
    while (file_exists($fileName)) {
        $counter++;
        $fileName = $directory . $baseName . "($counter)" . $extension;
    }
    return $fileName;
}

function getUploadDirectory()
{
    return dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'upload';
}

// -------------------------------------------------------------------
// ENDPOINT: GET ?progress=true — return current progress from file
// -------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['progress'])) {
    header('Content-Type: application/json');

    $progressFile = getExportProgressFilePath();

    if (file_exists($progressFile)) {
        $content = file_get_contents($progressFile);
        $data = json_decode($content, true);
        if ($data === null) {
            echo json_encode(['progress' => 0, 'status' => 'idle', 'msg' => '', 'file' => '', 'errors' => []]);
        } else {
            if (!isset($data['status'])) {
                $data['status'] = ($data['progress'] ?? 0) >= 100 ? 'success' : 'running';
            }
            echo json_encode($data);
        }
    } else {
        echo json_encode(['progress' => 0, 'status' => 'idle', 'msg' => '', 'file' => '', 'errors' => []]);
    }
    exit();
}

// -------------------------------------------------------------------
// ENDPOINT: POST — start the CSV export
// -------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['table'])) {
    header('Content-Type: application/json');

    $table = $_POST['table'];
    $progressFile = getExportProgressFilePath();

    // Validate table name
    if (empty($table)) {
        echo json_encode(['status' => 'error', 'msg' => 'Table name is required.']);
        exit();
    }

    if (!preg_match('/^[A-Za-z0-9_]+$/', $table)) {
        echo json_encode(['status' => 'error', 'msg' => 'Invalid table name.']);
        exit();
    }

    // Validate table exists
    $tableEscaped = $conn->real_escape_string($table);
    $tableCheck = $conn->query("SHOW TABLES LIKE '{$tableEscaped}'");
    if (!$tableCheck || $tableCheck->num_rows === 0) {
        echo json_encode(['status' => 'error', 'msg' => "Table '$table' does not exist."]);
        exit();
    }

    // Reset progress
    writeExportProgress($progressFile, 0, 'Starting export...', '', [], 'running');

    // Fast fail for empty tables so the frontend can show a proper error.
    $countResult = $conn->query("SELECT COUNT(*) AS total FROM `{$table}`");
    if (!$countResult) {
        $msg = 'Export failed: Query error while checking record count.';
        writeExportProgress($progressFile, 100, $msg, '', ['Query failed: ' . $conn->error], 'error');
        echo json_encode(['status' => 'error', 'msg' => $msg]);
        exit();
    }
    $countRow = $countResult->fetch_assoc();
    $total = isset($countRow['total']) ? (int)$countRow['total'] : 0;
    $countResult->free();
    if ($total <= 0) {
        $msg = 'No records found. Nothing to export.';
        writeExportProgress($progressFile, 100, $msg, '', [], 'error');
        echo json_encode(['status' => 'error', 'msg' => $msg]);
        exit();
    }

    // Release session so progress polling works
    session_write_close();

    // Set time limit and keep alive
    set_time_limit(300);
    ignore_user_abort(true);

    // Send immediate response
    if (ob_get_level() === 0) {
        ob_start();
    }

    echo json_encode(['status' => 'started', 'msg' => 'Export started.']);

    // Calculate content length for proper flush
    $size = ob_get_length();
    header("Content-Length: $size");
    header('Connection: close');

    // Flush all output buffers
    while (ob_get_level() > 0) {
        ob_end_flush();
    }
    flush();

    // For FastCGI environments
    if (function_exists('fastcgi_finish_request')) {
        fastcgi_finish_request();
    }

    // Do the actual export
    exportSqlToCsv($conn, $table, $progressFile);
    exit();
}

// If we reach here, no valid request was made
header('Content-Type: application/json');
echo json_encode(['error' => 'Invalid request.']);
exit();

// ===================================================================
// EXPORT LOGIC
// ===================================================================

function exportSqlToCsv($conn, $table, $progressFile)
{
    $errors = [];

    // Custom error handler
    set_error_handler(function ($errno, $errstr, $errfile, $errline) use (&$errors) {
        $errors[] = "PHP [{$errno}]: {$errstr} in {$errfile}:{$errline}";
        error_log("Export PHP Error [{$errno}]: {$errstr} in {$errfile}:{$errline}");
        return true;
    });

    try {
        $uploadDir = getUploadDirectory();
        if (!is_dir($uploadDir)) {
            $parentDir = dirname($uploadDir);
            if (!mkdir($uploadDir, 0775, true) && !is_dir($uploadDir)) {
                $errors[] = "Failed to create upload directory: {$uploadDir}";
                if (isset($parentDir) && !is_writable($parentDir)) {
                    $errors[] = "Parent directory is not writable: {$parentDir}";
                }
                if (function_exists('posix_geteuid') && function_exists('posix_getpwuid')) {
                    $u = posix_getpwuid(posix_geteuid());
                    if (is_array($u) && !empty($u['name'])) {
                        $errors[] = "PHP process user: {$u['name']}";
                    }
                }
                writeExportProgress($progressFile, 100, 'Export failed: Could not create upload directory.', '', $errors, 'error');
                return;
            }
        }

        if (!is_writable($uploadDir)) {
            $errors[] = "Upload directory is not writable: {$uploadDir}";

            // Extra diagnostics to make server permission issues easier to fix
            $perms = @fileperms($uploadDir);
            if ($perms !== false) {
                $errors[] = 'Upload directory permissions: ' . substr(sprintf('%o', $perms), -4);
            }

            $uid = @fileowner($uploadDir);
            if ($uid !== false) {
                $ownerName = null;
                if (function_exists('posix_getpwuid')) {
                    $pw = @posix_getpwuid($uid);
                    if (is_array($pw) && !empty($pw['name'])) {
                        $ownerName = $pw['name'];
                    }
                }
                $errors[] = 'Upload directory owner: ' . ($ownerName ? "{$ownerName} ({$uid})" : (string)$uid);
            }

            $gid = @filegroup($uploadDir);
            if ($gid !== false) {
                $groupName = null;
                if (function_exists('posix_getgrgid')) {
                    $gr = @posix_getgrgid($gid);
                    if (is_array($gr) && !empty($gr['name'])) {
                        $groupName = $gr['name'];
                    }
                }
                $errors[] = 'Upload directory group: ' . ($groupName ? "{$groupName} ({$gid})" : (string)$gid);
            }

            if (function_exists('posix_geteuid') && function_exists('posix_getpwuid')) {
                $u = posix_getpwuid(posix_geteuid());
                if (is_array($u) && !empty($u['name'])) {
                    $errors[] = "PHP process user: {$u['name']}";
                }
            }

            // Try a write test to capture the underlying filesystem error
            $testFile = rtrim($uploadDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . '.write_test_' . bin2hex(random_bytes(6));
            $writeOk = @file_put_contents($testFile, 'ok', LOCK_EX);
            if ($writeOk === false) {
                $last = error_get_last();
                if (is_array($last) && !empty($last['message'])) {
                    $errors[] = 'Write test failed: ' . $last['message'];
                } else {
                    $errors[] = 'Write test failed: unknown error';
                }
            } else {
                @unlink($testFile);
            }

            writeExportProgress($progressFile, 100, 'Export failed: Upload directory is not writable.', '', $errors, 'error');
            return;
        }

        writeExportProgress($progressFile, 5, 'Querying database...', '', $errors, 'running');

        // Fetch data
        $query = "SELECT * FROM `$table`";
        $result = mysqli_query($conn, $query);

        if (!$result) {
            $errors[] = 'Query failed: ' . mysqli_error($conn);
            writeExportProgress($progressFile, 100, 'Export failed: Query error.', '', $errors, 'error');
            return;
        }

        $totalRows = mysqli_num_rows($result);
        if ($totalRows === 0) {
            writeExportProgress($progressFile, 100, 'No records found. Nothing to export.', '', $errors, 'error');
            return;
        }

        writeExportProgress($progressFile, 10, "Exporting $totalRows rows...", '', $errors, 'running');

        // Get unique file name
        $baseName = $table;
        $extension = '.csv';
        $csvFileName = getUniqueFileName($uploadDir, $baseName, $extension);

        // Open file for writing
        $csvFile = fopen($csvFileName, 'w');
        if (!$csvFile) {
            $errors[] = 'Failed to create CSV file.';
            writeExportProgress($progressFile, 100, 'Export failed: Could not create file.', '', $errors, 'error');
            return;
        }

        // Write column headers
        $columns = [];
        while ($fieldinfo = mysqli_fetch_field($result)) {
            $columns[] = $fieldinfo->name;
        }
        fputcsv($csvFile, $columns, ',', '"', '\\', "\n");

        // Write data rows with progress updates
        $rowCount = 0;
        while ($row = mysqli_fetch_assoc($result)) {
            fputcsv($csvFile, $row, ',', '"', '\\', "\n");
            $rowCount++;

            // Update progress every 50 rows to reduce file I/O
            if ($rowCount % 50 === 0 || $rowCount === $totalRows) {
                $percent = 10 + ($rowCount / $totalRows) * 80; // 10-90%
                writeExportProgress(
                    $progressFile,
                    $percent,
                    "Exported $rowCount of $totalRows rows...",
                    '',
                    $errors,
                    'running'
                );
            }
        }

        fclose($csvFile);
        mysqli_free_result($result);

        writeExportProgress($progressFile, 92, 'Saving to media library...', '', $errors, 'running');

        // Insert file record into tbl_media
        $fileBaseName = basename($csvFileName);
        $checkStmt = $conn->prepare("SELECT COUNT(*) as count FROM tbl_media WHERE file_name = ?");
        if ($checkStmt) {
            $checkStmt->bind_param('s', $fileBaseName);
            $checkStmt->execute();
            $checkResult = $checkStmt->get_result();
            $checkRow = $checkResult->fetch_assoc();
            $checkStmt->close();

            if ($checkRow['count'] == 0) {
                $stmt = $conn->prepare("INSERT INTO tbl_media (file_name, `type`) VALUES (?, ?)");
                if ($stmt) {
                    $type = 'csv';
                    $stmt->bind_param('ss', $fileBaseName, $type);
                    if (!$stmt->execute()) {
                        $errors[] = 'Failed to save media record: ' . $stmt->error;
                    }
                    $stmt->close();
                } else {
                    $errors[] = 'Failed to prepare media insert: ' . $conn->error;
                }
            }
        }

        // Build the download path
        $downloadPath = media_folder . $fileBaseName;

        // Final progress
        if (empty($errors)) {
            $msg = "Export completed successfully. $rowCount rows exported.";
        } else {
            $msg = "Export completed with " . count($errors) . " warning(s). $rowCount rows exported.";
        }

        writeExportProgress($progressFile, 100, $msg, $downloadPath, $errors, 'success');
    } catch (Exception $e) {
        $errors[] = 'Exception: ' . $e->getMessage();
        error_log('Export exception: ' . $e->getMessage());
        writeExportProgress($progressFile, 100, 'Export failed: ' . $e->getMessage(), '', $errors, 'error');
    } catch (\Throwable $t) {
        $errors[] = 'Fatal: ' . $t->getMessage();
        error_log('Export fatal: ' . $t->getMessage());
        writeExportProgress($progressFile, 100, 'Export failed: ' . $t->getMessage(), '', $errors, 'error');
    }

    restore_error_handler();
}
