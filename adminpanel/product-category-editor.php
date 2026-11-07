<?php
require_once __DIR__ . '/components/config-header.php';
include("components/header.php");

$conn = db();
$page_title = "Product Category";
$table = "tbl_product_category";

$action = (($_GET['action'] ?? '') == "edit") ? 'edit' : 'add';

if (isset($_GET['action']) && $action == "edit") {
    $page_subtitle = "Edit Category";
    $id = isset($_GET['id']) ? $_GET['id'] : null;
    $data = ($id) ? mysqli_fetch_array($getdata->FetchQuery('SELECT * FROM `' . $table . '` WHERE id = ?', array($id))) : null;
} else {
    $page_subtitle = "Add Category";
}
function GiveVal($var)
{
    global $action, $data;
    return ($action === 'edit' && floatval($var) !== 0.0) ? "value='" . $var . "'" : '';
}
$unitOptions = [
    'KGM' => 'Kilogram (kg)',
    'GRM' => 'Gram (g)',
    'TNE' => 'Metric tonne',
    'LBR' => 'Pound (lb)',
    'LTR' => 'Liter (L)',
    'MLT' => 'Milliliter (ml)',
    'GLL' => 'US gallon',
    'C62' => 'Piece / unit',
    'BG' => 'Bag',
    'BO' => 'Bottle',
    'CT' => 'Carton',
];
$selectedUnit = ($action === 'edit' && isset($data['unit']) && isset($unitOptions[$data['unit']])) ? $data['unit'] : 'C62';
?>

<script src="assets/js/price-validation.js"></script>

