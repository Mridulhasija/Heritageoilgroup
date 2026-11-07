<?php
require_once __DIR__ . '/components/config-header.php';

$conn = db();

$page_title = "Products";
$page_subtitle = "Management";
$table = "tbl_product";

$id_col = 'id';
$id_type = "s";

$tableContent = [
    "Title" => "title",
    "<center><i class='fa fa-picture-o' aria-hidden='true'></i></center>" => "image",
];

// Define the actions and their enable/disable status
$actions = [
    'approve' => false,
    'add' => true,
    'edit' => true,
    'delete' => true
];

$editor_url = "product-editor.php";
$currentUrl = $_SERVER['PHP_SELF'];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_GET['action'])) {
    $action = $_GET['action'] ?? null;
    $id = $_GET['id'] ?? null;
    try {
        include('components/quick-process.php');

        if ($action === "add" || $action === "edit") {
            $title = isset($_POST['title']) ? trim($_POST['title']) : null;
            if (!$title) {
                throw new Exception("Title is required.");
            }

            $heading = isset($_POST['heading']) ? trim($_POST['heading']) : '';

            $url = isset($_POST['url']) ? trim($_POST['url']) : null;
            $url = empty($url) ? $title : $url;
            $url = $utils->stringToUrl($url);

            $meta_title = isset($_POST['meta_title']) ? trim($_POST['meta_title']) : null;
            $meta_title = empty($meta_title) ? $title : $meta_title;
            $meta_keyword = isset($_POST['meta_keyword']) ? trim($_POST['meta_keyword']) : null;
            $meta_desc = isset($_POST['meta_desc']) ? trim($_POST['meta_desc']) : null;
            $short_description = isset($_POST['short_description']) ? trim($_POST['short_description']) : null;
            $description = isset($_POST['description']) ? trim($_POST['description']) : null;
            $faqQuestions = $_POST['faq_question'] ?? [];
            $faqAnswers = $_POST['faq_answer'] ?? [];
            if (!is_array($faqQuestions) || !is_array($faqAnswers) || count($faqQuestions) !== count($faqAnswers)) {
                throw new InvalidArgumentException('Invalid FAQ submission');
            }
            $faqItems = [];
            foreach ($faqQuestions as $key => $question) {
                if (!array_key_exists($key, $faqAnswers) || !is_string($question) || !is_string($faqAnswers[$key])) {
                    throw new InvalidArgumentException('Invalid FAQ pair');
                }
                $question = trim($question);
                $answer = trim($faqAnswers[$key]);
                if ($question === '' && $answer === '') continue;
                if ($question === '' || $answer === '') throw new InvalidArgumentException('Complete both FAQ fields');
                $faqItems[] = ['question' => $question, 'answer' => $answer];
            }
            $faqs = json_encode($faqItems, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
            $image = isset($_POST['image']) ? trim($_POST['image']) : null;
            $feature = isset($_POST['feature']) ? 1 : 0;
            $status = isset($_POST['status']) ? 1 : 0;
            $category = isset($_POST['category']) ? implode(",", $_POST['category']) : null;

            $price_raw = isset($_POST['price']) ? trim($_POST['price']) : null;
            if ($price_raw === null || $price_raw === '' || !is_numeric($price_raw) || floatval($price_raw) <= 0) {
                throw new Exception("Price must be greater than 0.");
            }
            $price = $price_raw;

            $discounted_price_raw = isset($_POST['discounted_price']) ? trim($_POST['discounted_price']) : null;
            $discounted_price = null;
            if ($discounted_price_raw !== null && $discounted_price_raw !== '') {
                if (!is_numeric($discounted_price_raw) || floatval($discounted_price_raw) <= 0) {
                    throw new Exception("Discounted price must be greater than 0.");
                }
                if (floatval($discounted_price_raw) >= floatval($price_raw)) {
                    throw new Exception("Discounted price must be less than the regular price.");
                }
                $discounted_price = $discounted_price_raw;
            }

            $allowedUnits = ['KGM', 'GRM', 'TNE', 'LBR', 'LTR', 'MLT', 'GLL', 'C62', 'BG', 'BO', 'CT'];
            $unit = isset($_POST['unit']) && in_array($_POST['unit'], $allowedUnits, true) ? $_POST['unit'] : 'C62';

            $stmt = null;

            if ($action === 'add') {
                $id = $getdata->uuidv4();
                $url = $utils->generateUniqueURL($conn, $table, $url);

                $stmt = $conn->prepare(
                    "INSERT INTO $table (id, title, heading, meta_title, meta_keyword, meta_desc, category, url, short_description, description, image, feature, status, price, discounted_price, unit, faqs) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)"
                );
                if (!$stmt) {
                    throw new Exception("Error preparing statement: " . $conn->error);
                }
                $stmt->bind_param(
                    "sssssssssssiissss",
                    $id,
                    $title,
                    $heading,
                    $meta_title,
                    $meta_keyword,
                    $meta_desc,
                    $category,
                    $url,
                    $short_description,
                    $description,
                    $image,
                    $feature,
                    $status,
                    $price,
                    $discounted_price,
                    $unit,
                    $faqs
                );
            } elseif ($action === 'edit') {
                if (!$id) {
                    throw new Exception("Missing product id for edit.");
                }

                $existing_url = $utils->getURLById($conn, $table, $id, $id_type);
                if ($url != $existing_url) {
                    $url = $utils->generateUniqueURL($conn, $table, $url);
                }

                $stmt = $conn->prepare(
                    "UPDATE $table SET title = ?, heading = ?, meta_title = ?, meta_keyword = ?, meta_desc = ?, category = ?, url = ?, short_description = ?, description = ?, image = ?, feature = ?, status = ?, price = ?, discounted_price = ?, unit = ?, faqs = ? WHERE id = ?"
                );
                if (!$stmt) {
                    throw new Exception("Error preparing statement: " . $conn->error);
                }
                $stmt->bind_param(
                    "ssssssssssiisssss",
                    $title,
                    $heading,
                    $meta_title,
                    $meta_keyword,
                    $meta_desc,
                    $category,
                    $url,
                    $short_description,
                    $description,
                    $image,
                    $feature,
                    $status,
                    $price,
                    $discounted_price,
                    $unit,
                    $faqs,
                    $id
                );
            }

            if (!$stmt->execute()) {
                throw new Exception("Error executing statement: " . $stmt->error);
            }

            $stmt->close();
            $_SESSION["success-msg"] = $page_title . " successfully " . ($action === 'add' ? 'added' : 'edited');

            header('Location: ' . $editor_url . "?action=edit&id=$id");
            exit();
        }
    } catch (Exception $e) {
        $_SESSION["error-msg"] = "Error: " . $e->getMessage();
        if ($action === 'edit' && $id) {
            header('Location: ' . $editor_url . "?action=edit&id=$id");
        } else {
            header('Location: ' . $editor_url . "?action=add");
        }
    }
    exit();
} else {
    //show page
    include("components/header.php");
    include("components/manage.php");
}
