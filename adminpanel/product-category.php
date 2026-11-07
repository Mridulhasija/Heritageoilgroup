<?php
require_once __DIR__ . '/components/config-header.php';

$conn = db();

$page_title = "Product Category";
$table = "tbl_product_category";

$id_col = 'id';
$id_type = "s";

$tableContent = [
    "Title" => "category_title",
    "<center><i class='fa fa-picture-o' aria-hidden='true'></i></center>" => "image",
    "Url" => "url",
    "Status" => "boolean status",
];

// Define the actions and their enable/disable status
$actions = [
    'approve' => false,
    'add' => true,
    'edit' => true,
    'delete' => true
];

$editor_url = "product-category-editor.php";
$currentUrl = $_SERVER['PHP_SELF'];

if (isset($_GET['id']) && ($_GET['id'] != "0")) {
    $id = $_GET['id'];
    $fetchWhere = "where parent_id='$id'";

    $moreAddParms = "&parent_id=$id";

    $parentCatRes = $getdata->FetchQuery("SELECT `title` FROM $table WHERE `id` = ?", [$id]);
    $parentCat = $parentCatRes->fetch_assoc();
    $breadcrumb = [$parentCat['title'] => "?id=" . $id];
} else {
    $page_subtitle = "Management";
    $fetchWhere = "where parent_id='0'";
}


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_GET['action'])) {
    try {
        $action = $_GET['action'];
        $id = isset($_GET['id']) ? $_GET['id'] : $getdata->uuidv4();

        include('components/quick-process.php');

        if ($action === "add" || $action === "edit") {
            $title = isset($_POST['title']) ? trim($_POST['title']) : null;
            $url = isset($_POST['url']) ? trim($_POST['url']) : null;
            $url = empty($url) ? $title : $url; // if empty then use title as url
            $url = $utils->stringToUrl($url);
            $meta_title = isset($_POST['meta_title']) ? trim($_POST['meta_title']) : $title;
            $meta_title = empty($meta_title) ? $title : $meta_title;
            $meta_keyword = isset($_POST['meta_keyword']) ? trim($_POST['meta_keyword']) : null;
            $meta_desc = isset($_POST['meta_desc']) ? trim($_POST['meta_desc']) : null;
            $short_description = isset($_POST['short_description']) ? trim($_POST['short_description']) : null;
            $description = isset($_POST['description']) ? trim($_POST['description']) : null;
            $icon = isset($_POST['icon']) ? trim($_POST['icon']) : null;
            $image = isset($_POST['image']) ? trim($_POST['image']) : null;
            $banner = isset($_POST['banner']) ? trim($_POST['banner']) : null;
            $price = isset($_POST['price']) ? trim($_POST['price']) : null;
            $discounted_price = isset($_POST['discounted_price']) ? trim($_POST['discounted_price']) : null;
            // mysqli's `d` bind type coerces NULL to 0. Keep the optional
            // discount as a string so NULL reaches the nullable DB column.
            if ($discounted_price === '') {
                $discounted_price = null;
            } elseif (is_numeric($discounted_price) && (float) $discounted_price <= 0) {
                $discounted_price = null;
            }
            $allowedUnits = ['KGM', 'GRM', 'TNE', 'LBR', 'LTR', 'MLT', 'GLL', 'C62', 'BG', 'BO', 'CT'];
            $unit = isset($_POST['unit']) && in_array($_POST['unit'], $allowedUnits, true) ? $_POST['unit'] : 'C62';
            $status = isset($_POST['status']) ? trim($_POST['status']) : null;
            $parent_id = isset($_POST['parent_id']) ? trim($_POST['parent_id']) : null;
            $heading = trim($_POST['heading'] ?? '');
            $service_heading = trim($_POST['service_heading'] ?? '');
            $service_meta_title = trim($_POST['service_meta_title'] ?? '');
            $service_meta_keyword = trim($_POST['service_meta_keyword'] ?? '');
            $service_meta_desc = trim($_POST['service_meta_desc'] ?? '');
            $service_description = trim($_POST['service_description'] ?? '');
            $encodeFaqs = static function ($questions, $answers) {
                if (!is_array($questions) || !is_array($answers) || count($questions) !== count($answers)) {
                    throw new InvalidArgumentException('Invalid FAQ submission');
                }
                $items = [];
                foreach ($questions as $key => $question) {
                    if (!array_key_exists($key, $answers) || !is_string($question) || !is_string($answers[$key])) {
                        throw new InvalidArgumentException('Invalid FAQ pair');
                    }
                    $question = trim($question);
                    $answer = trim($answers[$key]);
                    if ($question === '' && $answer === '') continue;
                    if ($question === '' || $answer === '') throw new InvalidArgumentException('Complete both FAQ fields');
                    $items[] = ['question' => $question, 'answer' => $answer];
                }
                return json_encode($items, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
            };
            $faqs = $encodeFaqs($_POST['faq_question'] ?? [], $_POST['faq_answer'] ?? []);
            $service_faqs = $encodeFaqs($_POST['service_faq_question'] ?? [], $_POST['service_faq_answer'] ?? []);
            $stmt = null;

            if ($action == 'add') {
                // Generate unique URL
                $url = $utils->generateUniqueURL($conn, $table, $url);
                $stmt = $conn->prepare(
                    "INSERT INTO $table (`id`,title, url, meta_title, meta_keyword, meta_desc, short_description, description, icon, image, banner, price, discounted_price, unit, status, parent_id, heading, service_heading, service_meta_title, service_meta_keyword, service_meta_desc, service_description, faqs, service_faqs) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
                );
            } elseif ($action == 'edit' && isset($id)) {
                $existing_url = $utils->getURLById($conn, $table, $id, $id_type);
                if ($url != $existing_url) {
                    // Generate a unique URL if the provided URL is different
                    $url = $utils->generateUniqueURL($conn, $table, $url, $id);
                }
                $stmt = $conn->prepare(
                    "UPDATE $table SET title = ?, url = ?, meta_title = ?, meta_keyword = ?, meta_desc = ?, short_description = ?, description = ?, icon = ?, image = ?, banner = ?, price = ?, discounted_price = ?, unit = ?, status = ?, heading = ?, service_heading = ?, service_meta_title = ?, service_meta_keyword = ?, service_meta_desc = ?, service_description = ?, faqs = ?, service_faqs = ? WHERE id = ?"
                );
            }

            if (!$stmt) {
                throw new Exception("Error preparing statement: " . $conn->error);
            }

            if ($action == 'add') {
                $stmt->bind_param("sssssssssssdssisssssssss", $id, $title, $url, $meta_title, $meta_keyword, $meta_desc, $short_description, $description, $icon, $image, $banner, $price, $discounted_price, $unit, $status, $parent_id, $heading, $service_heading, $service_meta_title, $service_meta_keyword, $service_meta_desc, $service_description, $faqs, $service_faqs);
            } elseif ($action == 'edit' && isset($id)) {
                $stmt->bind_param("ssssssssssdssisssssssss", $title, $url, $meta_title, $meta_keyword, $meta_desc, $short_description, $description, $icon, $image, $banner, $price, $discounted_price, $unit, $status, $heading, $service_heading, $service_meta_title, $service_meta_keyword, $service_meta_desc, $service_description, $faqs, $service_faqs, $id);
            }

            if (!$stmt->execute()) {
                throw new Exception("Error executing statement: " . $stmt->error);
            }

            $stmt->close();
            $_SESSION["success-msg"] = "Category successfully " . ($action == 'add' ? 'added' : 'edited');
        }
    } catch (Exception $e) {
        $_SESSION["error-msg"] = "Error: " . $e->getMessage();
    }
    header('Location: ' . $editor_url . "?action=edit&id=$id");
    exit();
} else {
    //show page
    include("components/header.php");
    include("components/manage.php");
}
