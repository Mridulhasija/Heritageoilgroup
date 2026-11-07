<?php
require_once __DIR__ . '/components/config-header.php';

$conn = db();

$page_title = "Banner Management";
$table = "tbl_banner";

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
        $id = isset($_GET['id']) ? $_GET['id'] : null;
        $action = $_GET['action'];

        // Include necessary files
        include('components/quick-process.php');

        // For adding or editing banners
        if ($action === "add" || $action === "edit") {
            $title = isset($_POST['title']) ? trim($_POST['title']) : null;
            $sub_title = isset($_POST['sub_title']) ? trim($_POST['sub_title']) : null;
            $banner_link = isset($_POST['banner_link']) ? trim($_POST['banner_link']) : null;
            $banner_link_text = isset($_POST['banner_link_text']) ? trim($_POST['banner_link_text']) : null;
            $image = isset($_POST['image']) ? trim($_POST['image']) : null;
            $description = isset($_POST['description']) ? trim($_POST['description']) : null;
            $status = isset($_POST['status']) ? trim($_POST['status']) : null;
            $stmt = null;

            if ($action == 'add') {
                // Prepare statement for adding new banner
                $count_stmt = $conn->prepare("SELECT COUNT(*) FROM $table");
                if (!$count_stmt) {
                    throw new Exception("Error preparing statement: " . $conn->error);
                }
                $count_stmt->execute();
                $count_result = $count_stmt->get_result();
                $count_row = $count_result->fetch_assoc();
                $position = $count_row['COUNT(*)'] + 1; // Calculate position for the new record

                $stmt = $conn->prepare(
                    "INSERT INTO $table (title, sub_title, banner_link, link_text, image, description, status, position) VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
                );
                if (!$stmt) {
                    throw new Exception("Error preparing statement: " . $conn->error);
                }

                if (!$stmt->bind_param("ssssssii", $title, $sub_title, $banner_link, $banner_link_text, $image, $description, $status, $position)) {
                    throw new Exception("Error binding parameters: " . $stmt->error);
                }
            } elseif ($action == 'edit' && isset($_GET['id'])) {
                // Prepare statement for editing existing banner
                $stmt = $conn->prepare(
                    "UPDATE $table SET title = ?, sub_title = ?, banner_link = ?, link_text = ?, image = ?, description = ?, status = ? WHERE id = ?"
                );
                if (!$stmt) {
                    throw new Exception("Error preparing statement: " . $conn->error);
                }

                if (!$stmt->bind_param("ssssssii", $title, $sub_title, $banner_link, $banner_link_text, $image, $description, $status, $id)) {
                    throw new Exception("Error binding parameters: " . $stmt->error);
                }
            }

            if ($stmt) {
                $stmt->execute();
                $stmt->close();

                $_SESSION["success-msg"] = "Banner successfully " . ($action == 'add' ? 'added' : 'edited');
            }

            header('Location: ' . strtok($currentUrl, '?'));
            exit();
        } elseif ($action === "position" && isset($_POST['direction']) && isset($_POST['rowId'])) {
            // Move banner position
            $direction = $_POST['direction'];
            $rowId = $_POST['rowId'];
            $response = $utils->moveBannerPosition($conn, $table, $direction, $rowId);

            // Send response as JSON
            header('Content-Type: application/json');
            echo json_encode($response);
            exit();
        } else {
            // Parameters not provided
            $response['success'] = false;
            $response['error'] = "Missing parameters.";
            header('Content-Type: application/json');
            echo json_encode($response);
            exit();
        }
    } catch (Exception $e) {
        $_SESSION["error-msg"] = "Error: " . $e->getMessage();
    }
} else {
    //show page

    $action = (isset($_GET['action']) && $_GET['action'] == "edit") ? 'edit' : 'add';

    if ($action == "edit") {
        $page_subtitle = "Edit Banner";
        $id = isset($_GET['id']) ? $_GET['id'] : null;
        $data = ($id) ? mysqli_fetch_array($getdata->FetchQuery('SELECT * FROM `' . $table . '` WHERE id = ?', array($id))) : null;
        if (!$data) {
            // No record found with the provided ID, redirect to banners.php
            header('Location: ' . strtok($currentUrl, '?'));
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
                <h1 class="m-0" style="font-size:24px">
                    <?= $page_title; ?>
                </h1>
            </div>
            <ol class="breadcrumb">
                <li><a href="#"><i class="fa fa-home"></i> Home</a></li>
                <li><a href="dashboard.php"><i class="fa fa-dashboard"></i>Dashboard</a></li>
                <li class="active"><?= $page_title; ?></li>
            </ol>
        </section>
        <!-- Main content -->
        <section class="content">
            <!-- Main row -->
            <div class="row">
                <!-- Left col -->
                <section class="col-lg-8 connectedSortable">
                    <!-- TO DO List -->
                    <div class="box box-info">
                        <div class="box-header with-border">
                            <h3 class="box-title m-0"><?= h($page_subtitle); ?></h3>
                            <p class="help-block m-0 mt-1">
                                Add a new banner or select one on the right to edit. Use the arrows to change display order.
                            </p>
                        </div>
                        <?php include_once("components/alert-msg.php"); ?>
                        <div class="box-body">
                            <form method="post"
                                action="<?= $currentUrl ?>?<?= ($action == 'add') ? 'action=add' : 'action=edit&id=' . $id; ?>"
                                enctype="multipart/form-data">
                                <div class="box-body">
                                    <div class="row g-3">
                                        <div class="form-group col-lg-12">
                                            <label>Feature Image<span style="color:red;">*</span></label><br>
                                            <?php
                                            if (($action === 'edit') and (empty($data['image']) !== true)) {
                                                echo "<img src='$media_folder{$data['image']}' class='full-width' id='banner-image-img'>";
                                            } ?>
                                            <input type="hidden" name="image"
                                                value="<?= ($action === 'edit') ? h($data['image']) : ''; ?>"
                                                id='banner-image-input'>
                                            <button type="button" class="btn btn-secondary btn-sm mt-3 openMediaModal"
                                                data-bs-toggle="modal" data-bs-target="#mediaSelectionModal"
                                                data-type="image" data-selection="single" group-id='banner-image'>
                                                Select Image
                                            </button>
                                        </div>

                                        <div class="col-6">
                                            <label class="form-label">Banner Title <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" name="title" required
                                                value="<?= ($action === 'edit') ? h($data['title']) : ''; ?>"
                                                placeholder="e.g. Summer Offer - 50% Off">
                                            <div class="form-text">This title also appears in the banner list on the right.</div>
                                        </div>

                                        <div class="col-lg-6">
                                            <label class="form-label">Banner Sub-Title</label>
                                            <input type="text" class="form-control" name="sub_title"
                                                value="<?= ($action === 'edit') ? h($data['sub_title']) : ''; ?>"
                                                placeholder="Optional short sub-heading">
                                        </div>

                                        <div class="col-12">
                                            <label class="form-label">Description</label>
                                            <textarea rows="4" class="form-control" name="description"
                                                placeholder="Optional short description shown on the banner (if used)"><?= ($action === 'edit') ? h($data['description']) : ''; ?></textarea>
                                        </div>

                                        <div class="col-lg-6">
                                            <label class="form-label">Link Text</label>
                                            <input type="text" class="form-control" name="banner_link_text"
                                                value="<?= ($action === 'edit') ? h($data['link_text']) : ''; ?>"
                                                placeholder="e.g. Shop Now">
                                        </div>

                                        <div class="col-lg-6">
                                            <label class="form-label">Link</label>
                                            <input type="text" class="form-control" name="banner_link"
                                                value="<?= ($action === 'edit') ? h($data['banner_link']) : ''; ?>"
                                                placeholder="e.g. /products or https://example.com/page">
                                        </div>

                                        <div class="col-12">
                                            <label class="form-label mb-2">Status</label>
                                            <div class="d-flex flex-wrap gap-4">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="status" id="bannerStatusActive" value="1"
                                                        <?= ($action === 'add' || ($action === 'edit' && (int)$data['status'] === 1)) ? "checked='checked'" : ''; ?> />
                                                    <label class="form-check-label" for="bannerStatusActive">Active</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="status" id="bannerStatusInactive" value="0"
                                                        <?= ($action === 'edit' && (int)$data['status'] === 0) ? "checked='checked'" : ''; ?> />
                                                    <label class="form-check-label" for="bannerStatusInactive">Inactive</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                                <div class="box-footer">
                                    <?php if ($action === 'add'): ?>
                                        <button type="submit" name="Save" class="btn btn-success" id='saveButton'>Add <i class="fa fa-plus"
                                                aria-hidden="true"></i></button>
                                    <?php else: ?>
                                        <div class="d-flex flex-wrap gap-2">
                                            <button type="submit" name="Update" class="btn btn-success" id='saveButton'>Update <i
                                                    class="fa fa-floppy-o" aria-hidden="true"></i></button>
                                            <a href="<?= strtok($currentUrl, '?'); ?>" class="btn btn-outline-secondary">Add New</a>
                                        </div>
                                    <?php endif ?>
                                </div>
                            </form>
                        </div>
                        <!-- /.box -->
                </section>

                <!-- /.Left col -->
                <!-- right col (We are only adding the ID to make the widgets sortable)-->
                <section class="col-lg-4 connectedSortable">
                    <div class="box box-info">
                        <div class="box-header with-border">
                            <h3 class="box-title m-0">Banners</h3>
                            <p class="help-block m-0 mt-1">Titles are shown here for quick identification.</p>
                        </div>
                        <div class="box-body">
                            <div id="banner_list" class="banner-list" role="list">
                                <?php
                                $allRec = $getdata->FetchQuery("SELECT * FROM $table order by position");
                                $total_records = mysqli_num_rows($allRec);
                                $counter = 0;
                                if ($total_records == 0) :
                                ?>
                                    <div class="text-muted">No banners found yet.</div>
                                <?php
                                endif;
                                while ($data = mysqli_fetch_array($allRec)) :
                                    $counter++;
                                    $bannerId = (int)$data['id'];
                                    $bannerTitle = h($data['title'] ?? '');
                                    $bannerSubTitle = h($data['sub_title'] ?? '');
                                    $bannerImage = h($data['image'] ?? '');
                                    $bannerStatus = (int)($data['status'] ?? 0);
                                ?>
                                    <div class="banner-item" role="listitem" id="banner-<?= $bannerId; ?>" data-banner-id="<?= $bannerId; ?>">
                                        <div class="banner-reorder">
                                            <button type="button"
                                                class="btn btn-outline-primary btn-sm js-move-banner <?= $counter == 1 ? 'd-none' : ''; ?>"
                                                data-direction="up" title="Move Up" aria-label="Move Up">
                                                <i class="fa fa-arrow-up"></i>
                                            </button>
                                            <button type="button"
                                                class="btn btn-outline-primary btn-sm js-move-banner <?= $counter == $total_records ? 'd-none' : ''; ?>"
                                                data-direction="down" title="Move Down" aria-label="Move Down">
                                                <i class="fa fa-arrow-down"></i>
                                            </button>
                                        </div>

                                        <div class="banner-thumb">
                                            <img src="<?= $media_folder . $bannerImage ?>"
                                                alt="<?= $bannerTitle ?>"
                                                class="banner-thumb-img">
                                        </div>

                                        <div class="banner-meta">
                                            <div class="banner-title" title="<?= $bannerTitle ?>"><?= $bannerTitle ?></div>
                                            <?php if (!empty($bannerSubTitle)): ?>
                                                <div class="banner-subtitle" title="<?= $bannerSubTitle ?>"><?= $bannerSubTitle ?></div>
                                            <?php endif; ?>
                                            <div class="banner-badges">
                                                <?php if ($bannerStatus === 1): ?>
                                                    <span class="badge bg-success">Active</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">Inactive</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>

                                        <div class="banner-actions">
                                            <a href="<?php echo $currentUrl ?>?action=edit&id=<?= $bannerId ?>"
                                                type="button" class="btn btn-outline-primary btn-sm" title="Edit">
                                                <i class="fa fa-pencil" aria-hidden="true"></i>
                                            </a>
                                            <button type="button" class="btn btn-outline-danger btn-sm js-delete-banner"
                                                data-banner-id="<?= $bannerId; ?>" title="Delete">
                                                <i class="fa fa-trash" aria-hidden="true"></i>
                                            </button>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        </div>
                    </div>
                </section>
                <!-- /.content -->
            </div>
        </section>
        <!-- /.content-wrapper -->
    </div>

    <style>
        .banner-list {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .banner-item {
            display: grid;
            grid-template-columns: 56px 120px 1fr auto;
            gap: 10px;
            align-items: center;
            padding: 10px;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
            background: #fff;
        }

        .banner-reorder {
            display: flex;
            flex-direction: column;
            gap: 6px;
            justify-content: center;
        }

        .banner-thumb-img {
            width: 120px;
            height: 72px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid #e9ecef;
            background: #f8f9fa;
        }

        .banner-meta {
            min-width: 0;
        }

        .banner-title {
            font-weight: 600;
            line-height: 1.2;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .banner-subtitle {
            color: #6c757d;
            font-size: 0.9rem;
            margin-top: 2px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .banner-badges {
            margin-top: 6px;
        }

        .banner-actions {
            display: flex;
            gap: 6px;
            align-items: center;
        }

        @media (max-width: 992px) {
            .banner-item {
                grid-template-columns: 56px 120px 1fr;
            }

            .banner-actions {
                grid-column: 1 / -1;
                justify-content: flex-end;
            }
        }
    </style>

    <script>
        function updateBannerMoveButtons() {
            const items = document.querySelectorAll('#banner_list .banner-item');
            items.forEach(item => {
                item.querySelectorAll('.js-move-banner').forEach(btn => btn.classList.remove('d-none'));
            });

            if (items.length <= 1) {
                items.forEach(item => {
                    item.querySelectorAll('.js-move-banner').forEach(btn => btn.classList.add('d-none'));
                });
                return;
            }

            const firstUp = items[0].querySelector('.js-move-banner[data-direction="up"]');
            if (firstUp) firstUp.classList.add('d-none');
            const lastDown = items[items.length - 1].querySelector('.js-move-banner[data-direction="down"]');
            if (lastDown) lastDown.classList.add('d-none');
        }

        function moveBanner(direction, button) {
            const item = button.closest('.banner-item');
            if (!item) return;

            const sibling = (direction === 'up') ? item.previousElementSibling : item.nextElementSibling;
            if (!sibling || !sibling.classList.contains('banner-item')) return;

            const formData = new FormData();
            formData.append('direction', direction);
            formData.append('rowId', item.getAttribute('data-banner-id'));

            fetch('<?= $currentUrl ?>?action=position', {
                    method: 'POST',
                    body: formData,
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        if (direction === 'up') {
                            item.parentNode.insertBefore(item, sibling);
                        } else {
                            item.parentNode.insertBefore(sibling, item);
                        }
                        updateBannerMoveButtons();
                    } else {
                        alert('Failed to reorder banner.');
                    }
                })
                .catch(() => {
                    alert('Error while reordering banner.');
                });
        }

        $(document).ready(function() {
            updateBannerMoveButtons();

            $('#banner_list').on('click', '.js-delete-banner', function() {
                const bannerId = $(this).data('banner-id');
                const $item = $('#banner-' + bannerId);

                if (!bannerId) return;

                if (confirm('Are you sure you want to delete this banner?')) {
                    $.ajax({
                        type: 'POST',
                        url: "<?php echo $currentUrl; ?>?action=delete&id=" + bannerId,
                        success: function(response) {
                            if (response === 'success') {
                                $item.fadeOut('slow', function() {
                                    $(this).remove();
                                    updateBannerMoveButtons();
                                });
                            } else {
                                alert('Failed: ' + response);
                            }
                        },
                        error: function(xhr) {
                            console.error('AJAX Error:', xhr);
                            alert('Error in AJAX request.');
                        }
                    });
                }
            });

            $('#banner_list').on('click', '.js-move-banner', function() {
                const direction = $(this).data('direction');
                moveBanner(direction, this);
            });
        });
    </script>

    <script src="assets/js/media-gallery.js"></script>
    <?php include("components/media-gallery-modal.php"); ?>
<?php include_once("components/footer.php");
} ?>