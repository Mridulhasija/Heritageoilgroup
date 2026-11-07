<?php
error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', 'error_log.txt');

header("Content-Type: application/xml; charset=UTF-8");
echo '<?xml version="1.0" encoding="UTF-8"?>';

require_once __DIR__ . '/include/connection.php';

function formatLastMod($datetime)
{
    return date('Y-m-d\TH:i:sP', strtotime($datetime));
}

$conn = db();
$base = rtrim(SITE_URL, '/');

// Slug passed by index.php (null = sitemap index). Supported slugs defined and verified here.
$sitemapSlug = $sitemapSlug ?? null;
$sitemapSlugs = ['pages', 'categories', 'products', 'locations', 'location-categories'];

if ($sitemapSlug === null) {
    // ——— Sitemap index (submit /sitemap to Google) ———
    $now = date('Y-m-d\TH:i:sP');
    echo "\n<sitemapindex xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";

    foreach ($sitemapSlugs as $slug) {
        if ($slug === 'products') {
            $productCount = 0;
            $stmt = $conn->prepare("SELECT COUNT(*) FROM tbl_product WHERE status = 1");
            $stmt->execute();
            $stmt->bind_result($productCount);
            $stmt->fetch();
            $stmt->close();
            $productPages = $productCount > 0 ? (int) ceil($productCount / 500) : 0;
            for ($p = 1; $p <= $productPages; $p++) {
                $loc = $base . '/sitemap/products?p=' . $p;
                echo "    <sitemap>\n        <loc>" . htmlspecialchars($loc, ENT_XML1, 'UTF-8') . "</loc>\n        <lastmod>{$now}</lastmod>\n    </sitemap>\n";
            }
        } elseif ($slug === 'locations') {
            $stmt = $conn->prepare("SELECT COUNT(*) FROM tbl_page WHERE url = 'home-service'");
            $stmt->execute();
            $stmt->bind_result($homeServiceCount);
            $stmt->fetch();
            $stmt->close();
            if ($homeServiceCount > 0) {
                $loc = $base . '/sitemap/locations';
                echo "    <sitemap>\n        <loc>" . htmlspecialchars($loc, ENT_XML1, 'UTF-8') . "</loc>\n        <lastmod>{$now}</lastmod>\n    </sitemap>\n";
            }
        } elseif ($slug === 'location-categories') {
            $stmt = $conn->prepare("SELECT COUNT(*) FROM tbl_page WHERE url = 'home-service'");
            $stmt->execute();
            $stmt->bind_result($homeServiceCount);
            $stmt->fetch();
            $hasHomeService = ($homeServiceCount > 0);
            $stmt->close();
            $stmt = $conn->prepare("SELECT COUNT(*) FROM tbl_product_category WHERE status = 1 AND service_description IS NOT NULL AND TRIM(service_description) <> ''");
            $stmt->execute();
            $stmt->bind_result($serviceAreaCount);
            $stmt->fetch();
            $hasServiceAreaContent = ($serviceAreaCount > 0);
            $stmt->close();
            if ($hasHomeService && $hasServiceAreaContent) {
                $loc = $base . '/sitemap/location-categories';
                echo "    <sitemap>\n        <loc>" . htmlspecialchars($loc, ENT_XML1, 'UTF-8') . "</loc>\n        <lastmod>{$now}</lastmod>\n    </sitemap>\n";
            }
        } else {
            $loc = $base . '/sitemap/' . $slug;
            echo "    <sitemap>\n        <loc>" . htmlspecialchars($loc, ENT_XML1, 'UTF-8') . "</loc>\n        <lastmod>{$now}</lastmod>\n    </sitemap>\n";
        }
    }
    echo "</sitemapindex>";
    exit;
}

// Validate slug is supported
if (!in_array($sitemapSlug, $sitemapSlugs)) {
    http_response_code(404);
    include('error404.php');
    exit;
}

// ——— URLset sitemaps (react on $sitemapSlug) ———
echo "\n<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\" xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\"
    xsi:schemaLocation=\"http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd\">\n";

