<?php
// Get the requested URI
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Get the base directory dynamically
$baseDirectory = dirname($_SERVER['SCRIPT_NAME']);

// Remove base directory from the requested URI
if ($baseDirectory != DIRECTORY_SEPARATOR) {
    $baseDirectory .= '/';
    $router = str_replace($baseDirectory, '', $requestUri);
} else {
    $router = ltrim($requestUri, '/');
}


// Define routes and their corresponding file paths
$routes = [
    ''                 => 'home.php',
    'about'            => 'about.php',
    'contact'          => 'contact.php',
    'service-areas'    => 'service-areas.php',
    'shipping-policy'  => 'page.php',
    'return-policy'    => 'page.php',
];

// Parse the URL to get the query string
$queryString = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY) ?? '';
// Parse the query string into an associative array
parse_str($queryString, $queryParams);

// Sitemap: pass slug from URL; verification done in sitemap.php
if ($router === 'sitemap') {
    $sitemapSlug = null;
    include('sitemap.php');
} elseif (preg_match('/^sitemap\/([a-z0-9_-]+)$/', $router, $sitemapMatch)) {
    $sitemapSlug = $sitemapMatch[1];
    include('sitemap.php');
}
// Check routes dictionary for static pages
elseif (isset($routes[$router])) {
    include($routes[$router]);
}
// Check for trailing slash (e.g. /location/ or /location/category/ )
elseif (substr($requestUri, -1) === '/') {
    $trimmedRouter = trim($router, '/'); // Remove the trailing slash

    if (preg_match('/^([0-9a-zA-Z_-]+)\/([0-9a-zA-Z_-]+)$/', $trimmedRouter, $matches)) {
        $location = $matches[1];
        $slug = $matches[2]; // category
        include('category.php');
    }
    // If no category, handle home service
    elseif (preg_match('/^([0-9a-zA-Z_-]+)$/', $trimmedRouter, $matches)) {
        $location = $matches[1]; // Only location (e.g., /location/)
        include('home.php');
    } else {
        http_response_code(404);
        include('error404.php'); // Return 404 if no valid route
    }
}
// Check for /category (without trailing slash) 
elseif (preg_match('/^([0-9a-zA-Z_-]+)$/', $router, $matches)) {
    $slug = $matches[1]; // Category slug
    include('category.php');
}
// Handle 404 error for unrecognized routes
else {
    http_response_code(404);
    include('error404.php');
}
