<?php
// error_reporting(0);
error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', 'error_log.txt');

// Display errors on the page
// ini_set('display_errors', 1);

session_set_cookie_params([
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Strict',
]);
session_start();
require_once __DIR__ . '/connection.php';
$conn = db();
$config_result = mysqli_query($conn, "SELECT * FROM tbl_config LIMIT 1");
if ($config_result && mysqli_num_rows($config_result) > 0) {
    $configData = mysqli_fetch_assoc($config_result);
    $logo = !empty($configData['logo']) ? media_folder . $configData['logo'] : null;
    // Public-facing brand identity is maintained in the site templates.
    $site_name = 'Heritage Drop Oil';
    define("CURRENCY", $configData['currency']);
    define("THOUSAND_SEPERATOR", $configData['thousand_separator']);
    define("DECIMAL_SEPERATOR", $configData['decimal_separator']);
}

require_once __DIR__ . '/getdata.php';
$getdata = new Getdata();

$meta_title = $site_name ?? '';
$meta_keyword = '';
$meta_desc = '';

if (isset($router)) {
    // Check if $location is set and $slug is not set
    if (isset($location) && !isset($slug)) {
        $router = 'home-service';
    } else {
        $router = ($router == '') ? 'home' : $router;
    }

    // Fetch SEO metadata based on the router
    $selsql = $getdata->FetchSeoMeta($router);
    $respage = mysqli_fetch_object($selsql);

    // Set meta details
    $meta_title = $respage->meta_title ?? ($respage->title ?? $meta_title);
    $meta_keyword = $respage->meta_keyword ?? $meta_keyword;
    $meta_desc = $respage->meta_desc ?? $meta_desc;
}

// Replace the previous company name in existing SEO and page copy as it is rendered.
foreach (['meta_title', 'meta_keyword', 'meta_desc'] as $brandField) {
    if (isset($$brandField) && is_string($$brandField)) {
        $$brandField = str_ireplace('Grade Oil Group', 'Heritage Drop Oil', $$brandField);
    }
}
if (isset($respage) && is_object($respage)) {
    foreach (['title', 'description', 'meta_title', 'meta_keyword', 'meta_desc'] as $brandField) {
        if (isset($respage->$brandField) && is_string($respage->$brandField)) {
            $respage->$brandField = str_ireplace('Grade Oil Group', 'Heritage Drop Oil', $respage->$brandField);
        }
    }
}
