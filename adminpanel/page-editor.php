<?php
require_once __DIR__ . '/components/config-header.php';
include("components/header.php");

$conn = db();
$page_title = "Page Management";
$table = "tbl_page";

$action = (isset($_GET['action']) && $_GET['action'] == "edit") ? 'edit' : 'add';

if ($action == "edit") {
    $page_subtitle = "Edit Page";
    $id = isset($_GET['id']) ? $_GET['id'] : null;
    $data = ($id) ? mysqli_fetch_array($getdata->FetchQuery('SELECT * FROM `' . $table . '` WHERE id = ?', array($id))) : null;
} else {
    $page_subtitle = "Add Page";
}
?>

<form method="post" action="page.php?<?= ($action == 'add') ? 'action=add' : 'action=edit&id=' . $id; ?>">
    <div class="content-wrapper admin-editor">
        <!-- Content Header (Page header) -->
        <section class="content-header d-flex align-items-center justify-content-between">
            <div>
                <h1 style="margin: 0; padding-bottom: 1rem"><?= $page_subtitle; ?></h1>
                <ul class="breadcrumb" style="background: transparent;padding: 0;margin-left: 0.5rem;">
                    <li><a href="dashboard.php"><i class="fa fa-dashboard"></i> Dashboard</a></li>
                    <li><a href="page.php"><?= $page_title; ?></a></li>
                    <li class="active"><?= $page_subtitle; ?></li>
                </ul>
            </div>
            <div>
                <button type="submit" name="Submit" class="btn btn-success" id='saveButton'><i class="fa fa-floppy-o" aria-hidden="true"></i></button>
                <? if ($action === 'edit'): ?>
                    <a class='btn btn-primary' href='?action=add' role='button'><i class='fa fa-plus' aria-hidden='true'></i></a>
                <? endif ?>
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
                                        <input type="text" <?= ($action === 'edit') ? 'value="' . h($data['title']) . '"' : ''; ?> class="form-control" id="title" name="title" placeholder="Title" required>
                                        <label for="title">Title <span style="color:red;">*</span></label>
                                    </div>
                                </div>
                                <div class="form-group col-sm-6">
                                    <div class="form-floating mb-3">
                                        <input type="text" <?= ($action === 'edit') ? 'value="' . h($data['url']) . '"' : ''; ?> class="form-control" id="url" name="url" placeholder="Page Url">
                                        <label for="url">Page Url</label>
                                    </div>
                                </div>
                            </div>

                            <fieldset class="editor-meta-fields">
                                    <legend>Search appearance</legend>
                                    <div class="row">
                                <div class="form-group col-lg-12">
                                    <div class="form-floating mb-3">
                                        <input type="text" <?= ($action === 'edit') ? 'value="' . h($data['meta_title']) . '"' : ''; ?> class="form-control" id="meta_title" name="meta_title" placeholder="Meta Title">
                                        <label for="meta_title">Meta Title</label>
                                    </div>
                                </div>
                                <div class="form-group col-lg-6">
                                    <div class="form-group mb-3">
                                        <label for="metaKeywords">Meta Keywords</label>
                                        <textarea class="form-control" style="height: 100px"
                                            id="metaKeywords" name="meta_keyword" placeholder="Meta Keywords"><?= ($action === 'edit') ? h($data['meta_keyword']) : ''; ?></textarea>
                                    </div>
                                </div>
                                <div class="form-group col-lg-6">
                                    <div class="form-group mb-3">
                                        <label for="metaDescription">Meta Description</label>
                                        <textarea class="form-control" style="height: 100px"
                                            id="metaDescription" name="meta_desc" placeholder="Meta Description"><?= ($action === 'edit') ? h($data['meta_desc']) : ''; ?></textarea>
                                    </div>
                                </div>
                            </div>
                                </fieldset>

                            <div class="form-group">
                                <label for="description">Description <span class="text-danger">*</span></label>
                                <textarea class="ckeditor" id="description" name="description" cols="45" rows="7"><?= ($action === 'edit') ? h($data['description']) : ''; ?></textarea>
                            </div>
                </section>
                <section class="col-lg-3">
                    <div class="box box-info">
                        <div class="box-body">
                            <div class="py-2">
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
                            <div class="form-group  py-2">
                                <label>Feature Image<span style="color:red;">*</span></label><br>
                                <?php
                                if (($action === 'edit') and (empty($data['image']) !== true)) {
                                    echo "<img src='$media_folder{$data['image']}' class='full-width' id='feature-image-img'>";
                                }
                                ?>
                                <input type="hidden" name="image" value="<?= ($action === 'edit') ? h($data['image']) : ''; ?>" id='feature-image-input'>
                                <button type="button" class="btn btn-secondary btn-sm mt-2 openMediaModal" data-bs-toggle="modal" data-bs-target="#mediaSelectionModal"
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
