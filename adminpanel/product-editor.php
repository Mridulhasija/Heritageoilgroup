<?php
require_once __DIR__ . '/components/config-header.php';
include("components/header.php");

$conn = db();
$page_title = "Product Management";
$table = "tbl_product";

if (isset($_GET['action']) && $_GET['action'] == "edit") {
    $action = "edit";
    $page_subtitle = "Edit Product";
    $id = $_GET['id'];
    $data = mysqli_fetch_array($getdata->FetchQuery("SELECT * FROM $table WHERE id = ?", [$id]));
} else {
    $action = "add";
    $page_subtitle = "Add Product";
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

<div class="content-wrapper admin-editor" style="padding-bottom:10rem">
    <form method="post" action="product.php?action=<?= ($action == 'add') ? 'add' : 'edit&id=' . $id; ?>" enctype="multipart/form-data">
        <!-- Content Header (Page header) -->
        <section class="content-header d-flex align-items-center justify-content-between">
            <div>
                <h1 style="margin: 0; padding-bottom: 1rem"><?= $page_subtitle; ?></h1>
                <ul class="breadcrumb p-0" style="background: transparent;">
                    <li><a href="dashboard.php"><i class="fa fa-dashboard"></i> Dashboard</a></li>
                    <li><a href="product.php"><?= $page_title; ?></a></li>
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
                <!--main content-->
                <section class="col-lg-9 connectedSortable">
                    <div class="box box-info">
                        <?php include_once("components/alert-msg.php"); ?>
                        <div class="box-body">

                            <div class="row">
                                <div class="form-group col-sm-6">
                                    <div class="form-floating mb-3">
                                        <input type="text"
                                            <?= ($action === 'edit') ? 'value="' . h($data['title']) . '"' : ''; ?>
                                            class="form-control" id="title" name="title" placeholder="Title" required>
                                        <label for="title">Title <span style="color:red;">*</span></label>
                                    </div>
                                </div>
                                <div class="form-group col-sm-6">
                                    <div class="form-floating mb-3">
                                        <input type="text"
                                            <?= ($action === 'edit') ? 'value="' . h($data['url']) . '"' : ''; ?>
                                            class="form-control" id="pageUrl" name="url" placeholder="Page Url">
                                        <label for="pageUrl">Page Url</label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <label for="product-heading">Page heading (H1)</label>
                                <input type="text" class="form-control" id="product-heading" name="heading" maxlength="255"
                                    value="<?= ($action === 'edit') ? h($data['heading'] ?? '') : ''; ?>"
                                    placeholder="Leave blank to use the product title">
                            </div>

                            <fieldset class="editor-meta-fields">
                                <legend>Search appearance</legend>
                                <div class="row">
                                    <div class="form-group col-lg-12">
                                        <div class="form-floating mb-3">
                                            <input type="text"
                                                <?= ($action === 'edit') ? 'value="' . h($data['meta_title']) . '"' : ''; ?>
                                                class="form-control" id="meta_title" name="meta_title" placeholder="Meta Title">
                                            <label for="meta_title">Meta Title</label>
                                        </div>
                                    </div>
                                    <div class="form-group col-lg-6">
                                        <div class="form-group mb-3">
                                            <label for="metaKeywords">Meta Keywords</label>
                                            <textarea class="form-control" style="height: 100px" id="metaKeywords"
                                                name="meta_keyword"
                                                placeholder="Meta Keywords"><?= ($action === 'edit') ? h($data['meta_keyword']) : ''; ?></textarea>
                                        </div>
                                    </div>
                                    <div class="form-group col-lg-6">
                                        <div class="form-group mb-3">
                                            <label for="metaDescription">Meta Description</label>
                                            <textarea class="form-control" style="height: 100px" id="metaDescription"
                                                name="meta_desc"
                                                placeholder="Meta Description"><?= ($action === 'edit') ? h($data['meta_desc']) : ''; ?></textarea>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>

                            <div class="form-group mb-3">
                                <label for="short_description">Short Description</label>
                                <textarea class="ckeditor" id="short_description" name="short_description" cols="45" rows="7"><?= ($action === 'edit') ? h($data['short_description']) : ''; ?></textarea>
                            </div>

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

                            <div class="form-group">
                                <label for="description">Description <span class="text-danger">*</span></label>
                                <textarea class="ckeditor" id="description" name="description" cols="45" rows="7"><?= ($action === 'edit') ? h($data['description']) : ''; ?></textarea>
                            </div>
                            <?php
                            $productFaqs = json_decode($data['faqs'] ?? '[]', true);
                            if (!is_array($productFaqs)) $productFaqs = [];
                            ?>
                            <fieldset class="faq-editor">
                                <legend>Product FAQs</legend>
                                <div class="faq-editor-heading">
                                    <p>Add questions and answers customers commonly need.</p>
                                    <span class="faq-count"><?= count($productFaqs) ?> added</span>
                                </div>
                                <div class="faq-items">
                                    <?php foreach ($productFaqs as $index => $item): ?>
                                        <div class="faq-item">
                                            <div class="faq-item-heading"><strong>Question <?= $index + 1 ?></strong><button type="button" class="btn btn-outline-danger btn-sm faq-remove"><i class="fa fa-trash-o" aria-hidden="true"></i> Remove</button></div>
                                            <div class="faq-item-fields">
                                                <div><label for="product-faq-question-<?= $index ?>">Question</label><input class="form-control" id="product-faq-question-<?= $index ?>" name="faq_question[]" value="<?= h($item['question'] ?? '') ?>" placeholder="Write a clear question"></div>
                                                <div><label for="product-faq-answer-<?= $index ?>">Answer</label><textarea class="form-control" id="product-faq-answer-<?= $index ?>" name="faq_answer[]" rows="3" placeholder="Write a clear, helpful answer"><?= h($item['answer'] ?? '') ?></textarea></div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <button type="button" class="btn btn-outline-primary btn-sm faq-add" data-prefix="product-faq"><i class="fa fa-plus" aria-hidden="true"></i> Add question</button>
                            </fieldset>
                        </div>
                    </div>
                </section>
                <!--left column-->
                <section class="col-lg-3 connectedSortable">
                    <div class="box box-info">
                        <div class="box-body">
                            <div class="form-group">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="product-status" name="status" value="1" <?= (($action === 'edit') && ($data['status'] === 1)) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="product-status">Status <span class="text-danger">*</span></label>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="form-check">
                                    <input type="checkbox" class="form-check-input" id="feature-product" name="feature" value="1" <?= (($action === 'edit') && ($data['feature'] === 1)) ? 'checked' : ''; ?>>
                                    <label class="form-check-label" for="feature-product">Feature Product <span class="text-danger">*</span></label>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Product Image<span style="color:red;">*</span></label><br>
                                <?php
                                if (($action === 'edit') and (empty($data['image']) !== true)) {
                                    echo "<img src='$media_folder{$data['image']}' class='full-width' id='product-image-img'>";
                                } ?>
                                <!--<input type="file" name="image" accept="image/*">-->
                                <input type="hidden" name="image" value="<?= ($action === 'edit') ? h($data['image']) : ''; ?>" id='product-image-input'>
                                <button type="button" class="btn btn-secondary btn-sm mt-3 openMediaModal" data-bs-toggle="modal" data-bs-target="#mediaSelectionModal"
                                    data-type="image" data-selection="single" group-id='product-image'>
                                    Select Product Image
                                </button>
                            </div>

                            <div class="accordion py-2" id="categoryAccordion">
                                <div class="accordion-item">
                                    <div class="accordion-header" id="categoryHeading">
                                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#categoryCollapse" aria-expanded="true" aria-controls="categoryCollapse">
                                            Category
                                        </button>
                                    </div>
                                    <div id="categoryCollapse" class="accordion-collapse collapse show" aria-labelledby="categoryHeading" data-bs-parent="#categoryAccordion">
                                        <div class="accordion-body">
                                            <div class="category-list">
                                                <!-- ########### CATEGORY START ###########-->
                                                <?php
                                                function displayCategories($conn, $parent_id, $level = 0, $selectedCat = [], $action = 'edit')
                                                {
                                                    $sql_cat = mysqli_query($conn, "SELECT * FROM tbl_product_category WHERE parent_id = '$parent_id'");
                                                    while ($res_cat = mysqli_fetch_array($sql_cat)) {
                                                        $checkedCat = ($action === 'edit' && in_array($res_cat['id'], $selectedCat)) ? 'checked' : '';
                                                ?>
                                                        <div class="category-item" style="margin-left: <?= $level * 20 ?>px;" data-parent="<?= $res_cat['parent_id'] ?>">
                                                            <input type="checkbox" class="check me-2" name="category[]" value="<?= $res_cat['id'] ?>" <?php echo $checkedCat; ?>>
                                                            <?= $res_cat['title']; ?>
                                                        </div>
                                                    <?php
                                                        // Recursive call to display subcategories
                                                        displayCategories($conn, $res_cat['id'], $level + 1, $selectedCat, $action);
                                                    }
                                                }

                                                $sql_cat = mysqli_query($conn, "SELECT * FROM tbl_product_category WHERE parent_id = '0'");
                                                if ($action === 'edit' && !empty($data['category'])) {
                                                    $selectedCat = explode(",", $data['category']);
                                                } else {
                                                    $selectedCat = [];
                                                }
                                                while ($res_cat = mysqli_fetch_array($sql_cat)) {
                                                    $checkedCat = ($action === 'edit' && in_array($res_cat['id'], $selectedCat)) ? 'checked' : '';
                                                    ?>
                                                    <div class="category-item" style="margin-left: 0;" data-parent="<?= $res_cat['parent_id'] ?>">
                                                        <input type="checkbox" class="check me-2" name="category[]" value="<?= $res_cat['id'] ?>" <?= $checkedCat ?>>
                                                        <?= $res_cat['title']; ?>
                                                    </div>
                                                <?php
                                                    // Display subcategories
                                                    displayCategories($conn, $res_cat['id'], 1, $selectedCat, $action);
                                                }
                                                ?>
                                                <!-- ########### CATEGORY END ###########-->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </section>

            </div>
        </section>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const editor = document.querySelector('.faq-editor');
        if (!editor) return;

        function refresh() {
            const items = editor.querySelectorAll('.faq-item');
            editor.querySelector('.faq-count').textContent = `${items.length} added`;
            items.forEach((item, index) => item.querySelector('.faq-item-heading strong').textContent = `Question ${index + 1}`);
        }
        editor.addEventListener('click', function(event) {
            const remove = event.target.closest('.faq-remove');
            if (remove) {
                remove.closest('.faq-item').remove();
                refresh();
                return;
            }
            if (!event.target.closest('.faq-add')) return;
            const index = editor.querySelectorAll('.faq-item').length;
            const row = document.createElement('div');
            row.className = 'faq-item';
            row.innerHTML = `<div class="faq-item-heading"><strong>Question</strong><button type="button" class="btn btn-outline-danger btn-sm faq-remove"><i class="fa fa-trash-o" aria-hidden="true"></i> Remove</button></div><div class="faq-item-fields"><div><label for="product-faq-question-${index}">Question</label><input class="form-control" id="product-faq-question-${index}" name="faq_question[]" placeholder="Write a clear question"></div><div><label for="product-faq-answer-${index}">Answer</label><textarea class="form-control" id="product-faq-answer-${index}" name="faq_answer[]" rows="3" placeholder="Write a clear, helpful answer"></textarea></div></div>`;
            editor.querySelector('.faq-items').append(row);
            refresh();
        });
    });
</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const checkboxes = document.querySelectorAll('.check');

        function updateParentCheckbox(checkbox) {
            const parentID = checkbox.closest('.category-item').getAttribute('data-parent');
            const parentCheckbox = document.querySelector(`.check[value='${parentID}']`);

            if (parentCheckbox) {
                if (checkbox.checked) {
                    parentCheckbox.checked = true;
                } else {
                    const siblingCheckboxes = checkbox.closest('.category-list').querySelectorAll(`.category-item[data-parent='${parentID}'] .check`);
                    let anyChecked = false;
                    siblingCheckboxes.forEach(function(sibling) {
                        if (sibling.checked) {
                            anyChecked = true;
                        }
                    });
                    if (!anyChecked) {
                        parentCheckbox.checked = false;
                    }
                }
                // Recur to update the parent's parent
                updateParentCheckbox(parentCheckbox);
            }
        }

        checkboxes.forEach(function(checkbox) {
            checkbox.addEventListener('change', function() {
                updateParentCheckbox(checkbox);
            });
        });
    });
</script>
<script src="assets/js/media-gallery.js"></script>
<?php include("components/media-gallery-modal.php"); ?>
<?php include("components/footer.php"); ?>