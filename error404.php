<?php
error_reporting(0);

session_set_cookie_params([
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Strict',
]);

session_start();
require_once __DIR__ . '/include/connection.php';
?>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Heritage Drop Oil | Page Not Found</title>
    <meta name="robots" content="noindex, nofollow">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/svg+xml" href="<?= SITE_URL ?>/assets/brand/heritage-drop-mark.svg?v=1">
    <script src="<?= SITE_URL ?>/assets/js/tailwind.js"></script>
    <?php include __DIR__ . '/components/tailwind-config.php'; ?>
</head>
<body class="m-0 bg-white font-sans">

<section>
    <div class="mx-auto max-w-screen-xl px-4 py-8">
        <div class="mx-auto max-w-2xl text-center">
            <h1 class="mb-4 text-8xl font-extrabold text-primary-600">404</h1>
            <p class="mb-4 text-4xl font-bold text-secondary-800">Something's missing.</p>
            <p class="mb-4 text-lg font-light text-secondary-500">
                Sorry, we can't find that page. You'll find lots to explore on the home page.
            </p>
            <a href="<?= SITE_URL ?>" class="my-4 inline-flex rounded-md bg-primary-600 px-5 py-2.5 text-center text-sm font-medium text-white transition-colors hover:bg-primary-700 focus:outline-none focus:ring-4 focus:ring-primary-300">Back to Homepage</a>
        </div>   
    </div>
</section>
  
</body>
</html>
