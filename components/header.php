<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <title><?= htmlspecialchars((string) ($meta_title ?? $site_name ?? ''), ENT_QUOTES, 'UTF-8') ?></title>
    <meta name="keywords" content="<?= htmlspecialchars((string) ($meta_keyword ?? ''), ENT_QUOTES, 'UTF-8') ?>">
    <meta name="description" content="<?= htmlspecialchars((string) ($meta_desc ?? ''), ENT_QUOTES, 'UTF-8') ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" type="image/svg+xml" href="<?= SITE_URL ?>/assets/brand/heritage-drop-mark.svg?v=1">

    <?php if (!empty($pageCanonical)): ?>
        <link rel="canonical" href="<?= htmlspecialchars($pageCanonical, ENT_QUOTES, 'UTF-8') ?>">
    <?php endif; ?>

    <?php if (!empty($schema)): ?>
        <script type="application/ld+json">
            <?= json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>
        </script>
    <?php endif; ?>

    <script src="<?= SITE_URL ?>/assets/js/tailwind.js"></script>
    <?php include __DIR__ . '/tailwind-config.php'; ?>
    <script src="<?= SITE_URL ?>/assets/js/navbar.js"></script>

    <link rel="stylesheet" href="<?= SITE_URL ?>/assets/intlTelInput/style.css?v=<?= (int) filemtime(__DIR__ . '/../assets/intlTelInput/style.css') ?>" />
    <script defer src="<?= SITE_URL ?>/assets/intlTelInput/main.js"></script>
    <script defer src="<?= SITE_URL ?>/assets/js/contact-form.js"></script>
</head>

<body>

    <!-- navbar -->
    <nav class="sticky top-0 z-50 border-b border-secondary-100 bg-white/95 shadow-sm backdrop-blur">
        <div class="mx-auto flex min-h-[88px] w-full max-w-screen-xl flex-wrap items-center justify-between px-4 py-4 lg:px-8">
            <a href="<?= SITE_URL ?>/" class="flex items-center gap-3 text-secondary-900 no-underline md:w-1/4">
                <img src="<?= SITE_URL ?>/assets/brand/heritage-drop-mark.svg" class="h-12 w-12 shrink-0" alt="">
                <span class="whitespace-nowrap text-lg font-semibold tracking-tight text-secondary-900">Heritage Drop Oil</span>
            </a>

            <div class="order-2 flex items-center justify-end gap-2 md:w-1/4">
                <a href="<?= SITE_URL ?>/contact"
                    class="hidden bg-primary-700 px-6 py-3.5 text-base font-semibold text-white transition hover:bg-secondary-900 focus:outline-none focus:ring-4 focus:ring-primary-200 sm:inline-flex">
                    Contact us
                </a>
                <button data-collapse-toggle="navbar-sticky" type="button"
                    class="inline-flex h-11 w-11 items-center justify-center border border-secondary-200 bg-white text-secondary-800 hover:bg-primary-50 focus:outline-none focus:ring-2 focus:ring-primary-300 md:hidden"
                    aria-controls="navbar-sticky" aria-expanded="false">
                    <span class="sr-only">Open main menu</span>
                    <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 17 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M1 1h15M1 7h15M1 13h15" />
                    </svg>
                </button>
            </div>
            <div id="navbar-sticky" class="order-1 hidden basis-full items-center justify-center md:flex md:w-2/4 md:basis-auto">
                <ul
                    class="nav-menu m-0 flex w-full list-none flex-col gap-1 p-0 font-medium md:w-auto md:flex-row md:items-center md:gap-7">
                    <li>
                        <a href="<?= SITE_URL ?>/"
                    class="block border-b border-secondary-100 px-3 py-3 text-base text-secondary-800 no-underline hover:text-primary-700 md:border-0 md:px-0 md:py-3">
                            Home
                        </a>
                    </li>
                    <li>
                        <a href="<?= SITE_URL ?>/about"
                            class="block border-b border-secondary-100 px-3 py-3 text-base text-secondary-800 no-underline hover:text-primary-700 md:border-0 md:px-0 md:py-3">
                            About
                        </a>
                    </li>
                    <!-- Dropdowns -->
                    <?php
                    $categories = $getdata->fetch('tbl_product_category', "parent_id='0' ORDER BY created_at");
                    foreach ($categories as $category):
                        $categoryUrl = SITE_URL . (isset($location) ? "/$location/{$category['url']}/" : "/{$category['url']}");
                        $subcategories = $getdata->fetch('tbl_product_category', "parent_id='{$category['id']}' ORDER BY created_at");

                        if (!empty($subcategories)): ?>
                            <li class="dropdown relative">
                                <div class="dropdown-link flex w-full items-center justify-between gap-2 px-3 py-3 text-base text-secondary-800 md:w-auto md:px-0 md:py-3">
                                    <a class="text-inherit no-underline hover:text-primary-700" href="<?= $categoryUrl ?>"><?= $category['title'] ?></a>
                                    <span class="dropdown-toggle cursor-pointer px-1">
                                        <svg class="w-2 h-2 ml-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4" />
                                        </svg>
                                    </span>
                                </div>
                                <div class="dropdown-menu absolute left-0 top-full z-20 hidden min-w-48 border border-secondary-100 bg-white p-2 font-normal shadow-lg">
                                    <ul class="m-0 list-none p-0 text-sm">
                                        <?php
                                        foreach ($subcategories as $subcategory):
                                            $subcategoryUrl = SITE_URL . (isset($location) ? "/$location/{$subcategory['url']}/" : "/{$subcategory['url']}");
                                        ?>
                                            <li>
                                                <a href="<?= $subcategoryUrl ?>" class="block px-4 py-2 text-secondary-800 no-underline hover:bg-primary-50 hover:text-primary-800">
                                                    <?= $subcategory['title'] ?>
                                                </a>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </li>
                        <?php else: ?>
                            <li>
                                <a href="<?= $categoryUrl ?>"
                                    class="block border-b border-secondary-100 px-3 py-3 text-base text-secondary-800 no-underline hover:text-primary-700 md:border-0 md:px-0 md:py-3">
                                    <?= $category['title'] ?>
                                </a>
                            </li>
                    <?php endif;
                    endforeach; ?>
                </ul>
            </div>
        </div>
    </nav>
