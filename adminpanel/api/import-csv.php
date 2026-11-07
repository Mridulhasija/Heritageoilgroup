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
require_once __DIR__ . '/../utils.php';

$utils = new utils();
$getdata = new Getdata();
$conn = db();

if (!isset($_SESSION['is_admin'])) {
    header('Content-Type: application/json');
    die(json_encode(['error' => 'user is not logged in']));
}

// -------------------------------------------------------------------
// Helper: get a unique progress file path for this session
// -------------------------------------------------------------------
function getProgressFilePath()
{
    $tmpDir = sys_get_temp_dir();
    $sessionId = session_id();
    return $tmpDir . DIRECTORY_SEPARATOR . "csv_import_progress_{$sessionId}.json";
}

// -------------------------------------------------------------------
// Helper: Write progress data to file (avoids session locking issues)
// -------------------------------------------------------------------
function writeProgress($progressFile, $percent, $msg = '', $errors = [], $stats = [])
{
    $data = [
        'progress' => round($percent, 1),
        'msg' => $msg,
        'errors' => $errors,
        'stats' => $stats,
    ];
    file_put_contents($progressFile, json_encode($data), LOCK_EX);
}

// -------------------------------------------------------------------
// ENDPOINT: GET ?progress=true — return current progress from file
// -------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['progress'])) {
    header('Content-Type: application/json');

    $progressFile = getProgressFilePath();

    if (file_exists($progressFile)) {
        $content = file_get_contents($progressFile);
        $data = json_decode($content, true);
        if ($data === null) {
            echo json_encode(['progress' => 0, 'msg' => '', 'errors' => [], 'stats' => []]);
        } else {
            echo json_encode($data);
        }
    } else {
        echo json_encode(['progress' => 0, 'msg' => '', 'errors' => [], 'stats' => []]);
    }
    exit();
}

// -------------------------------------------------------------------
// ENDPOINT: POST — start the CSV import
// -------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['csvFilePath']) && isset($_POST['tableName'])) {
    header('Content-Type: application/json');

    $csvFilePath = "../../upload/" . basename($_POST['csvFilePath']);
    $tableName = $_POST['tableName'];
    $progressFile = getProgressFilePath();

    // Validate inputs
    if (empty($csvFilePath) || empty($tableName)) {
        echo json_encode(['status' => 'error', 'msg' => 'Missing CSV file path or table name.']);
        exit();
    }

    if (!file_exists($csvFilePath)) {
        echo json_encode(['status' => 'error', 'msg' => 'CSV file not found: ' . basename($_POST['csvFilePath'])]);
        exit();
    }

    // Validate table exists
    $tableCheck = $conn->query("SHOW TABLES LIKE '$tableName'");
    if (!$tableCheck || $tableCheck->num_rows === 0) {
        echo json_encode(['status' => 'error', 'msg' => "Table '$tableName' does not exist."]);
        exit();
    }

    // Reset progress file
    writeProgress($progressFile, 0, 'Starting import...', []);

    // Release session lock so progress polling works during import
    session_write_close();

    // Set generous time limit for large imports
    set_time_limit(600);
    ignore_user_abort(true);

    // Send immediate response to client so JS can start polling
    // Start output buffering if not already active
    if (ob_get_level() === 0) {
        ob_start();
    }

    echo json_encode(['status' => 'started', 'msg' => 'Import started.']);

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

    // Now do the actual import work
    importCSVtoTable($csvFilePath, $tableName, $progressFile, $conn, $utils);
    exit();
}

// If we reach here, no valid request was made
header('Content-Type: application/json');
echo json_encode(['error' => 'Invalid request.']);
exit();

// ===================================================================
// IMPORT LOGIC
// ===================================================================

function getColumnTypes($conn, $tableName)
{
    $columnTypes = [];
    $result = $conn->query("DESCRIBE `$tableName`");

    if (!$result) {
        error_log("Failed to DESCRIBE table '$tableName': " . $conn->error);
        return $columnTypes;
    }

    while ($row = $result->fetch_assoc()) {
        $columnTypes[$row['Field']] = [
            'type' => $row['Type'],
            'null' => $row['Null'] === 'YES',
            'default' => $row['Default'],
            'extra' => $row['Extra'],
        ];
    }

    return $columnTypes;
}