<form method="post"
    action="product-category.php?<?= ($action == 'add') ? 'action=add' : 'action=edit&id=' . $id; ?>">
    <div class="content-wrapper admin-editor category-editor">
        <!-- Content Header (Page header) -->
        <section class="content-header d-flex align-items-center justify-content-between category-editor-header">
            <div>
                <h1 style="margin: 0; padding-bottom: 1rem">
                    <?= $page_subtitle; ?>
                </h1>
                <ul class="breadcrumb" style="background: transparent;padding: 0;margin-left: 0.5rem;">
                    <li><a href="dashboard.php"><i class="fa fa-dashboard"></i> Dashboard</a></li>
                    <li><a href="product-category.php<?= isset($data['parent_id']) && ($data['parent_id']) !== 0 ? '?id=' . $data['parent_id'] : '' ?>"><?= $page_title; ?></a>
                    </li>
                    <li class="active"><?= $page_subtitle; ?></li>
                </ul>
            </div>
            <div>
                <button type="submit" name="Submit" class="btn btn-primary" id='saveButton'><i class="fa fa-floppy-o"
                        aria-hidden="true"></i> Save</button>
                <?= ($action === 'edit') ? "<a class='btn btn-outline-primary' href='?action=add&parent_id=" . $data['parent_id'] . "' role='button'><i class='fa fa-plus' aria-hidden='true'></i> Add category</a>" : ''; ?>
            </div>
        </section>

        <section class="content">
            <div class="row">
                <!-- Main content -->
                <section class="col-lg-9">
                    <div class="box box-info category-editor-main">
                        <?php include_once("components/alert-msg.php"); ?>
                        <div class="box-body">
                            <div class="row">
                                <input type="hidden"
                                    value="<?= $action === 'add' && isset($_GET['parent_id']) ? $_GET['parent_id'] : 0 ?>"
                                    name="parent_id">
                                <div class="form-group col-sm-6">
                                    <div class="form-floating mb-3">
                                        <input type="text"
                                            <?= ($action === 'edit') ? 'value="' . h($data['title'] ?? '') . '"' : ''; ?>
                                            class="form-control" id="title" name="title" placeholder="Title" required>
                                        <label for="title">Title <span style="color:red;">*</span></label>
                                    </div>
                                </div>
                                <div class="form-group col-sm-6">
                                    <div class="form-floating mb-3">
                                        <input type="text"
                                            <?= ($action === 'edit') ? 'value="' . h($data['url'] ?? '') . '"' : ''; ?>
                                            class="form-control" id="pageUrl" name="url" placeholder="Page Url">
                                        <label for="pageUrl">Page Url</label>
                                    </div>
                                </div>
                            </div>

                            <section class="category-editor-section" aria-labelledby="category-content-heading">
                                <div class="category-section-heading">
                                    <h2 id="category-content-heading">Category page</h2>
                                    <p>Content shown on the category page without a location.</p>
                                </div>
                                <div class="form-group mb-3"><label for="heading">Page heading (H1)</label>
                                    <input class="form-control" id="heading" name="heading" value="<?= h($data['heading'] ?? '') ?>">
                                </div>
                                <fieldset class="category-seo-fields">
                                    <legend>Search appearance</legend>
                                    <div class="row">
                                        <div class="form-group col-lg-12">
                                            <div class="form-floating mb-3">
                                                <input type="text"
                                                    value="<?= h($data['meta_title'] ?? '') ?>"
                                                    class="form-control" id="meta_title" name="meta_title" placeholder="Meta Title">
                                                <label for="meta_title">Meta Title</label>
                                            </div>
                                        </div>
                                        <div class="form-group col-lg-6 mb-3">
                                            <label for="metaKeywords">Meta keywords</label>
                                            <textarea class="form-control" id="metaKeywords" name="meta_keyword" rows="4" placeholder="Meta keywords"><?= h($data['meta_keyword'] ?? '') ?></textarea>
                                        </div>
                                        <div class="form-group col-lg-6 mb-3">
                                            <label for="metaDescription">Meta description</label>
                                            <textarea class="form-control" id="metaDescription" name="meta_desc" rows="4" placeholder="Meta description"><?= h($data['meta_desc'] ?? '') ?></textarea>
                                        </div>
                                    </div>
                                </fieldset>

                                <div class="row mb-3 price-group">
                                    <div class="form-group col-lg-4">
                                        <div class="form-floating">
                                            <input type="number" step="any" min="0.01"
                                                <?= $action === 'edit' ? GiveVal($data['price']) : '' ?>
                                                class="form-control price" id="price" name="price" placeholder="Price">
                                            <label for="price">Price</label>
                                        </div>
                                    </div>
                                    <div class="form-group col-lg-4">
                                        <div class="form-floating">
                                            <input type="number" step="any" min="0.01"
                                                <?= $action === 'edit' ? GiveVal($data['discounted_price']) : '' ?>
                                                class="form-control discount-price" id="discount-price" name="discounted_price"
                                                placeholder="Discount Price">
                                            <label for="discount-price">Discount Price</label>
                                        </div>
                                    </div>
                                    <div class="form-group col-lg-4">
                                        <div class="form-floating">
                                            <select class="form-select" id="unit" name="unit" required>
                                                <?php foreach ($unitOptions as $code => $label): ?>
                                                    <option value="<?= h($code) ?>" <?= $selectedUnit === $code ? 'selected' : '' ?>><?= h($label) ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                            <label for="unit">Price unit</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="short_description">Short description</label>
                                    <textarea class="form-control" id="short_description" name="short_description" rows="4" placeholder="Short description"><?= ($action === 'edit') ? h($data['short_description'] ?? '') : ''; ?></textarea>
                                </div>

                                <div class="form-group">
                                    <label for="category_description">Description</label>
                                    <textarea class="ckeditor" id="category_description" name="description" cols="45"
                                        rows="7"><?= ($action === 'edit') ? h($data['description']) : ''; ?></textarea>
                                </div>
                                <?php foreach (['faqs' => 'Category FAQs'] as $field => $label):
                                    $items = json_decode($data[$field] ?? '[]', true);
                                    if (!is_array($items)) $items = [];
                                    $prefix = $field === 'faqs' ? 'faq' : 'service_faq'; ?>
                                    <fieldset class="faq-editor">
                                        <legend><?= $label ?></legend>
                                        <div class="faq-editor-heading">
                                            <p>Add questions and answers customers commonly need.</p>
                                            <span class="faq-count"><?= count($items) ?> added</span>
                                        </div>
                                        <div class="faq-items">
                                            <?php foreach ($items as $index => $item): ?>
                                                <div class="faq-item">
                                                    <div class="faq-item-heading">
                                                        <strong>Question <?= $index + 1 ?></strong>
                                                        <button type="button" class="btn btn-outline-danger btn-sm faq-remove"><i class="fa fa-trash-o" aria-hidden="true"></i> Remove</button>
                                                    </div>
                                                    <div class="faq-item-fields">
                                                        <div><label for="<?= $prefix ?>-question-<?= $index ?>">Question</label><input class="form-control" id="<?= $prefix ?>-question-<?= $index ?>" name="<?= $prefix ?>_question[]" value="<?= h($item['question'] ?? '') ?>" placeholder="e.g. What is the minimum order quantity?"></div>
                                                        <div><label for="<?= $prefix ?>-answer-<?= $index ?>">Answer</label><textarea class="form-control" id="<?= $prefix ?>-answer-<?= $index ?>" name="<?= $prefix ?>_answer[]" rows="3" placeholder="Write a clear, helpful answer"><?= h($item['answer'] ?? '') ?></textarea></div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        <button type="button" class="btn btn-outline-primary btn-sm faq-add" data-prefix="<?= $prefix ?>"><i class="fa fa-plus" aria-hidden="true"></i> Add question</button>
                                    </fieldset>
                                <?php endforeach; ?>
                            </section>
                            <details class="category-editor-section service-editor-section">
                                <summary class="category-section-heading">
                                    <span><strong id="service-content-heading">Service-area category page</strong><small>Optional content for location/category URLs. Use {{location}}, location data tokens, and catname.</small></span>
                                    <i class="fa fa-angle-down" aria-hidden="true"></i>
                                </summary>
                                <div class="service-editor-fields">
                                    <div class="form-group mb-3"><label for="service_heading">Page heading (H1)</label>
                                        <input class="form-control" id="service_heading" name="service_heading" value="<?= h($data['service_heading'] ?? '') ?>">
                                    </div>
                                    <fieldset class="category-seo-fields">
                                        <legend>Search appearance</legend>
                                        <div class="form-group mb-3"><label for="service_meta_title">Meta title</label>
                                            <input class="form-control" id="service_meta_title" name="service_meta_title" value="<?= h($data['service_meta_title'] ?? '') ?>">
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-lg-6 mb-3"><label for="service_meta_keyword">Meta keywords</label>
                                                <textarea class="form-control" id="service_meta_keyword" name="service_meta_keyword" rows="3"><?= h($data['service_meta_keyword'] ?? '') ?></textarea>
                                            </div>
                                            <div class="form-group col-lg-6 mb-3"><label for="service_meta_desc">Meta description</label>
                                                <textarea class="form-control" id="service_meta_desc" name="service_meta_desc" rows="3"><?= h($data['service_meta_desc'] ?? '') ?></textarea>
                                            </div>
                                        </div>
                                    </fieldset>
                                    <div class="form-group mb-3"><label for="service_description">Service-area description</label>
                                        <textarea class="ckeditor" id="service_description" name="service_description"><?= h($data['service_description'] ?? '') ?></textarea>
                                    </div>
                                    <?php foreach (['service_faqs' => 'Service-area FAQs'] as $field => $label):
                                        $items = json_decode($data[$field] ?? '[]', true);
                                        if (!is_array($items)) $items = [];
                                        $prefix = $field === 'faqs' ? 'faq' : 'service_faq'; ?>
                                        <fieldset class="faq-editor">
                                            <legend><?= $label ?></legend>
                                            <div class="faq-editor-heading">
                                                <p>Add questions and answers for location-specific visitors.</p>
                                                <span class="faq-count"><?= count($items) ?> added</span>
                                            </div>
                                            <div class="faq-items">
                                                <?php foreach ($items as $index => $item): ?>
                                                    <div class="faq-item">
                                                        <div class="faq-item-heading">
                                                            <strong>Question <?= $index + 1 ?></strong>
                                                            <button type="button" class="btn btn-outline-danger btn-sm faq-remove"><i class="fa fa-trash-o" aria-hidden="true"></i> Remove</button>
                                                        </div>
                                                        <div class="faq-item-fields">
                                                            <div><label for="<?= $prefix ?>-question-<?= $index ?>">Question</label><input class="form-control" id="<?= $prefix ?>-question-<?= $index ?>" name="<?= $prefix ?>_question[]" value="<?= h($item['question'] ?? '') ?>" placeholder="e.g. Which areas do you deliver to?"></div>
                                                            <div><label for="<?= $prefix ?>-answer-<?= $index ?>">Answer</label><textarea class="form-control" id="<?= $prefix ?>-answer-<?= $index ?>" name="<?= $prefix ?>_answer[]" rows="3" placeholder="Write a clear, helpful answer"><?= h($item['answer'] ?? '') ?></textarea></div>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                            <button type="button" class="btn btn-outline-primary btn-sm faq-add" data-prefix="<?= $prefix ?>"><i class="fa fa-plus" aria-hidden="true"></i> Add question</button>
                                        </fieldset>
                                    <?php endforeach; ?>
                                </div>
                            </details>
                            <button type="submit" name="Submit" class="btn btn-primary mt-3">Save changes</button>
                        </div>

                    </div>
                </section>
                <section class="col-lg-3">
                    <div class="box box-info">
                        <div class="box-body">
                            <div class="mb-3">
                                <label>Status</label>
                                <div class="form-check">
                                    <input type="radio" class="form-check-input" name="status" value="1"
                                        <?= ($action === 'add' || ($action === 'edit' && $data['status'] === 1)) ? "checked" : ''; ?>
                                        required>
                                    <label class="form-check-label">Active</label>
                                </div>
                                <div class="form-check">
                                    <input type="radio" class="form-check-input" name="status" value="0"
                                        <?= ($action === 'edit' && $data['status'] === 0) ? "checked" : ''; ?>
                                        required>
                                    <label class="form-check-label">Inactive</label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Category Icon</label><br>
                                <?php
                                if (($action === 'edit') and (empty($data['icon']) !== true)) {
                                    echo "<img src='$media_folder{$data['icon']}' class='full-width' id='icon-img'>";
                                }
                                ?>
                                <input type="hidden" name="icon"
                                    value="<?= ($action === 'edit') ? h($data['icon']) : ''; ?>"
                                    id='icon-input'>
                                <button type="button" class="btn btn-secondary btn-sm mt-3 openMediaModal"
                                    data-bs-toggle="modal" data-bs-target="#mediaSelectionModal" data-type="image"
                                    data-selection="single" group-id='icon'>
                                    Select Icon
                                </button>
                            </div>
                            <div class="form-group">
                                <label>Category Image</label><br>
                                <?php
                                if (($action === 'edit') and (empty($data['image']) !== true)) {
                                    echo "<img src='$media_folder{$data['image']}' class='full-width' id='feature-image-img'>";
                                }
                                ?>
                                <input type="hidden" name="image"
                                    value="<?= ($action === 'edit') ? h($data['image']) : ''; ?>"
                                    id='feature-image-input'>
                                <button type="button" class="btn btn-secondary btn-sm mt-3 openMediaModal"
                                    data-bs-toggle="modal" data-bs-target="#mediaSelectionModal" data-type="image"
                                    data-selection="single" group-id='feature-image'>
                                    Select Image
                                </button>
                            </div>
                            <div class="form-group">
                                <label>Banner Image</label><br>
                                <?php
                                if (($action === 'edit') and (empty($data['banner']) !== true)) {
                                    echo "<img src='$media_folder{$data['banner']}' class='full-width' id='banner-image-img'>";
                                }
                                ?>
                                <input type="hidden" name="banner"
                                    value="<?= ($action === 'edit') ? h($data['banner']) : ''; ?>"
                                    id='banner-image-input'>
                                <button type="button" class="btn btn-secondary btn-sm mt-3 openMediaModal"
                                    data-bs-toggle="modal" data-bs-target="#mediaSelectionModal" data-type="image"
                                    data-selection="single" group-id='banner-image'>
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

