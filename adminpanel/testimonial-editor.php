<?php
require_once __DIR__ . '/components/config-header.php';
include("components/header.php");

$conn = db();
$page_title = "Testimonials";
$table = "tbl_testimonial";

$action = ($_GET['action'] == "edit") ? 'edit' : 'add';

if (isset($_GET['action']) && $action == "edit") {
    $page_subtitle = "Edit Testimonial";
    $id = isset($_GET['id']) ? $_GET['id'] : null;
    $data = ($id) ? mysqli_fetch_array($getdata->FetchQuery('SELECT * FROM `' . $table . '` WHERE id = ?', array($id))) : null;
} else {
    $page_subtitle = "Add Testimonial";
}
?>

<form method="post" action="testimonial.php?<?= ($action == 'add') ? 'action=add' : 'action=edit&id=' . $id; ?>">
    <div class="content-wrapper admin-editor">
        <!-- Content Header (Page header) -->
        <section class="content-header d-flex align-items-center justify-content-between">
            <div>
                <h1 style="margin: 0; padding-bottom: 1rem"><?= $page_subtitle; ?></h1>
                <ul class="breadcrumb p-0" style="background: transparent;">
                    <li><a href="dashboard.php"><i class="fa fa-dashboard"></i> Dashboard</a></li>
                    <li><a href="testimonial.php"><?= $page_title; ?></a></li>
                    <li class="active"><?= $page_subtitle; ?></li>
                </ul>
            </div>
            <div>
                <button type="submit" name="Submit" class="btn btn-success" id='saveButton'><i class="fa fa-floppy-o" aria-hidden="true"></i></button>
                <?= ($action === 'edit') ? "<a class='btn btn-primary' href='?action=add' role='button'><i class='fa fa-plus' aria-hidden='true'></i></a>" : ''; ?>
            </div>
        </section>

        <section class="content">
            <div class="row">
                <!-- Main content -->
                <section class="col-lg-9">
                    <div class="box box-info">
                        <?php include_once("components/alert-msg.php"); ?>
                        <div class="box-body">
                            <div class="row">
                                <div class="form-group col-sm-6">
                                    <div class="form-floating mb-3">
                                        <input type="text" <?= ($action === 'edit') ? 'value="' . h($data['name']) . '"' : ''; ?> class="form-control" id="name" name="name" placeholder="Name" required>
                                        <label for="name">Name <span style="color:red;">*</span></label>
                                    </div>
                                </div>
                                <div class="form-group col-sm-6">
                                    <div class="form-floating mb-3">
                                        <input type="text" <?= ($action === 'edit') ? 'value="' . h($data['designation']) . '"' : ''; ?> class="form-control" id="designation" name="designation" placeholder="Designation">
                                        <label for="designation">Designation</label>
                                    </div>
                                </div>
                                <div class="form-group col-sm-12">
                                    <div class="form-floating mb-3">
                                        <input type="text" <?= ($action === 'edit') ? 'value="' . h($data['company']) . '"' : ''; ?> class="form-control" id="company" name="company" placeholder="Company">
                                        <label for="company">Company</label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="description">Description <span class="text-danger">*</span></label>
                                <textarea class="ckeditor" id="description" name="description" cols="45" rows="7"><?= ($action === 'edit') ? h($data['description']) : ''; ?></textarea>
                            </div>
                </section>
                <section class="col-lg-3">
                    <div class="box box-info">
                        <div class="box-body">
                            <div class="mb-3">
                                <label>Status</label>
                                <div class="form-check">
                                    <input type="radio" class="form-check-input" name="status" value="1" <?= ($action === 'add' || ($action === 'edit' && $data['status'] === 1)) ? "checked" : ''; ?> required>
                                    <label class="form-check-label">Active</label>
                                </div>
                                <div class="form-check">
                                    <input type="radio" class="form-check-input" name="status" value="0" <?= ($action === 'edit' && $data['status'] === 0) ? "checked" : ''; ?> required>
                                    <label class="form-check-label">Inactive</label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Feature Image<span style="color:red;">*</span></label><br>
                                <?php
                                if (($action === 'edit') and (empty($data['image']) !== true)) {
                                    echo "<img src='$media_folder{$data['image']}' class='full-width' id='feature-image-img'>";
                                }
                                ?>
                                <!--<input type="file" name="image" accept="image/*">-->
                                <input type="hidden" name="image" value="<?= ($action === 'edit') ? h($data['image']) : ''; ?>" id='feature-image-input'>
                                <button type="button" class="btn btn-secondary btn-sm mt-3 openMediaModal" data-bs-toggle="modal" data-bs-target="#mediaSelectionModal"
                                    data-type="image" data-selection="single" group-id='feature-image'>
                                    Select Image
                                </button>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </section>
    </div>
</form>

<script src="assets/js/media-gallery.js"></script>
<?php include("components/media-gallery-modal.php"); ?>
<?php include("components/footer.php"); ?>