function processValue($value, $columnInfo, $conn)
{
    $type = strtolower($columnInfo['type']);
    $hasDefault = ($columnInfo['default'] !== null || !empty($columnInfo['extra']));

    // If value is empty string and column allows NULL
    if ($value === '' && $columnInfo['null']) {
        return 'NULL';
    }

    // If value is empty string but column doesn't allow NULL, handle based on type
    if ($value === '') {
        // If column has a default, let DB use it
        if ($hasDefault) {
            return 'DEFAULT';
        }

        // For numeric types, return 0
        if (
            strpos($type, 'int') !== false ||
            strpos($type, 'decimal') !== false ||
            strpos($type, 'float') !== false ||
            strpos($type, 'double') !== false
        ) {
            return '0';
        }

        // For other types, return empty string (quoted)
        return "''";
    }

    // For non-empty values, return escaped and quoted
    return "'" . $conn->real_escape_string($value) . "'";
}

function importCSVtoTable($filePath, $tableName, $progressFile, $conn, $utils)
{
    $errors = [];
    $stats = ['total' => 0, 'inserted' => 0, 'skipped' => 0, 'failed_batches' => 0];

    // Custom error handler to catch PHP warnings/notices
    set_error_handler(function ($errno, $errstr, $errfile, $errline) use (&$errors) {
        $errors[] = "PHP [{$errno}]: {$errstr} in {$errfile}:{$errline}";
        error_log("Import PHP Error [{$errno}]: {$errstr} in {$errfile}:{$errline}");
        return true; // Don't execute the standard error handler
    });

    try {
        if ($conn->connect_error) {
            writeProgress($progressFile, 100, 'Database connection error: ' . $conn->connect_error, $errors, $stats);
            return;
        }

        // Get column information from the database
        $columnTypes = getColumnTypes($conn, $tableName);
        if (empty($columnTypes)) {
            writeProgress($progressFile, 100, "Could not retrieve column info for table '$tableName'.", $errors, $stats);
            return;
        }

        $dbColumns = array_keys($columnTypes);

        // Open CSV file
        $handle = fopen($filePath, 'r');
        if ($handle === false) {
            writeProgress($progressFile, 100, 'Failed to open CSV file: ' . basename($filePath), $errors, $stats);
            return;
        }

        writeProgress($progressFile, 2, 'Reading CSV file...', $errors, $stats);

        // Count total rows (exclude header)
        $totalRows = 0;
        while (fgetcsv($handle, 0, ',', '"', '\\') !== false) {
            $totalRows++;
        }
        $totalRows--; // subtract header
        rewind($handle);

        if ($totalRows <= 0) {
            fclose($handle);
            writeProgress($progressFile, 100, 'CSV file is empty or contains only a header row.', $errors, $stats);
            return;
        }

        $stats['total'] = $totalRows;
        writeProgress($progressFile, 5, "Found $totalRows rows to import...", $errors, $stats);

        // Read header columns
        $csvColumns = fgetcsv($handle, 0, ',', '"', '\\');
        if ($csvColumns === false) {
            fclose($handle);
            writeProgress($progressFile, 100, 'Failed to read CSV header row.', $errors, $stats);
            return;
        }

        // Trim BOM and whitespace from column names
        $csvColumns = array_map(function ($col) {
            return trim($col, "\xEF\xBB\xBF \t\n\r\0\x0B");
        }, $csvColumns);

        // Validate CSV columns against database columns
        $validColumns = [];
        $validColumnIndices = [];
        $invalidColumns = [];

        foreach ($csvColumns as $index => $col) {
            if (in_array($col, $dbColumns)) {
                $validColumns[] = $col;
                $validColumnIndices[] = $index;
            } else {
                $invalidColumns[] = $col;
            }
        }

        if (!empty($invalidColumns)) {
            $errors[] = "Warning: The following CSV columns do not exist in table '$tableName' and will be skipped: " . implode(', ', $invalidColumns);
        }

        if (empty($validColumns)) {
            fclose($handle);
            writeProgress($progressFile, 100, 'No valid columns found in CSV that match the database table.', $errors, $stats);
            return;
        }

        // Build backticked column list for INSERT
        $backtickColumns = array_map(function ($col) {
            return "`$col`";
        }, $validColumns);
        $columnList = implode(", ", $backtickColumns);

        // Build ON DUPLICATE KEY UPDATE clause
        // Exclude 'url' (preserve original URLs) and 'created_at' (preserve original creation time)
        $skipOnUpdate = ['url', 'created_at'];
        $updateParts = [];
        foreach ($validColumns as $column) {
            if (in_array($column, $skipOnUpdate)) {
                continue;
            }
            $updateParts[] = "`$column` = VALUES(`$column`)";
        }
        $onDuplicateClause = !empty($updateParts) ? " ON DUPLICATE KEY UPDATE " . implode(", ", $updateParts) : "";

        // Identify special column indices within validColumns
        $titleIdx = array_search('title', $csvColumns);
        $slugIdx = array_search('slug', $csvColumns);

        $batchSize = 50; // Smaller batches for better progress granularity
        $rows = [];
        $allRowData = []; // Store raw data for media processing
        $processedRows = 0;

        while (($data = fgetcsv($handle, 0, ',', '"', '\\')) !== false) {
            $processedRows++;

            // Generate slug from title if slug is empty
            if ($slugIdx !== false && $titleIdx !== false && empty($data[$slugIdx]) && !empty($data[$titleIdx])) {
                $slug = $utils->stringToUrl($data[$titleIdx]);
                $original_slug = $slug;
                $counter = 1;

                while (true) {
                    $stmt = $conn->prepare("SELECT id FROM `$tableName` WHERE `slug` = ?");
                    if ($stmt) {
                        $stmt->bind_param("s", $slug);
                        $stmt->execute();
                        $stmt->store_result();
                        $num_rows = $stmt->num_rows;
                        $stmt->close();

                        if ($num_rows == 0)
                            break;
                        $slug = $original_slug . '-' . $counter;
                        $counter++;
                    } else {
                        $errors[] = "Row $processedRows: Failed to prepare slug check query: " . $conn->error;
                        break;
                    }
                }
                $data[$slugIdx] = $slug;
            }

            // Process 'url' column - generate unique URL
            foreach ($csvColumns as $idx => $col) {
                if ($col === 'url' && isset($data[$idx])) {
                    if (empty($data[$idx]) && $titleIdx !== false && !empty($data[$titleIdx])) {
                        $data[$idx] = $data[$titleIdx];
                    }
                    $data[$idx] = $utils->generateUniqueURL($conn, $tableName, $data[$idx]);
                }
            }

            // Build the VALUES tuple using only valid columns
            $processedValues = [];
            foreach ($validColumnIndices as $vi => $csvIndex) {
                $col = $validColumns[$vi];
                $value = isset($data[$csvIndex]) ? $data[$csvIndex] : '';
                $columnInfo = $columnTypes[$col];
                $processedValues[] = processValue($value, $columnInfo, $conn);
            }

            $rowValue = "(" . implode(", ", $processedValues) . ")";
            $rows[] = $rowValue;

            // Store raw data for media processing
            $rowDataAssoc = [];
            foreach ($validColumnIndices as $vi => $csvIndex) {
                $rowDataAssoc[$validColumns[$vi]] = isset($data[$csvIndex]) ? $data[$csvIndex] : '';
            }
            $allRowData[] = $rowDataAssoc;

            // Execute batch insert when batch size is reached
            if (count($rows) >= $batchSize) {
                $batchResult = executeBatchInsert($conn, $tableName, $columnList, $rows, $onDuplicateClause, $processedRows, $errors, $stats);
                $errors = $batchResult['errors'];
                $stats = $batchResult['stats'];
                $rows = [];

                // Update progress (reserve 90% for row processing, 10% for media)
                $percent = 5 + ($processedRows / $totalRows) * 85;
                writeProgress($progressFile, $percent, "Processing row $processedRows of $totalRows...", $errors, $stats);
            }
        }
        fclose($handle);

        // Insert remaining rows
        if (count($rows) > 0) {
            $batchResult = executeBatchInsert($conn, $tableName, $columnList, $rows, $onDuplicateClause, $processedRows, $errors, $stats);
            $errors = $batchResult['errors'];
            $stats = $batchResult['stats'];
        }

        writeProgress($progressFile, 92, 'Processing media records...', $errors, $stats);

        // Handle media columns (image, banner, icon)
        $mediaColumns = ['image', 'banner', 'icon'];
        $mediaCount = 0;
        foreach ($validColumns as $col) {
            if (in_array($col, $mediaColumns)) {
                foreach ($allRowData as $rowData) {
                    if (!empty($rowData[$col])) {
                        $title = isset($rowData['title']) ? $rowData['title'] : '';
                        insertIntoMediaTable($conn, $rowData[$col], $title, $errors);
                        $mediaCount++;
                    }
                }
            }
        }

        writeProgress($progressFile, 98, 'Finalizing...', $errors, $stats);

        // Build final message
        $stats['media_records'] = $mediaCount;
        if (empty($errors)) {
            $msg = "CSV import completed successfully. {$stats['inserted']} records processed.";
        } else {
            $msg = "CSV import completed with " . count($errors) . " warning(s)/error(s). {$stats['inserted']} records processed.";
        }

        writeProgress($progressFile, 100, $msg, $errors, $stats);

    } catch (Exception $e) {
        $errors[] = 'Exception: ' . $e->getMessage();
        error_log('Import exception: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
        writeProgress($progressFile, 100, 'Import failed with an exception: ' . $e->getMessage(), $errors, $stats);
    } catch (\Throwable $t) {
        $errors[] = 'Fatal: ' . $t->getMessage();
        error_log('Import fatal: ' . $t->getMessage() . ' in ' . $t->getFile() . ':' . $t->getLine());
        writeProgress($progressFile, 100, 'Import failed with a fatal error: ' . $t->getMessage(), $errors, $stats);
    }

    // Restore the default error handler
    restore_error_handler();
}

function executeBatchInsert($conn, $tableName, $columnList, $rows, $onDuplicateClause, $currentRow, $errors, $stats)
{
    $insertQuery = "INSERT INTO `$tableName` ($columnList) VALUES " . implode(", ", $rows) . $onDuplicateClause;

    $result = $conn->query($insertQuery);

    if ($result === true) {
        $affectedRows = $conn->affected_rows;
        $stats['inserted'] += $affectedRows;

        // If affected_rows is 0, something unexpected happened
        if ($affectedRows === 0) {
            $warnMsg = 'Batch executed successfully but affected 0 rows. This may mean all rows already exist with identical data, or the INSERT has no effect. Query preview: ' . substr($insertQuery, 0, 500);
            $errors[] = $warnMsg;
            error_log('[CSV Import] WARNING: ' . $warnMsg);
        }
    } else {
        $stats['failed_batches']++;
        $batchStart = $currentRow - count($rows) + 1;
        $errorMsg = "Batch insert failed (rows $batchStart-$currentRow): " . $conn->error;
        $errors[] = $errorMsg;
        error_log('[CSV Import] ' . $errorMsg);
        error_log('[CSV Import] Failed query: ' . substr($insertQuery, 0, 2000));

        // Try inserting rows individually to identify the problematic row(s)
        foreach ($rows as $idx => $row) {
            $singleQuery = "INSERT INTO `$tableName` ($columnList) VALUES $row $onDuplicateClause";
            $singleResult = $conn->query($singleQuery);
            if ($singleResult === true) {
                $singleAffected = $conn->affected_rows;
                $stats['inserted'] += ($singleAffected > 0 ? 1 : 0);
            } else {
                $rowNum = $batchStart + $idx;
                $singleError = "Row $rowNum insert failed: " . $conn->error;
                $errors[] = $singleError;
                error_log('[CSV Import] ' . $singleError);
                error_log('[CSV Import] Failed single query: ' . substr($singleQuery, 0, 1000));
                $stats['skipped']++;
            }
        }
    }

    return ['errors' => $errors, 'stats' => $stats];
}

function insertIntoMediaTable($conn, $fileName, $altText, &$errors)
{
    // Check if filename already exists in the database
    $checkStmt = $conn->prepare("SELECT COUNT(*) as count FROM tbl_media WHERE file_name = ?");
    if (!$checkStmt) {
        $errors[] = "Media check prepare failed for '$fileName': " . $conn->error;
        return;
    }

    $checkStmt->bind_param('s', $fileName);
    $checkStmt->execute();
    $result = $checkStmt->get_result();
    $row = $result->fetch_assoc();
    $checkStmt->close();

    // If filename already exists, skip insertion
    if ($row && $row['count'] > 0) {
        return;
    }

    // Prepare and execute the insert
    $stmt = $conn->prepare("INSERT INTO tbl_media (file_name, `type`, `description`) VALUES (?, ?, ?)");
    if (!$stmt) {
        $errors[] = "Media insert prepare failed for '$fileName': " . $conn->error;
        return;
    }

    $type = 'image';
    $stmt->bind_param('sss', $fileName, $type, $altText);
    if (!$stmt->execute()) {
        $errors[] = "Media insert failed for '$fileName': " . $stmt->error;
    }
    $stmt->close();
}
