<?php
require_once __DIR__ . '/include/header_config.php';
$pageCanonical = SITE_URL . '/' . $router;

include('components/header.php');

$pageHeroTitle = trim((string) ($respage->title ?? 'Information')) ?: 'Information';
include __DIR__ . '/components/page-hero.php';
?>

<main class="bg-secondary-50 px-4 py-12 sm:px-6 md:py-16 lg:px-8">
    <?php $richContent = $respage->description ?? ''; include __DIR__ . '/components/rich-content.php'; ?>
</main>

<?php include('components/footer.php'); ?>
