<?php
require_once __DIR__ . '/include/header_config.php';
$pageCanonical = SITE_URL . '/service-areas';
include('components/header.php');
$pageHeroTitle = trim((string) ($respage->title ?? 'Service Areas')) ?: 'Service Areas';
include __DIR__ . '/components/page-hero.php';
?>

<section class="py-16 text-center">
    <div class="max-w-screen-xl mx-auto px-4">
        <div>
            <ul class="flex flex-wrap justify-center gap-4 text-lg font-semibold">
                <?php
                $service_locations = $getdata->fetch('tbl_location');
                foreach ($service_locations as $service_location):
                ?>
                    <li class="w-52 list-none">
                        <a href="<?= SITE_URL . '/' . $service_location['url'] . '/'; ?>"
                            class="block w-full truncate rounded-md bg-primary-900 px-4 py-2.5 text-white transition-colors hover:bg-primary-700 focus:outline-none focus:ring-4 focus:ring-primary-300"
                            title="<?= $service_location['title']; ?>">
                            <?= $service_location['title']; ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</section>


<?php include('components/footer.php'); ?>
