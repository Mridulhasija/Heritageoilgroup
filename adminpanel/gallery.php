<?php
require_once __DIR__ . '/components/config-header.php';

$conn = db();

$page_title = "Gallery Management";
$table = "tbl_gallery";

$id_col = 'id';
$id_type = "i";

$actions = [
    'approve' => false,
    'edit' => true,
    'delete' => true
];

$currentUrl = $_SERVER['PHP_SELF'];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_GET['action'])) {
    try {
        $action = $_GET['action'];
        $id = isset($_GET['id']) ? $_GET['id'] : null;

        include('components/quick-process.php');

        if ($action === "add" || $action === "edit") {
            $title = isset($_POST['title']) ? trim($_POST['title']) : null;
            $image = isset($_POST['image']) ? trim($_POST['image']) : null;
            $stmt = null;

            if ($action == 'add') {
                // Count the number of existing records
                $count_stmt = $conn->prepare("SELECT COUNT(*) FROM $table");
                $count_stmt->execute();
                $count_result = $count_stmt->get_result();
                $count_row = $count_result->fetch_assoc();
                $position = $count_row['COUNT(*)'] + 1; // Calculate position for the new record

                // Prepare the INSERT statement
                $stmt = $conn->prepare(
                    "INSERT INTO $table (title, image, position) VALUES (?, ?, ?)"
                );
            } elseif ($action == 'edit' && isset($id)) {
                // Prepare the UPDATE statement
                $stmt = $conn->prepare(
                    "UPDATE $table SET title = ?, image = ? WHERE id = ?"
                );
            }

            if (!$stmt) {
                throw new Exception("Error preparing statement: " . $conn->error);
            }

            if ($action == 'add') {
                // Bind parameters for INSERT
                $stmt->bind_param("ssi", $title, $image, $position);
            } elseif ($action == 'edit' && isset($id)) {
                // Bind parameters for UPDATE
                $stmt->bind_param("ssi", $title, $image, $id);
            }

            if (!$stmt->execute()) {
                throw new Exception("Error executing statement: " . $stmt->error);
            }

            $stmt->close();
            $_SESSION["success-msg"] = "Gallery Image successfully " . ($action == 'add' ? 'added' : 'edited');

            header('Location: ' . strtok($currentUrl, '?'));
            exit();
        } elseif ($action === "position" && isset($_POST['direction']) && isset($_POST['rowId'])) {
            $direction = $_POST['direction'];
            $rowId = $_POST['rowId'];

            // Call the function to move banner position
            $response = $utils->moveBannerPosition($conn, $table, $direction, $rowId);

            // Send response as JSON
            header('Content-Type: application/json');
            echo json_encode($response);
            exit;
        } else {
            // Parameters not provided
            $response['success'] = false;
            $response['error'] = "Missing parameters.";
            header('Content-Type: application/json');
            echo json_encode($response);
            exit;
        }
    } catch (Exception $e) {
        $_SESSION["error-msg"] = "Error: " . $e->getMessage();
    }
    exit();
} else {
    //show page

    $action = (isset($_GET['action']) && $_GET['action'] == "edit") ? 'edit' : 'add';

    if ($action == "edit") {
        $page_subtitle = "Edit Banner";
        $id = isset($_GET['id']) ? $_GET['id'] : null;
        $data = ($id) ? mysqli_fetch_array($getdata->FetchQuery('SELECT * FROM `' . $table . '` WHERE id = ?', array($id))) : null;
        if (!$data) {
            // No record found with the provided ID, redirect to banners.php
            header("Location: $currentUrl.php");
            exit();
        }
    } else {
        $page_subtitle = "Add Banner";
    }
    include("components/header.php");
    ?>
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <div class="d-flex align-items-center">
                <h1 class="m-0" style="font-size:24px"><?= $page_title; ?></h1>
            </div>
            <ol class="breadcrumb">
                <li><a href="#"><i class="fa fa-home"></i> Home</a></li>
                <li><a href="dashboard.php"><i class="fa fa-dashboard"></i>Dashboard</a></li>
                <li><a href="gallery.php"><?= $page_title; ?></a></li>
                <li class="active"><?= $page_subtitle; ?></li>
            </ol>
        </section>
        <!-- Main content -->
        <section class="content">
            <!-- Main row -->
            <div class="row justify-content-center">
                <!-- Left col -->
                <section class="col-lg-12">
                    <!-- TO DO List -->
                    <div class="box box-info">
                        <?php include_once("components/alert-msg.php"); ?>
                        <div class="box-body">
                            <form method="post"
                                action="<?= $currentUrl ?>?<?= ($action == 'add') ? 'action=add' : 'action=edit&id=' . $id; ?>"
                                enctype="multipart/form-data">
                                <div class="box-body">
                                    Title <span style="color:red;">*</span>
                                    <div class="form-group">
                                        <input type="text" class="form-control" name="title"
                                            value="<?= ($action === 'edit') ? h($data['title']) : ''; ?>" placeholder="Title">
                                    </div>

                                    <div class="row">
                                        <div class="form-group col-lg-3">
                                            <label>Feature Image<span style="color:red;">*</span></label><br>
                                            <?php
                                            if (($action === 'edit') and (empty($data['image']) !== true)) {
                                                echo "<img src='$media_folder{$data['image']}' class='full-width' id='galery-image-img'>";
                                            } ?>
                                            <!--<input type="file" name="image" accept="image/*">-->
                                            <input type="hidden" name="image"
                                                value="<?= ($action === 'edit') ? h($data['image']) : ''; ?>"
                                                id='galery-image-input'>
                                            <button type="button" class="btn btn-secondary btn-sm mt-3 openMediaModal"
                                                data-bs-toggle="modal" data-bs-target="#mediaSelectionModal"
                                                data-type="image" data-selection="single" group-id='galery-image'>
                                                Select Image
                                            </button>
                                        </div>
                                        <div class="col-lg-9 d-flex align-items-end justify-content-end">
                                            <?php if ($action === 'add'): ?>
                                                <button type="submit" name="Save" class="btn btn-primary">Add</button>
                                            <?php else: ?>
                                                <button type="submit" name="Update" class="btn btn-primary">Update</button>
                                            <?php endif ?>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <!-- /.box -->
                </section>

                <!-- /.Left col -->
                <!-- right col (We are only adding the ID to make the widgets sortable)-->
                <section class="col-lg-12">
                    <div class="box box-info">
                        <div class="box-body">
                            <table id="gallery_table">
                                <tbody class="d-flex flex-wrap gap-2">
                                    <?php
                                    $allRec = $getdata->FetchQuery("SELECT * FROM $table order by position");
                                    $total_records = mysqli_num_rows($allRec);
                                    $counter = 0;
                                    while ($data = mysqli_fetch_array($allRec)):
                                        $counter++;
                                        ?>
                                        <tr id="<?= h($data['id']) ?>" style="min-width:300px">
                                            <td style="width:10%" class="pe-2">
                                                <button class="btn btn-outline-primary <?= $counter == 1 ? 'd-none' : ''; ?>"
                                                    title="Move Up"><i class="fa fa-arrow-left"></i></button>
                                                <button
                                                    class="btn btn-outline-primary <?= $counter == $total_records ? 'd-none' : ''; ?>"
                                                    title="Move Down"><i class="fa fa-arrow-right"></i></button>
                                            </td>

                                            <td class="img-col">
                                                <div class="image-overlay-wrapper">
                                                    <img src="<?= $media_folder . $data['image'] ?>"
                                                        title="<?= h($data['title']) ?>" class="img-fluid">
                                                    <div class="image-overlay">
                                                        <div class="overlay-content">
                                                            <a href="<?php echo $currentUrl ?>?action=edit&id=<?= h($data["id"]) ?>"
                                                                type="button" class="btn btn-primary" title="Edit"><i
                                                                    class="fa fa-pencil" aria-hidden="true"></i></a>
                                                            <button type="submit" class="btn btn-danger" title="Delete"><i
                                                                    class="fa fa-trash" aria-hidden="true"></i></button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>
                <!-- /.content -->
            </div>
        </section>
        <!-- /.content-wrapper -->
    </div>

    <style>
        .img-col {
            position: relative;
            height: 150px;
        }

        .image-overlay-wrapper {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
        }

        .image-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            opacity: 0;
            transition: opacity 0.3s;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .overlay-content {
            text-align: center;
        }

        /* Optional: Style for image */
        .img-fluid {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Show overlay on hover */
        .image-overlay-wrapper:hover .image-overlay {
            opacity: 1;
        }
    </style>

    <script>
        function handleArrowButtons(tableId) {
            var table = document.getElementById(tableId);
            var firstRow = table.querySelector('tr:first-child');
            var lastRow = table.querySelector('tr:last-child');

            firstRow.querySelector('.fa-arrow-left').parentNode.classList.add('d-none'); // Hide the up arrow button
            firstRow.querySelector('.fa-arrow-right').parentNode.classList.remove('d-none'); // Show the down arrow button
            firstRow.nextElementSibling.querySelector('.fa-arrow-left').parentNode.classList.remove('d-none');

            lastRow.querySelector('.fa-arrow-left').parentNode.classList.remove('d-none'); // Show the up arrow button
            lastRow.querySelector('.fa-arrow-right').parentNode.classList.add('d-none'); // Hide the down arrow button
            lastRow.previousElementSibling.querySelector('.fa-arrow-right').parentNode.classList.remove('d-none');
        }

        $(document).ready(function () {
            $('.btn-danger').on('click', function () {
                var trId = $(this).closest('tr').attr('id');

                // Display a confirmation dialog
                if (confirm('Are you sure you want to delete this item?')) {
                    // Send AJAX request
                    $.ajax({
                        type: 'POST',
                        url: "<?php echo $currentUrl; ?>?action=delete&id=" + trId,
                        // data: { id: trId },
                        success: function (response) {
                            if (response === 'success') {
                                // Remove the row with a smooth animation
                                $('#' + trId).fadeOut('slow', function () {
                                    $(this).remove();
                                    handleArrowButtons('gallery_table');
                                });
                            } else {
                                alert('Failed: ' + response);
                                console.log(response);
                            }
                        },
                        error: function (xhr, status, error) {
                            console.error('AJAX Error:', xhr);
                            alert('Error in AJAX request.');
                        }
                    });
                }
            });
        });

        // Function to move table rows up or down
        function moveRow(direction, button) {
            var row = button.closest('tr'); // Get the parent row
            var sibling = (direction === 'up') ? row.previousElementSibling : row.nextElementSibling; // Get the sibling row

            if (sibling) {
                const formData = new FormData();
                formData.append('direction', direction);
                formData.append('rowId', row.getAttribute('id'));

                // Assuming you're using the fetch API for AJAX requests
                fetch('<?= $currentUrl ?>?action=position', {
                    method: 'POST',
                    body: formData,
                })
                    .then(res => res.json())
                    .then(data => {
                        console.log('Response from server:', data); // Log the response
                        if (data.success) {
                            // If success is true, shift the row
                            if (direction === 'up') {
                                row.parentNode.insertBefore(row, sibling); // Move row up
                            } else {
                                row.parentNode.insertBefore(sibling, row); // Move row down
                            }
                            handleArrowButtons('gallery_table');
                        } else {
                            // If success is false, handle the error or show a message
                            console.error("Failed to shift the row");
                        }
                    })
                    .catch(error => {
                        console.error('There was a problem with your fetch operation:', error);
                    });
            }
        }


        // Add event listeners to the arrow buttons
        document.querySelectorAll('.fa-arrow-left').forEach(function (icon) {
            var button = icon.parentNode; // Get the parent button
            button.addEventListener('click', function () {
                moveRow('up', button);
            });
        });

        document.querySelectorAll('.fa-arrow-right').forEach(function (icon) {
            var button = icon.parentNode; // Get the parent button
            button.addEventListener('click', function () {
                moveRow('down', button);
            });
        });
    </script>

    <script src="assets/js/media-gallery.js"></script>
    <?php include("components/media-gallery-modal.php"); ?>
    <?php include_once("components/footer.php");
} ?>