<script>
    function updateFaqCount(editor) {
        const items = editor.querySelectorAll('.faq-item');
        const count = items.length;
        editor.querySelector('.faq-count').textContent = `${count} added`;
        items.forEach((item, index) => item.querySelector('.faq-item-heading strong').textContent = `Question ${index + 1}`);
    }

    document.querySelectorAll('.faq-editor').forEach(editor => {
        editor.addEventListener('click', event => {
            const removeButton = event.target.closest('.faq-remove');
            if (removeButton) {
                removeButton.closest('.faq-item').remove();
                updateFaqCount(editor);
                return;
            }
            const addButton = event.target.closest('.faq-add');
            if (!addButton) return;
            const prefix = addButton.dataset.prefix;
            const rowId = `${prefix}-${Date.now()}-${Math.random().toString(36).slice(2, 8)}`;
            const row = document.createElement('div');
            row.className = 'faq-item';
            row.innerHTML = `<div class="faq-item-heading"><strong>Question</strong><button type="button" class="btn btn-outline-danger btn-sm faq-remove"><i class="fa fa-trash-o" aria-hidden="true"></i> Remove</button></div><div class="faq-item-fields"><div><label for="${rowId}-question">Question</label><input class="form-control" id="${rowId}-question" name="${prefix}_question[]" placeholder="Write a clear question"></div><div><label for="${rowId}-answer">Answer</label><textarea class="form-control" id="${rowId}-answer" name="${prefix}_answer[]" rows="3" placeholder="Write a clear, helpful answer"></textarea></div></div>`;
            editor.querySelector('.faq-items').append(row);
            updateFaqCount(editor);
        });
    });
</script>