<?php
require_once __DIR__ . '/components/config-header.php';

$conn = db();

$page_title = "Media Library";
$table = "tbl_media";

$currentUrl = $_SERVER['PHP_SELF'];

/**
 * Function to resize and convert image to WebP
 * @param string $sourcePath - Path to the original image
 * @param string $destinationPath - Path where the processed image will be saved
 * @param int $maxWidth - Maximum width for resizing (default: 1200)
 * @return bool - True if successful, false otherwise
 */
function processImage($sourcePath, $destinationPath, $maxWidth = 1200)
{
    // Get image info
    $imageInfo = getimagesize($sourcePath);
    if (!$imageInfo) {
        return false;
    }


    $originalWidth = $imageInfo[0];
    $originalHeight = $imageInfo[1];
    $mimeType = $imageInfo['mime'];
    if ($mimeType !== 'image/jpeg' && $mimeType !== 'image/png') {
        return false;
    }


    // Calculate new dimensions while maintaining aspect ratio
    if ($originalWidth > $maxWidth) {
        $newWidth = $maxWidth;
        $newHeight = intval(($originalHeight * $maxWidth) / $originalWidth);
    } else {
        $newWidth = $originalWidth;
        $newHeight = $originalHeight;
    }

    // Create image resource from source
    switch ($mimeType) {
        case 'image/jpeg':
            $sourceImage = imagecreatefromjpeg($sourcePath);
            break;
        case 'image/png':
            $sourceImage = imagecreatefrompng($sourcePath);
            break;
        case 'image/bmp':
            $sourceImage = imagecreatefrombmp($sourcePath);
            break;
        case 'image/webp':
            $sourceImage = imagecreatefromwebp($sourcePath);
            break;
        default:
            return false;
    }

    if (!$sourceImage) {
        return false;
    }

    // Create new image with calculated dimensions
    $newImage = imagecreatetruecolor($newWidth, $newHeight);

    // Preserve transparency for PNG and GIF
    if ($mimeType == 'image/png' || $mimeType == 'image/gif') {
        imagealphablending($newImage, false);
        imagesavealpha($newImage, true);
        $transparent = imagecolorallocatealpha($newImage, 255, 255, 255, 127);
        imagefill($newImage, 0, 0, $transparent);
    }

    // Resize the image
    imagecopyresampled($newImage, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);

    // Save as WebP with good quality (80%)
    $result = imagewebp($newImage, $destinationPath, 80);

    // Clean up memory
    imagedestroy($sourceImage);
    imagedestroy($newImage);

    return $result;
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $folder = '../upload/';

    if (!is_dir($folder)) {
        if (!mkdir($folder, 0777, true) && !is_dir($folder)) {
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode(array(
                'status' => 'error',
                'message' => 'Upload directory could not be created. Please check server permissions for the project folder.'
            ));
            mysqli_close($conn);
            exit();
        }
    }

    if (!is_writable($folder)) {
        $details = array();
        $perms = @fileperms($folder);
        if ($perms !== false) {
            $details[] = 'Permissions: ' . substr(sprintf('%o', $perms), -4);
        }
        $uid = @fileowner($folder);
        if ($uid !== false) {
            $ownerName = function_exists('posix_getpwuid') ? @posix_getpwuid($uid) : false;
            $details[] = 'Owner: ' . ($ownerName && isset($ownerName['name']) ? $ownerName['name'] . " ({$uid})" : (string)$uid);
        }
        $gid = @filegroup($folder);
        if ($gid !== false) {
            $groupName = function_exists('posix_getgrgid') ? @posix_getgrgid($gid) : false;
            $details[] = 'Group: ' . ($groupName && isset($groupName['name']) ? $groupName['name'] . " ({$gid})" : (string)$gid);
        }

        header('Content-Type: application/json');
        http_response_code(500);
        echo json_encode(array(
            'status' => 'error',
            'message' => 'Upload directory is not writable. Please fix folder permissions for /upload.',
            'details' => $details,
        ));
        mysqli_close($conn);
        exit();
    }

    $itemsPerPage = isset($_POST["itemsPerPage"]) ? intval($_POST["itemsPerPage"]) : 40; // Dynamic items per page from frontend
    $itemsPerPage = max(20, min(100, $itemsPerPage)); // Clamp between 20 and 100 for safety
    $page = max(1, isset($_POST["page"]) ? intval($_POST["page"]) : 1);
    $offset = ($page - 1) * $itemsPerPage; // Offset for SQL LIMIT

    header('Content-Type: application/json');

    // Handle cases where request payload exceeds PHP upload limits
    if (
        empty($_FILES)
        && empty($_POST)
        && isset($_SERVER['CONTENT_LENGTH'])
        && intval($_SERVER['CONTENT_LENGTH']) > 0
    ) {
        http_response_code(413);
        echo json_encode(array(
            'status' => 'error',
            'message' => 'The upload request exceeds the server limits. Try uploading fewer files at a time or increase PHP upload_max_filesize and post_max_size.'
        ));
        mysqli_close($conn);
        exit();
    }

    // Check if files were uploaded
    if (isset($_FILES["file"]) && $_FILES["file"]["error"][0] != UPLOAD_ERR_NO_FILE) {
        $maxFileSize = 250 * 1024 * 1024; // 250 MB (in bytes)
        $newFiles = "";
        $errorMessages = array();

        // Loop through each file in the $_FILES array
        foreach ($_FILES["file"]["tmp_name"] as $key => $tmp_name) {
            // Check if the file is uploaded successfully
            if ($_FILES["file"]["error"][$key] == UPLOAD_ERR_OK) {
                $originalName = pathinfo($_FILES["file"]["name"][$key], PATHINFO_FILENAME);
                $fileExtension = pathinfo($_FILES["file"]["name"][$key], PATHINFO_EXTENSION);
                $mimeType = $_FILES["file"]["type"][$key];

                // Sanitize the original name
                // Replace spaces and underscores with hyphens first
                $sanitizedOriginalName = str_replace([" ", "_"], "-", $originalName);

                // Remove all special chars EXCEPT hyphens
                $sanitizedOriginalName = preg_replace("/[^a-zA-Z0-9-]/", "", $sanitizedOriginalName);

                // Remove leading/trailing hyphens
                $sanitizedOriginalName = trim($sanitizedOriginalName, "-");

                // Convert everything to lowercase
                $sanitizedOriginalName = strtolower($sanitizedOriginalName);

                // Check if it's an image file
                $isImage = strpos($mimeType, 'image') !== false;

                if ($isImage) {
                    // For images, change extension to .webp
                    $file_name = $sanitizedOriginalName . '.webp';
                } else {
                    // For non-images, keep original extension (in lowercase)
                    $file_name = $sanitizedOriginalName . '.' . strtolower($fileExtension);
                }

                $destination = $folder . $file_name;

                // Check if the file already exists in the target directory and modify the file name accordingly
                $i = 1;
                while (file_exists($destination)) {
                    if ($isImage) {
                        $file_name = $sanitizedOriginalName . '-' . $i . '.webp';
                    } else {
                        $file_name = $sanitizedOriginalName . '-' . $i . '.' . $fileExtension;
                    }
                    $destination = $folder . $file_name;
                    $i++;
                }

                // Check file size
                if ($_FILES["file"]["size"][$key] <= $maxFileSize) {
                    $uploadSuccess = false;

                    $sourcePath = $_FILES["file"]["tmp_name"][$key];

                    // Get the original MIME type from the upload
                    $mimeType = $_FILES["file"]["type"][$key];

                    if ($isImage) {
                        // For images, get detailed info
                        $imageInfo = getimagesize($sourcePath);

                        if ($imageInfo !== false) {
                            $imageMimeType = $imageInfo['mime'];

                            if ($imageMimeType === 'image/jpeg' || $imageMimeType === 'image/png') {
                                // Process jpeg/png only to webp
                                $uploadSuccess = processImage($sourcePath, $destination);
                                if (!$uploadSuccess) {
                                    $errorMessages[] = "Failed to process image: " . $_FILES["file"]["name"][$key];
                                    continue;
                                }
                            } else {
                                // Save other image types (gif, avif) as-is with original extension
                                $file_name = $sanitizedOriginalName . '.' . $fileExtension;

                                $destination = $folder . $file_name;
                                $i = 1;
                                while (file_exists($destination)) {
                                    $file_name = $sanitizedOriginalName . '-' . $i . '.' . $fileExtension;
                                    $destination = $folder . $file_name;
                                    $i++;
                                }

                                $uploadSuccess = move_uploaded_file($sourcePath, $destination);
                                if (!$uploadSuccess) {
                                    $errorMessages[] = "Failed to move uploaded file: " . $_FILES["file"]["name"][$key];
                                    continue;
                                }
                            }
                        } else {
                            $errorMessages[] = "Invalid image file: " . $_FILES["file"]["name"][$key];
                            continue;
                        }
                    } else {
                        // For non-images, move file normally
                        $uploadSuccess = move_uploaded_file($sourcePath, $destination);
                        if (!$uploadSuccess) {
                            $errorMessages[] = "Failed to move uploaded file: " . $_FILES["file"]["name"][$key];
                            continue;
                        }
                    }

                    if ($uploadSuccess) {
                        $type = '';

                        // Determine the type based on file extension/mime type
                        if ($isImage) {
                            $type = 'image';
                        } elseif (strpos($mimeType, 'pdf') !== false || $fileExtension === 'pdf') {
                            $type = 'pdf';
                        } elseif (strpos($mimeType, 'video') !== false) {
                            $type = 'video';
                        } elseif (
                            strpos($mimeType, 'text/csv') !== false ||
                            strpos($mimeType, 'application/csv') !== false ||
                            $fileExtension === 'csv'
                        ) {
                            $type = 'csv';
                        } elseif (strpos($mimeType, 'audio') !== false) {
                            $type = 'audio';
                        } else {
                            // If type cannot be determined, log error and skip
                            $errorMessages[] = "Unknown file type for: " . $_FILES["file"]["name"][$key];
                            // Delete the uploaded file since we can't process it
                            if (file_exists($destination)) {
                                unlink($destination);
                            }
                            continue;
                        }

                        // Using prepared statement for media insertion
                        $sql = "INSERT INTO tbl_media (file_name, type) VALUES (?, ?)";
                        $stmt = mysqli_prepare($conn, $sql);

                        if ($stmt) {
                            mysqli_stmt_bind_param($stmt, 'ss', $file_name, $type);
                            if (mysqli_stmt_execute($stmt)) {
                                // File was successfully uploaded and inserted

                                $id = mysqli_insert_id($conn);
                                $url = media_folder . $file_name;

                                if ($type == 'pdf') {
                                    $icon = "assets/img/pdf-icon.png";
                                } else if ($type == 'csv') {
                                    $icon = "assets/img/csv-icon.png";
                                } else if ($type == 'video') {
                                    $icon = "assets/img/video-icon.png";
                                } else {
                                    $icon = media_folder . $file_name;
                                }
                                $newFiles .= '<div class="col media-tile" id="' . $id . '" data-desc="" data-file="' . $url . '" data-type="' . $type . '"> <img src="' . $icon . '" width="140" height="140">';

                                if ($type != 'image') {
                                    $newFiles .= '<span>' . $file_name . '</span>';
                                }

                                $newFiles .= '</div>';
                            } else {
                                $errorMessages[] = "Error in uploading file: " . $_FILES["file"]["name"][$key];
                            }

                            mysqli_stmt_close($stmt);
                        } else {
                            $errorMessages[] = "Error in preparing media insertion statement: " . mysqli_error($conn);
                        }
                    } else {
                        $errorMessages[] = "Failed to move uploaded file.";
                    }
                } else {
                    $errorMessages[] = $_FILES["file"]["name"][$key] . "'s size exceeds the maximum limit.";
                }
            } else {
                $errorMessages[] = "Error uploading file: " . $_FILES["file"]["name"][$key];
            }
        }

        if (!empty($errorMessages)) {
            echo json_encode(array('status' => 'error', 'messages' => $errorMessages));
        } else {
            echo json_encode(array('status' => 'success', 'data' => $newFiles));
        }
    }

    //checks if post request is for deleting media
    elseif (isset($_POST["delete_ids"])) {
        $delete_ids = $_POST["delete_ids"];

        // Sanitize the array values to prevent SQL injection
        $sanitizedIds = array_map('intval', $delete_ids);
        $placeholders = implode(',', array_fill(0, count($sanitizedIds), '?'));

        // Construct the SELECT query to get file names before deletion
        $selectSql = "SELECT file_name FROM tbl_media WHERE id IN ($placeholders)";
        $selectStmt = mysqli_prepare($conn, $selectSql);

        // Perform the SELECT query
        if ($selectStmt) {
            $types = str_repeat('i', count($sanitizedIds));
            mysqli_stmt_bind_param($selectStmt, $types, ...$sanitizedIds);
            mysqli_stmt_execute($selectStmt);
            mysqli_stmt_bind_result($selectStmt, $file);

            $fileToDelete = array();

            while (mysqli_stmt_fetch($selectStmt)) {
                $fileToDelete[] = $file;
            }

            mysqli_stmt_close($selectStmt);

            // Construct the DELETE query
            $deleteSql = "DELETE FROM tbl_media WHERE id IN ($placeholders)";
            $deleteStmt = mysqli_prepare($conn, $deleteSql);

            // Perform the DELETE query
            if ($deleteStmt) {
                mysqli_stmt_bind_param($deleteStmt, $types, ...$sanitizedIds);
                if (mysqli_stmt_execute($deleteStmt)) {
                    // Delete the corresponding files
                    foreach ($fileToDelete as $file) {
                        $filePath = $folder . $file;
                        if (file_exists($filePath)) {
                            unlink($filePath);
                        }
                    }

                    $response = array('status' => 'success', 'message' => 'Records and files deleted successfully.');
                } else {
                    $response = array('status' => 'error', 'message' => 'Error executing DELETE statement: ' . mysqli_error($conn));
                }

                mysqli_stmt_close($deleteStmt);
            } else {
                $response = array('status' => 'error', 'message' => 'Error preparing DELETE statement: ' . mysqli_error($conn));
            }
        } else {
            $response = array('status' => 'error', 'message' => 'Error preparing SELECT statement: ' . mysqli_error($conn));
        }

        // Return the response as JSON
        echo json_encode($response);
    }

    //checks for media file requests
    elseif (isset($_POST["type"])) {
        // Define the base SQL query
        $selectSql = "SELECT * FROM tbl_media";

        // Check if the type is "all"
        if ($_POST["type"] != "all") {
            $selectSql .= " WHERE type = ?";
        }

        $selectSql .= " ORDER BY id DESC LIMIT ? OFFSET ?";

        // Prepare the SQL statement
        $selectStmt = mysqli_prepare($conn, $selectSql);

        if ($selectStmt) {
            // Calculate the OFFSET value based on the current page
            $offset = ($page - 1) * $itemsPerPage;

            // Fetch one extra row so the client knows whether another batch exists.
            $queryLimit = $itemsPerPage + 1;

            // Bind parameters
            if ($_POST["type"] != "all") {
                mysqli_stmt_bind_param($selectStmt, 'sii', $_POST["type"], $queryLimit, $offset);
            } else {
                mysqli_stmt_bind_param($selectStmt, 'ii', $queryLimit, $offset);
            }

            // Execute the statement
            mysqli_stmt_execute($selectStmt);

            // Get the result
            $result = mysqli_stmt_get_result($selectStmt);

            // Initialize modal content
            $modalContent = "";

            $hasMore = mysqli_num_rows($result) > $itemsPerPage;
            $rendered = 0;
            while ($rendered < $itemsPerPage && $res = mysqli_fetch_assoc($result)) {
                $rendered++;
                // Output each media item
                $id = $res['id'];
                $type = $res['type'];
                if ($type == 'pdf') {
                    $icon = "assets/img/pdf-icon.png";
                } else if ($type == 'csv') {
                    $icon = "assets/img/csv-icon.png";
                } else if ($type == 'video') {
                    $icon = "assets/img/video-icon.png";
                } else {
                    $icon = '../upload/' . $res['file_name'];
                }
                $url = '../upload/' . $res['file_name'];

                $modalContent .= '
                <div class="col media-tile" id="' . $id . '" 
                    data-desc="' . ((isset($res['description']) && $res['description'] !== null) ? h($res['description']) : '') . '"
                    data-file="' . $url . '" 
                    data-type="' . $type . '">
                    <img src="' . $icon . '" width="140" height="140" loading="lazy" decoding="async" alt="' . h($res['file_name']) . '">';

                if ($type != 'image') {
                    $modalContent .= '<span>' . h($res['file_name']) . '</span>';
                }

                $modalContent .= '</div>';
            }

            $response = array('status' => 'success', 'data' => $modalContent, 'has_more' => $hasMore);
        } else {
            $response = array('status' => 'error', 'message' => 'Error preparing statement: ' . mysqli_error($conn));
        }

        // Return the response as JSON
        header('Content-Type: application/json');
        echo json_encode($response);
    }

    // Get the attachment ID and description from the POST data
    elseif (isset($_POST["description"])) {
        $attachmentId = $_POST["id"];
        $description = $_POST["description"];

        // Update the description in the database
        $updateSql = "UPDATE tbl_media SET description = ? WHERE id = ?";
        $updateStmt = mysqli_prepare($conn, $updateSql);

        if ($updateStmt) {
            mysqli_stmt_bind_param($updateStmt, 'si', $description, $attachmentId);
            if (mysqli_stmt_execute($updateStmt)) {
                // Description updated successfully
                $response = array('status' => 'success', 'message' => 'Description updated successfully.');
            } else {
                $response = array('status' => 'error', 'message' => 'Error updating description: ' . mysqli_error($conn));
            }

            mysqli_stmt_close($updateStmt);
        } else {
            $response = array('status' => 'error', 'message' => 'Error preparing UPDATE statement: ' . mysqli_error($conn));
        }

        // Return the response as JSON
        header('Content-Type: application/json');
        echo json_encode($response);
    }

    // Invalid request
    else {
        http_response_code(400); // Bad Request
        echo json_encode(array('status' => 'error', 'message' => 'Invalid request.'));
    }

    mysqli_close($conn);
    exit();
} else {
    include("components/header.php");
?>
    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Loader -->
        <div id="loader" class="loader d-none">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>

        <!-- Content Header (Page header) -->
        <section class="content-header media-library-header">
            <div class="media-library-heading d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
                <h1 class="mb-0">
                    <?php echo $page_title; ?>
                </h1>
                <div class="media-library-actions d-flex align-items-center gap-2">
                    <label class="upload__btn">
                        Add New
                        <input type="file" multiple accept="image/*,application/pdf,video/*,.csv" id="add-new-media"
                            name="file[]" class="upload__inputfile btn btn-primary">
                    </label>

                    <!-- Bulk Select Button -->
                    <button type="button" id="bulkSelect" class="btn btn-outline-primary text-nowrap">Bulk Select</button>

                    <!-- Delete Selected Button -->
                    <button id="deleteSelected" class="btn btn-outline-danger m-1 d-none">Delete</button>
                </div>
            </div>
            <ol class="breadcrumb">
                <li><a href="dashboard.php"><i class="fa fa-dashboard"></i> Home</a></li>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="media-gallery.php"><?php echo $page_title; ?></a></li>
            </ol>
        </section>

        <section class="content media-library-content">
            <div id="media-grid-body">
                <div class="media-grid">
                    <div class="media-load-sentinel" role="status" aria-live="polite">
                        <span class="media-load-message"></span>
                        <button type="button" class="btn btn-outline-secondary btn-sm media-load-retry d-none">Try again</button>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="attachmentModal" tabindex="-1" aria-labelledby="attachmentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="attachmentModalLabel">Attachment Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div id="attachment-preview"></div>
                        </div>
                        <div class="col-md-4">
                            <a id="attachment-link" target="_blank">
                                <i class="fa fa-external-link"></i> Open File
                            </a>
                            <div class="form-group">
                                <label for="attachment-desc" class="form-label">Alt Text</label>
                                <input type="text" class="form-control" id="attachment-desc" name="attachment-desc">
                                <input type="hidden" id="attachment-id" name="attachment-id">
                            </div>
                            <button type="button" class="btn btn-success w-100 mt-2" id="descSaveBtn">
                                <i class="fa fa-floppy-o"></i> Save
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script>
        var bulkSelect = 'off';
    </script>
    <script src="assets/js/media-gallery.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            selectionType = 'multiple';

            FileType = "all";
            currentPage = 0;
            loadMedia(1, FileType);

            // Bulk Select Button Click Event
            $('#bulkSelect').on('click', function() {
                $(this).toggleClass('selected-bulk');
                if ($(this).hasClass('selected-bulk')) {
                    bulkSelect = 'on';
                } else {
                    // Deselect all tiles
                    $('.media-tile').removeClass('selected_tile');
                    selectedIds = [];
                    bulkSelect = 'off';
                    $('#deleteSelected').addClass('d-none');
                }
            });

            // Delete Selected Button Click Event
            $('#deleteSelected').on('click', function() {
                console.log(selectedIds);
                // AJAX request to delete selected items
                $.ajax({
                    url: 'media-gallery.php',
                    method: 'POST',
                    data: {
                        delete_ids: selectedIds
                    },
                    success: function(response) {
                        // Handle the response from the server
                        console.log(response);

                        if (response.status === 'success') {
                            // Remove the selected media-tiles from the HTML
                            selectedIds.forEach(function(id) {
                                $('#' + id).fadeOut('slow', function() {
                                    $(this).remove();
                                });
                            });

                            // Clear the selectedIds array
                            selectedIds = [];
                            if (selectedIds.length > 0) {
                                $('#deleteSelected').removeClass('d-none');
                            }
                            //bulk select off
                            bulkSelect = 'off';
                            $('#bulkSelect').removeClass('selected-bulk');
                            $('#deleteSelected').addClass('d-none');
                        }
                    },
                    error: function(xhr, status, error) {
                        // Handle errors
                        console.error(xhr.responseText);
                    }
                });
            });

        });
    </script>
    <?php include("components/footer.php"); ?>
<?php } ?>
