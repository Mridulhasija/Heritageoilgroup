<div class="content-wrapper">
    <!-- Section for when type is set -->
    <section class="content-header d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
        <div class="d-flex align-items-center">
            <h1 class="m-0" style="font-size:24px"><?= $page_title; ?></h1>
            <?php if (isset($editor_url) && $editor_url && isset($actions['add']) && $actions['add']): ?>
                <a class="btn btn-outline-primary mx-3"
                    href="<?php echo $editor_url ?>?action=add<?php echo isset($moreAddParms) ? $moreAddParms : '' ?>"
                    role="button">Add New</a>
            <?php endif ?>
        </div>
        <?php
        $new_links = [
            "<i class='fa fa-home'></i>Home" => SITE_URL,
            "<i class='fa fa-dashboard'></i>Dashboard" => "dashboard.php",
        ];

        if (isset($page_title)) {
            $new_links[$page_title] = $currentUrl;
        }

        if (isset($page_subtitle)) {
            $new_links[$page_subtitle] = "";
        }

        if (isset($breadcrumb)) {
            $breadcrumb = $new_links + $breadcrumb;
        } else {
            $breadcrumb = $new_links;
        }

        echo "<ol class='breadcrumb mb-0'>";
        foreach ($breadcrumb as $text => $link) {
            echo "<li><a href='$link'>$text</a></li>";
        }
        echo "</ol>";
        ?>

    </section>

    <?php if (isset($_GET['action']) && $_GET['action'] == 'import'): ?>

        <!-- Main content -->
        <section class="content">
            <div class="container">
                <div class="cardshadow-sm" style="max-width: 680px; margin: 0 auto;">
                    <div class="card-header" style="border-bottom: 2px solid #f0f0f0; padding: 1.1rem 1.5rem;">
                        <div class="d-flex align-items-center gap-2">
                            <i class="fa fa-upload text-primary" style="font-size: 1.15rem;"></i>
                            <h5 class="mb-0 fw-semibold" style="font-size: 1rem; letter-spacing: 0.01em;">Import Data from
                                CSV</h5>
                        </div>
                        <?php include_once("components/alert-msg.php"); ?>
                    </div>
                    <div class="card-body" style="padding: 1.5rem;">

                        <!-- File selector row -->
                        <div class="d-flex align-items-center justify-content-between gap-3 pb-3"
                            style="border-bottom: 1px solid #f4f4f4;">
                            <div class="d-flex align-items-center gap-3 flex-grow-1">
                                <div>
                                    <p class="mb-1 fw-semibold text-dark" style="font-size: 0.85rem;">CSV File <span
                                            class="text-danger">*</span></p>
                                    <p class="mb-0 text-muted" id="csv-file-label" style="font-size: 0.78rem;">No file
                                        selected</p>
                                </div>
                                <input type="hidden" name="image" id="import-csv-input">
                                <button type="button" class="btn btn-outline-secondary btn-sm openMediaModal flex-shrink-0"
                                    data-bs-toggle="modal" data-bs-target="#mediaSelectionModal" data-type="csv"
                                    data-selection="single" group-id="import-csv"
                                    style="font-size: 0.8rem; white-space: nowrap;">
                                    <i class="fa fa-folder-open-o me-1"></i> Browse
                                </button>
                            </div>
                            <button id="import-btn" class="btn btn-primary btn-sm d-none flex-shrink-0"
                                style="font-size: 0.85rem; min-width: 110px;">
                                <i class="fa fa-play me-1"></i> Start Import
                            </button>
                        </div>

                        <!-- Progress Bar -->
                        <div class="mt-4 d-none" id="import-progress">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="text-muted" style="font-size: 0.78rem;">Progress</span>
                                <span id="progress-pct" class="text-muted" style="font-size: 0.78rem;">0%</span>
                            </div>
                            <div class="progress" style="height: 10px; border-radius: 6px;">
                                <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar"
                                    style="width: 0%;" id="progress-bar" aria-valuenow="0" aria-valuemin="0"
                                    aria-valuemax="100"></div>
                            </div>
                            <div id="import-status" class="text-muted mt-2 d-none" style="font-size: 0.78rem;"></div>
                        </div>

                        <!-- Errors -->
                        <div id="import-errors" class="alert alert-danger mt-3 d-none"
                            style="max-height: 300px; overflow-y: auto; font-size: 0.82rem; border-radius: 6px;">
                        </div>

                        <!-- Completion message -->
                        <div id="message" class="mt-4 d-none" style="border-top: 1px solid #f4f4f4; padding-top: 1.1rem;">
                            <p id="success-message" class="mb-3" style="font-size: 0.9rem;"></p>
                            <a id="go-back-btn" href="#" class="btn btn-outline-secondary btn-sm">
                                <i class="fa fa-arrow-left me-1"></i> Go Back
                            </a>
                        </div>

                    </div>
                </div>
            </div>
        </section>

        <script>
            const tableName = "<?php echo $table; ?>";
        </script>
        <script src="assets/js/import-csv.js"></script>
        <script src="assets/js/media-gallery.js"></script>
        <?php include("components/media-gallery-modal.php"); ?>

    <?php elseif (isset($_GET['action']) && $_GET['action'] == 'export'): ?>

        <!-- Main content -->
        <section class="content">
            <div class="container">
                <div class="card shadow-sm" style="max-width: 680px; margin: 0 auto;">
                    <div class="card-header" style="border-bottom: 2px solid #f0f0f0; padding: 1.1rem 1.5rem;">
                        <div class="d-flex align-items-center gap-2">
                            <i class="fa fa-download text-success" style="font-size: 1.15rem;"></i>
                            <h5 class="mb-0 fw-semibold" style="font-size: 1rem; letter-spacing: 0.01em;">Export Data to CSV
                            </h5>
                        </div>
                        <?php include_once("components/alert-msg.php"); ?>
                    </div>
                    <div class="card-body" style="padding: 1.5rem;">

                        <!-- Description + Button row -->
                        <div class="d-flex align-items-center justify-content-between gap-3 pb-3"
                            style="border-bottom: 1px solid #f4f4f4;">
                            <p class="mb-0 text-muted" style="font-size: 0.85rem;">
                                Exports all records from <strong><?php echo h($table); ?></strong> to a CSV
                                file saved in the media library.
                            </p>
	                            <div class="d-flex align-items-center gap-2 flex-shrink-0">
	                                <div class="btn-group">
	                                    <button type="button" class="btn btn-outline-secondary btn-sm dropdown-toggle"
	                                        data-bs-toggle="dropdown" aria-expanded="false"
	                                        style="font-size: 0.85rem; white-space: nowrap;">
	                                        <i class="fa fa-file-text-o me-1"></i> CSV
	                                    </button>
	                                    <ul class="dropdown-menu dropdown-menu-end">
	                                        <li>
	                                            <a class="dropdown-item"
	                                                href="api/export-template-csv.php?table=<?= urlencode($table); ?>" download>
	                                                Get Template
	                                            </a>
	                                        </li>
	                                    </ul>
	                                </div>
	                                <button id="export-btn" class="btn btn-success btn-sm"
	                                    style="font-size: 0.85rem; min-width: 110px;">
	                                    <i class="fa fa-play me-1"></i> Start Export
	                                </button>
	                            </div>
	                        </div>

                        <!-- Progress Container -->
                        <div id="progress-container" class="mt-4" style="display: none;">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="text-muted" style="font-size: 0.78rem;">Progress</span>
                                <span id="export-pct" class="text-muted" style="font-size: 0.78rem;">0%</span>
                            </div>
                            <div class="progress" style="height: 10px; border-radius: 6px;">
                                <div id="progress-bar" class="progress-bar progress-bar-striped progress-bar-animated"
                                    role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0"
                                    aria-valuemax="100"></div>
                            </div>
                            <div id="export-status" class="text-muted mt-2" style="display: none; font-size: 0.78rem;">
                            </div>
                            <div id="warning-message" class="mt-2"
                                style="display: none; font-size: 0.8rem; color: #c87f0a;">
                                <i class="fa fa-exclamation-triangle me-1"></i>
                                Export in progress — don't close this page.
                            </div>
                        </div>

                        <!-- Completion: Download + Back -->
                        <div id="file-saved-message" class="mt-4"
                            style="display: none; border-top: 1px solid #f4f4f4; padding-top: 1.1rem;">
                            <p class="mb-3 text-muted" style="font-size: 0.85rem;">
                                File saved to the <a href="media-gallery.php">Media Library</a>. Download it below.
                            </p>
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <a id="download-btn" class="btn btn-success btn-sm" href="" download
                                    style="display: none; font-size: 0.85rem;">
                                    <i class="fa fa-download me-1"></i> Download CSV
                                </a>
                                <a id="go-back-btn" href="#" class="btn btn-outline-secondary btn-sm"
                                    style="font-size: 0.85rem;">
                                    <i class="fa fa-arrow-left me-1"></i> Go Back
                                </a>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </section>

        <script>
            const tableName = "<?php echo $table; ?>";
        </script>
        <script src="assets/js/export-csv.js"></script>


    <?php else: ?>

        <!-- Main content -->
        <section class="content">
            <div class='box'>
                <div class="box-header d-flex justify-items-center">
                    <?php include_once("components/alert-msg.php"); ?>
                </div>

                <?php
                // Pagination settings
                $items_per_page = isset($items_per_page) ? $items_per_page : 10;
                $current_page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
                if ($current_page < 1)
                    $current_page = 1;

                $hasTitleColumn = isset($tableContent) && is_array($tableContent) && (isset($tableContent['title']) || in_array('title', $tableContent, true));
                $search_title = ($hasTitleColumn && isset($_GET['search_title'])) ? trim($_GET['search_title']) : '';
                $search_params = [];
                $search_sql = '';
                if ($hasTitleColumn && $search_title !== '') {
                    $search_sql = (isset($fetchWhere) && trim($fetchWhere) !== '') ? ' AND title LIKE ?' : ' WHERE title LIKE ?';
                    $search_params[] = '%' . $search_title . '%';
                }

                // Count total records
                $count_sql = "SELECT COUNT(*) as total FROM $table " . (isset($fetchWhere) ? trim($fetchWhere) : '') . $search_sql;
                $count_result = $getdata->FetchQuery($count_sql, $search_params);
                $count_row = mysqli_fetch_assoc($count_result);
                $total_records = $count_row['total'];
                $total_pages = ceil($total_records / $items_per_page);

                // Ensure current page is within bounds
                if ($current_page > $total_pages && $total_pages > 0)
                    $current_page = $total_pages;

                // Calculate offset
                $offset = ($current_page - 1) * $items_per_page;

                ob_start();
                ?>
                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                    <nav aria-label="Page navigation">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 px-2">
                            <div class="text-muted">
                                Showing <?= (($current_page - 1) * $items_per_page) + 1 ?> to
                                <?= min($current_page * $items_per_page, $total_records) ?> of <?= $total_records ?> entries
                            </div>
                            <ul class="pagination mb-0">
                                <?php
                                // Build base URL preserving existing query parameters
                                $query_params = $_GET;
                                unset($query_params['page']);
                                $base_url = '?' . http_build_query($query_params) . (empty($query_params) ? '' : '&');
                                ?>

                                <!-- Previous Button -->
                                <li class="page-item <?= ($current_page <= 1) ? 'disabled' : '' ?>">
                                    <a class="page-link" href="<?= $base_url ?>page=<?= $current_page - 1 ?>"
                                        aria-label="Previous">
                                        <span aria-hidden="true">&laquo;</span>
                                    </a>
                                </li>

                                <?php
                                // Calculate start and end page numbers
                                $start_page = max(1, $current_page - 2);
                                $end_page = min($total_pages, $current_page + 2);

                                // Show first page if not in range
                                if ($start_page > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="<?= $base_url ?>page=1">1</a>
                                    </li>
                                    <?php if ($start_page > 2): ?>
                                        <li class="page-item disabled"><span class="page-link">...</span></li>
                                    <?php endif; ?>
                                <?php endif; ?>

                                <!-- Page Numbers -->
                                <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                                    <li class="page-item <?= ($i == $current_page) ? 'active' : '' ?>">
                                        <a class="page-link" href="<?= $base_url ?>page=<?= $i ?>"><?= $i ?></a>
                                    </li>
                                <?php endfor; ?>

                                <?php
                                // Show last page if not in range
                                if ($end_page < $total_pages): ?>
                                    <?php if ($end_page < $total_pages - 1): ?>
                                        <li class="page-item disabled"><span class="page-link">...</span></li>
                                    <?php endif; ?>
                                    <li class="page-item">
                                        <a class="page-link" href="<?= $base_url ?>page=<?= $total_pages ?>"><?= $total_pages ?></a>
                                    </li>
                                <?php endif; ?>

                                <!-- Next Button -->
                                <li class="page-item <?= ($current_page >= $total_pages) ? 'disabled' : '' ?>">
                                    <a class="page-link" href="<?= $base_url ?>page=<?= $current_page + 1 ?>" aria-label="Next">
                                        <span aria-hidden="true">&raquo;</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </nav>
                <?php endif; ?>
                <?php $pagination_html = ob_get_clean(); ?>

                <div class="<?php if (!empty($pagination_html) ? "mt-3 mb-4" : "") ?>">
                    <?= $pagination_html; ?>
                </div>
                <div class="block d-md-flex justify-content-between mb-3 px-2">
                    <div class="d-flex gap-3">
                        <select class="form-select" id="actionSelect">
                            <option selected>select option</option>
                            <option value="bulk-delete">bulk delete</option>
                        </select>
                        <button type="button" class="btn btn-primary" id="applyButton">Apply</button>
                    </div>

                    <div class="d-flex align-items-center gap-2 flex-wrap justify-content-end">
                        <?php if ($hasTitleColumn): ?>
                            <form method="GET" class="d-flex align-items-center gap-2 mb-0">
                                <?php
                                foreach ($_GET as $k => $v) {
                                    if ($k === 'page' || $k === 'search_title') {
                                        continue;
                                    }
                                    echo '<input type="hidden" name="' . h($k) . '" value="' . h($v) . '">';
                                }
                                ?>
                                <input type="text" class="form-control" name="search_title" placeholder="Search by title"
                                    value="<?= h($search_title); ?>" style="max-width: 260px;">
                                <button type="submit" class="btn btn-outline-secondary">Search</button>
                                <?php if ($search_title !== ''): ?>
                                    <?php
                                    $clear_params = $_GET;
                                    unset($clear_params['search_title'], $clear_params['page']);
                                    $clear_url = '?' . http_build_query($clear_params);
                                    ?>
                                    <a href="<?= $clear_url ?>" class="btn btn-outline-danger">Clear</a>
                                <?php endif; ?>
                            </form>
                        <?php endif; ?>

	                        <div class="d-none d-md-block">
	                            <div class="btn-group">
		                                <button type="button" class="btn btn-outline-secondary dropdown-toggle"
		                                    data-bs-toggle="dropdown" aria-expanded="false">
		                                    <i class="fa fa-file-text-o me-1"></i> CSV
		                                </button>
	                                <ul class="dropdown-menu dropdown-menu-end">
	                                    <li><a class="dropdown-item" href="?action=import">Import</a></li>
	                                    <li><a class="dropdown-item" href="?action=export">Export</a></li>
	                                    <li>
	                                        <a class="dropdown-item"
	                                            href="api/export-template-csv.php?table=<?= urlencode($table); ?>" download>
	                                            Get Template
	                                        </a>
	                                    </li>
	                                </ul>
	                            </div>
	                        </div>
	                    </div>
	                </div>
                <form class="box-body table-responsive" id="manage-form" method="POST">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>
                                    <input type="checkbox" class="form-check-input" id="checkAll">
                                </th>
                                <?php
                                foreach ($tableContent as $columnName => $columnValue) {
                                    echo "<th>$columnName</th>";
                                }
                                ?>
                                <?php if (isset($actions)): ?>
                                    <th>Actions</th>
                                <?php endif ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sel_sql = "SELECT * FROM $table ";
                            if (isset($fetchWhere) && trim($fetchWhere) !== '') {
                                $sel_sql .= trim($fetchWhere) . ' ';
                            }
                            if ($hasTitleColumn && $search_title !== '') {
                                $sel_sql .= (isset($fetchWhere) && trim($fetchWhere) !== '') ? "AND title LIKE ? " : "WHERE title LIKE ? ";
                            }
                            $sel_sql .= "ORDER BY created_at DESC LIMIT $items_per_page OFFSET $offset";

                            $selsql = $getdata->FetchQuery($sel_sql, $search_params);

                            // Use a while loop to iterate through each row in the result set
                            while ($data = mysqli_fetch_array($selsql)):
                            ?>
                                <tr id="<?= h($data[$id_col]) ?>">
                                    <td>
                                        <input type="checkbox" class="form-check-input" name="bulkSelect[]"
                                            value="<?= h($data[$id_col]) ?>">
                                    </td>
                                    <?php
                                    // Loop through each key-value pair in $tableContent
                                    foreach ($tableContent as $columnName => $columnValue) {
                                        $isHtml = false;
                                        //username
                                        if ($columnValue === "username") {
                                            $value = $getdata->FetchUsername($data['user_id']);
                                        }
                                        //coupon_value
                                        elseif ($columnValue === "coupon_value") {
                                            $value = $data['value'] . (($data['type'] === 'percentage') ? '%' : '');
                                        } elseif ($columnValue == "order_item") {
                                            $item_type = $data['chapter_id'] ? "chapter" : "subject";
                                            $value = $data['chapter_id'] ? $getdata->FetchProductName($data['chapter_id']) : $getdata->FetchProductCatName($data['subject_id']);
                                        }
                                        //product_name
                                        elseif ($columnValue === "product_name") {
                                            $value = $getdata->FetchProductName($data['product_id']);
                                        }
                                        //boolean
                                        else if (strpos($columnValue, "boolean") !== false) {
                                            $columnValue = explode(" ", $columnValue)[1];
                                            if ($data[$columnValue] === 0) {
                                                $value = "<i class='fa fa-times-circle text-danger' aria-hidden='true'></i>";
                                            } else {
                                                $value = "<i class='fa fa-check-circle text-success' aria-hidden='true'></i>";
                                            }
                                            $isHtml = true;
                                        }
                                        //category_title
                                        elseif ($columnValue === "category_title") {
                                            $href = '?id=' . urlencode((string) $data['id']);
                                            $label = h($getdata->decodeStoredText($data['title']));
                                            $value = "<a href='" . h($href) . "'>" . $label . "</a>";
                                            $isHtml = true;
                                        }
                                        //product_catname
                                        else if (strpos($columnValue, "product_catname") !== false) {
                                            $columnValue = $data[explode(" ", $columnValue)[1]];
                                            $columnValues = explode(",", $columnValue);

                                            $resultArray = [];
                                            foreach ($columnValues as $element) {
                                                $resultArray[] = $getdata->FetchProductCatName($element);
                                            }
                                            $value = implode(", ", $resultArray);
                                        }
                                        //image
                                        elseif ($columnValue === "image") {
                                            if (empty($data[$columnValue])) {
                                                $image = "assets/img/dummy.jpg";
                                            } else {
                                                $image = media_folder . $data[$columnValue];
                                            }
                                            $value = "<center><img src='" . h($image) . "' height='100' loading='lazy' onerror=\"this.onerror=null;this.src='assets/img/dummy.jpg';\"></center>";
                                            $isHtml = true;
                                        } else {
                                            // Split the column value by space to get individual field names
                                            $fieldNames = explode(" ", $columnValue);
                                            $value = '';
                                            // Concatenate values of individual fields
                                            foreach ($fieldNames as $fieldName) {
                                                $value .= $data[$fieldName] . ' ';
                                            }
                                        }

                                        if (!$isHtml) {
                                            $value = h($getdata->decodeStoredText($value));
                                        }
                                        //add edit link to first column
                                        if ($columnName === array_key_first($tableContent) && $columnName !== "category_title" && $actions['edit']) {
                                            $href = $editor_url . "?action=edit&id=" . urlencode((string) $data[$id_col]);
                                            $value = "<a href='" . h($href) . "'>" . $value . "</a>";
                                            $isHtml = true;
                                        }
                                        echo "<td>" . trim($value) . "</td>";
                                    }
                                    ?>
                                    <?php if (isset($actions)): ?>
                                        <td>
                                            <?php if ($actions['approve'] && $data["status"] == 0) { ?>
                                                <button class="btn btn-outline-success" title="Approve">
                                                    <i class="fa fa-check" aria-hidden="true"></i>
                                                </button>
                                            <?php } ?>

                                            <?php if ($actions['edit']) { ?>
                                                <a href="<?php echo $editor_url ?>?action=edit&id=<?= h($data[$id_col]) ?>" type="button"
                                                    class="btn btn-outline-primary" title="Edit">
                                                    <i class="fa fa-pencil" aria-hidden="true"></i>
                                                </a>
                                            <?php } ?>

                                            <?php if ($actions['delete']) { ?>
                                                <button type="submit" class="btn btn-outline-danger" title="Delete">
                                                    <i class="fa fa-trash" aria-hidden="true"></i>
                                                </button>
                                            <?php } ?>
                                        </td>
                                    <?php endif ?>
                                </tr>
                            <?php endwhile; ?>

                        </tbody>
                    </table>
                </form>

                <!-- Pagination -->
                <div class="<?php if (!empty($pagination_html) ? "mt-4 pb-5" : "") ?>">
                    <?= $pagination_html; ?>
                </div>

            </div>
        </section>

        <script>
            $(document).ready(function() {
                // Functions to handle actions
                <?php if ($actions['approve']) { ?>
                    $('.btn-outline-success').on('click', function() {
                        var $btn = $(this);
                        var trId = $btn.closest('tr').attr('id');
                        var $row = $('#' + trId);
                        var $icon = $row.find('.fa-times-circle, .fa-check-circle'); // Find either icon

                        $.ajax({
                            type: 'POST',
                            url: "<?php echo $currentUrl; ?>?action=approve&id=" + trId,
                            success: function(response) {
                                if (response === 'success') {
                                    // Animate button removal
                                    $btn.fadeOut(300, function() {
                                        $(this).remove();
                                    });

                                    // Change icon from times to check-circle
                                    $icon.removeClass('fa-times-circle text-danger')
                                        .addClass('fa-check-circle text-success');

                                    // Flash green background for success
                                    $row.css('background-color', '#d4edda')
                                        .animate({
                                            backgroundColor: '#ffffff'
                                        }, 1000);
                                } else {
                                    alert('Failed to approve the review.');
                                    console.log(response);
                                }
                            },
                            error: function() {
                                alert('Error in AJAX request.');
                            }
                        });
                    });
                <?php } ?>
                <?php if ($actions['delete']) { ?>
                    $('.btn-outline-danger').on('click', function() {
                        var trId = $(this).closest('tr').attr('id');

                        // Display a confirmation dialog
                        if (confirm('Are you sure you want to delete this item?')) {
                            // Send AJAX request
                            $.ajax({
                                type: 'POST',
                                url: "<?php echo $currentUrl; ?>?action=delete&id=" + trId,
                                // data: { id: trId },
                                success: function(response) {
                                    response = response.trim();
                                    if (response == 'success') {
                                        // Remove the row with a smooth animation
                                        $('#' + trId).fadeOut('slow', function() {
                                            $(this).remove();
                                        });
                                    } else {
                                        alert('Failed: ' + response);
                                        console.log(response);
                                    }
                                },
                                error: function(xhr, status, error) {
                                    console.error('AJAX Error:', xhr);
                                    alert('Error in AJAX request.');
                                }
                            });
                        }
                    });
                <?php } ?>
                // Select All functionality
                document.getElementById('checkAll').addEventListener('change', function() {
                    const checkboxes = document.querySelectorAll('input[name="bulkSelect[]"]');
                    checkboxes.forEach(checkbox => {
                        checkbox.checked = this.checked;
                    });
                });

                document.getElementById('applyButton').addEventListener('click', function() {
                    const selectedValue = document.getElementById('actionSelect').value;
                    const checkboxes = document.querySelectorAll('input[name="bulkSelect[]"]:checked');

                    if (selectedValue === "select option") {
                        alert("Please select action.");
                        return;
                    }

                    if (checkboxes.length === 0) {
                        alert("Please select items to perform the action.");
                        return;
                    }

                    const form = document.getElementById('manage-form');
                    const currentUrl = new URL(window.location.href);
                    currentUrl.search = '';
                    currentUrl.searchParams.set('action', selectedValue);
                    form.action = currentUrl.toString();
                    form.submit();
                });
            });
        </script>

    <?php endif ?>

</div>

<?php include("components/footer.php"); ?>