if ($sitemapSlug === 'pages') {
    echo "<url>
    <loc>" . SITE_URL . "/</loc>
    <priority>1.0</priority>
    <changefreq>monthly</changefreq>
    <lastmod>" . date('Y-m-d\TH:i:sP') . "</lastmod>
</url>\n";
    $stmt = $conn->prepare("SELECT url, updated_at FROM tbl_page WHERE url NOT IN ('home-service', 'home')");
    $stmt->execute();
    $stmt->bind_result($pageUrl, $lastmod);
    while ($stmt->fetch()) {
        echo "<url>
        <loc>" . SITE_URL . '/' . htmlspecialchars($pageUrl, ENT_XML1, 'UTF-8') . "</loc>
        <priority>0.8</priority>
        <changefreq>monthly</changefreq>
        <lastmod>" . formatLastMod($lastmod) . "</lastmod>
    </url>\n";
    }
    $stmt->close();
} elseif ($sitemapSlug === 'categories') {
    $stmt = $conn->prepare("SELECT url, updated_at FROM tbl_product_category WHERE status = 1");
    $stmt->execute();
    $stmt->bind_result($categoryUrl, $lastmod);
    while ($stmt->fetch()) {
        echo "<url>
        <loc>" . SITE_URL . '/' . htmlspecialchars($categoryUrl, ENT_XML1, 'UTF-8') . "</loc>
        <priority>0.7</priority>
        <changefreq>monthly</changefreq>
        <lastmod>" . formatLastMod($lastmod) . "</lastmod>
    </url>\n";
    }
    $stmt->close();
} elseif ($sitemapSlug === 'products') {
    $perPage = 500;
    $page = max(1, (int) ($_GET['p'] ?? 1));
    $offset = ($page - 1) * $perPage;
    $stmt = $conn->prepare("SELECT url, updated_at FROM tbl_product WHERE status = 1 ORDER BY updated_at DESC LIMIT ? OFFSET ?");
    $stmt->bind_param("ii", $perPage, $offset);
    $stmt->execute();
    $stmt->bind_result($productUrl, $lastmod);
    while ($stmt->fetch()) {
        echo "<url>
        <loc>" . SITE_URL . "/product/" . htmlspecialchars($productUrl, ENT_XML1, 'UTF-8') . "</loc>
        <priority>0.7</priority>
        <changefreq>monthly</changefreq>
        <lastmod>" . formatLastMod($lastmod) . "</lastmod>
    </url>\n";
    }
    $stmt->close();
} elseif ($sitemapSlug === 'locations') {
    $stmt = $conn->prepare("SELECT COUNT(*) FROM tbl_page WHERE url = 'home-service'");
    $stmt->execute();
    $stmt->bind_result($homeServiceCount);
    $stmt->fetch();
    $stmt->close();
    if ($homeServiceCount > 0) {
        $stmt = $conn->prepare("SELECT url, created_at FROM tbl_location WHERE status = 1");
        $stmt->execute();
        $stmt->bind_result($locationUrl, $lastmod);
        while ($stmt->fetch()) {
            echo "<url>
            <loc>" . SITE_URL . '/' . htmlspecialchars($locationUrl, ENT_XML1, 'UTF-8') . "/</loc>
            <priority>0.7</priority>
            <changefreq>monthly</changefreq>
            <lastmod>" . formatLastMod($lastmod) . "</lastmod>
        </url>\n";
        }
        $stmt->close();
    }
} elseif ($sitemapSlug === 'location-categories') {
    $hasHomeService = false;
    $stmt = $conn->prepare("SELECT COUNT(*) FROM tbl_page WHERE url = 'home-service'");
    $stmt->execute();
    $stmt->bind_result($homeServiceCount);
    $stmt->fetch();
    $hasHomeService = ($homeServiceCount > 0);
    $stmt->close();
    $hasServiceAreaContent = false;
    $stmt = $conn->prepare("SELECT COUNT(*) FROM tbl_product_category WHERE status = 1 AND service_description IS NOT NULL AND TRIM(service_description) <> ''");
    $stmt->execute();
    $stmt->bind_result($serviceAreaCount);
    $stmt->fetch();
    $hasServiceAreaContent = ($serviceAreaCount > 0);
    $stmt->close();
    if ($hasHomeService && $hasServiceAreaContent) {
        $locations = [];
        $stmt = $conn->prepare("SELECT url, created_at FROM tbl_location WHERE status = 1");
        $stmt->execute();
        $stmt->bind_result($locationUrl, $lastmod);
        while ($stmt->fetch()) {
            $locations[$locationUrl] = formatLastMod($lastmod);
        }
        $stmt->close();
        $categories = [];
        $stmt = $conn->prepare("SELECT url, updated_at FROM tbl_product_category WHERE status = 1 AND service_description IS NOT NULL AND TRIM(service_description) <> ''");
        $stmt->execute();
        $stmt->bind_result($categoryUrl, $lastmod);
        while ($stmt->fetch()) {
            $categories[$categoryUrl] = formatLastMod($lastmod);
        }
        $stmt->close();
        foreach ($locations as $locationUrl => $locationLastmod) {
            foreach ($categories as $categoryUrl => $categoryLastmod) {
                echo "<url>
                <loc>" . SITE_URL . '/' . htmlspecialchars($locationUrl, ENT_XML1, 'UTF-8') . "/" . htmlspecialchars($categoryUrl, ENT_XML1, 'UTF-8') . "/</loc>
                <priority>0.6</priority>
                <changefreq>monthly</changefreq>
                <lastmod>" . max($locationLastmod, $categoryLastmod) . "</lastmod>
            </url>\n";
            }
        }
    }
} else {
    http_response_code(404);
    include('error404.php');
    exit;
}

echo "</urlset>";
