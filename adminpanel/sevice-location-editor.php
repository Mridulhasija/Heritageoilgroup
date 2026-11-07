<?php
require_once __DIR__ . '/components/config-header.php';
include("components/header.php");

$conn = db();
$page_title = "Sevice Area Content";
$table = "tbl_location";

$action = (($_GET['action'] ?? '') == "edit") ? 'edit' : 'add';

if (isset($_GET['action']) && $action == "edit") {
    $page_subtitle = "Edit ";
    $id = isset($_GET['id']) ? $_GET['id'] : null;
    $data = ($id) ? mysqli_fetch_array($getdata->FetchQuery('SELECT * FROM `' . $table . '` WHERE id = ?', array($id))) : null;
} else {
    $page_subtitle = "Add ";
}
$page_subtitle = $page_subtitle . $page_title;
$locationData = json_decode($data['data'] ?? '', true);
$locationData = is_array($locationData) ? $locationData : [];
$locationData = array_merge([
    'primary_commercial_hub' => '',
    'major_sub_locations' => [],
    'industrial_hubs' => [],
    'trade_and_logistics_hubs' => [],
], $locationData);
if (!is_scalar($locationData['primary_commercial_hub'])) {
    $locationData['primary_commercial_hub'] = '';
}
$listFields = [
    'major_sub_locations' => 'Major sub locations',
    'industrial_hubs' => 'Industrial hubs',
    'trade_and_logistics_hubs' => 'Trade and logistics hubs',
];
?>

<form method="post" action="sevice-location.php?<?= ($action == 'add') ? 'action=add' : 'action=edit&id=' . $id; ?>">
    <div class="content-wrapper admin-editor">
        <!-- Content Header (Page header) -->
        <section class="content-header d-flex align-items-center justify-content-between">
            <div>
                <h1 style="margin: 0; padding-bottom: 1rem"><?= $page_subtitle; ?></h1>
                <ul class="breadcrumb p-0" style="background: transparent;">
                    <li><a href="dashboard.php"><i class="fa fa-dashboard"></i> Dashboard</a></li>
                    <li><a href="sevice-location.php"><?= $page_title; ?></a></li>
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
                                <div class="form-group col-12">
                                    <div class="form-floating mb-3">
                                        <input type="text" <?= ($action === 'edit') ? 'value="' . h($data['title']) . '"' : ''; ?> class="form-control" id="title" name="title" placeholder="Title" required>
                                        <label for="title">Title <span style="color:red;">*</span></label>
                                    </div>
                                </div>
                                <div class="form-group col-12">
                                    <div class="form-floating mb-3">
                                        <input type="text" <?= ($action === 'edit') ? 'value="' . h($data['url']) . '"' : ''; ?> class="form-control" id="url" name="url" placeholder="URL">
                                        <label for="url">URL <span style="color:red;">*</span></label>
                                    </div>
                                </div>
                                <div class="form-group col-12">
                                    <label for="primary-commercial-hub">Primary commercial hub</label>
                                    <input class="form-control" id="primary-commercial-hub" type="text" value="<?= h($locationData['primary_commercial_hub']) ?>" placeholder="e.g. Mumbai">
                                </div>
                                <?php foreach ($listFields as $field => $label): ?>
                                    <div class="form-group col-12">
                                        <label for="<?= h($field) ?>"><?= h($label) ?></label>
                                        <div class="location-pill-editor" data-field="<?= h($field) ?>">
                                            <div class="location-pills" aria-live="polite"></div>
                                            <input class="form-control location-pill-input" type="text" placeholder="Type a name and press Enter or comma">
                                        </div>
                                        <small class="text-muted">Add each name separately. Press Enter or comma to create a pill.</small>
                                    </div>
                                <?php endforeach; ?>
                                <input type="hidden" name="data" id="location-data">
                            </div>
                        </div>
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
                        </div>
                    </div>
                </section>
            </div>
        </section>
    </div>
</form>
<script>
(() => {
    const initial = <?= json_encode($locationData, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
    const fields = ['major_sub_locations', 'industrial_hubs', 'trade_and_logistics_hubs'];
    const values = Object.fromEntries(fields.map(field => [field, Array.isArray(initial[field]) ? initial[field] : []]));
    const form = document.querySelector('form');
    const hidden = document.getElementById('location-data');
    const primary = document.getElementById('primary-commercial-hub');
    function sync() {
        hidden.value = JSON.stringify({
            primary_commercial_hub: primary.value.trim(),
            ...values
        });
    }
    function render(field, list) {
        const pills = list.querySelector('.location-pills');
        pills.replaceChildren();
        values[field].forEach((value, index) => {
            const pill = document.createElement('span');
            pill.className = 'location-pill';
            const text = document.createElement('span');
            text.textContent = value;
            const remove = document.createElement('button');
            remove.type = 'button';
            remove.className = 'location-pill-remove';
            remove.setAttribute('aria-label', `Remove ${value}`);
            remove.textContent = '×';
            remove.addEventListener('click', () => {
                values[field].splice(index, 1);
                render(field, list);
                sync();
            });
            pill.append(text, remove);
            pills.append(pill);
        });
    }
    document.querySelectorAll('.location-pill-editor').forEach(list => {
        const field = list.dataset.field;
        const input = list.querySelector('.location-pill-input');
        const add = text => {
            text.split(',').map(value => value.trim()).filter(Boolean).forEach(value => {
                if (!values[field].includes(value)) values[field].push(value);
            });
            render(field, list);
            sync();
        };
        render(field, list);
        input.addEventListener('keydown', event => {
            if (event.key === 'Enter' || event.key === ',') {
                event.preventDefault();
                add(input.value);
                input.value = '';
            }
            if (event.key === 'Backspace' && !input.value && values[field].length) {
                values[field].pop();
                render(field, list);
                sync();
            }
        });
        input.addEventListener('blur', () => {
            if (input.value.trim()) {
                add(input.value);
                input.value = '';
            }
        });
    });
    primary.addEventListener('input', sync);
    form.addEventListener('submit', sync);
    sync();
})();
</script>
<style>
.location-pills{display:flex;flex-wrap:wrap;gap:.5rem;margin-bottom:.5rem}.location-pill{display:inline-flex;align-items:center;gap:.4rem;background:#e8f2f5;border:1px solid #bfd5dd;border-radius:999px;padding:.25rem .65rem}.location-pill-remove{border:0;background:none;font-size:1.15rem;line-height:1;cursor:pointer;color:#475569}
</style>
<?php include("components/footer.php"); ?>
