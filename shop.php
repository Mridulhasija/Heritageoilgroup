<?php
require_once __DIR__ . '/include/header_config.php';

$oilGroups = [
    'Sunflower oils' => [
        ['slug' => 'sunflower-oil', 'title' => 'Sunflower Oil'],
        ['slug' => 'sunflower-seed-oil', 'title' => 'Sunflower Seed Oil'],
        ['slug' => 'refined-sunflower-oil', 'title' => 'Refined Sunflower Oil'],
        ['slug' => 'unrefined-sunflower-oil', 'title' => 'Unrefined Sunflower Oil'],
        ['slug' => 'high-oleic-sunflower-oil', 'title' => 'High Oleic Sunflower Oil'],
        ['slug' => 'organic-sunflower-oil', 'title' => 'Organic Sunflower Oil'],
        ['slug' => 'cold-pressed-sunflower-oil', 'title' => 'Cold Pressed Sunflower Oil'],
    ],
    'Palm oils' => [
        ['slug' => 'palm-oil', 'title' => 'Palm Oil'],
        ['slug' => 'palmolein-oil', 'title' => 'Palmolein Oil'],
        ['slug' => 'palm-kernel-oil', 'title' => 'Palm Kernel Oil'],
        ['slug' => 'red-palm-oil', 'title' => 'Red Palm Oil'],
        ['slug' => 'rbd-palm-oil', 'title' => 'RBD Palm Oil'],
        ['slug' => 'organic-palm-oil', 'title' => 'Organic Palm Oil'],
        ['slug' => 'sustainable-palm-oil', 'title' => 'Sustainable Palm Oil'],
        ['slug' => 'palm-shortening', 'title' => 'Palm Shortening'],
    ],
];

// Card content is maintained here; resolve active catalog IDs only for cart actions.
$oilSlugs = array_column(array_merge(...array_values($oilGroups)), 'slug');
$quotedOilSlugs = implode(',', array_map(static fn(string $slug): string => "'" . $conn->real_escape_string($slug) . "'", $oilSlugs));
$oilIdResult = $conn->query("SELECT id, url FROM tbl_product_category WHERE status = 1 AND url IN ($quotedOilSlugs)");
$oilIdsBySlug = [];
if ($oilIdResult) {
    while ($oilRow = $oilIdResult->fetch_assoc()) {
        $oilIdsBySlug[(string)$oilRow['url']] = (string)$oilRow['id'];
    }
}

$pageCanonical = rtrim(SITE_URL, '/') . '/shop.php';
$meta_title = 'Oils | Heritage Drop Oil';
$meta_desc = 'Explore our sunflower and palm oil ranges, product details, and enquiry options.';
include __DIR__ . '/components/header.php';
$pageHeroTitle = 'Oils';
include __DIR__ . '/components/page-hero.php';
?>

<main class="bg-secondary-50 text-secondary-900">
    <section class="scroll-reveal mx-auto max-w-screen-xl px-5 py-14 sm:px-8 md:py-20 lg:px-12">
        <div class="max-w-3xl">
            <p class="text-xs font-semibold uppercase tracking-[.2em] text-primary-700">Thoughtfully sourced</p>
            <h2 class="mt-3 text-3xl font-normal tracking-tight text-secondary-950 sm:text-4xl">Oils for kitchens and makers</h2>
            <p class="mt-5 text-base leading-8 text-secondary-600">Explore our sunflower and palm oil ranges. If you need specifications or supply options, choose a product and send an enquiry to our team.</p>
        </div>
    </section>

    <?php foreach ($oilGroups as $groupTitle => $oilCards): ?>
        <section class="scroll-reveal border-y border-secondary-200 bg-white px-5 py-14 sm:px-8 md:py-20 lg:px-12" aria-label="<?= htmlspecialchars($groupTitle, ENT_QUOTES, 'UTF-8') ?>">
            <div class="mx-auto max-w-screen-xl">
                <div class="mb-10 flex flex-col gap-4 border-b border-secondary-200 pb-7 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[.2em] text-primary-700">Our range</p>
                        <h2 class="mt-2 text-3xl font-normal tracking-tight text-secondary-950 sm:text-4xl"><?= htmlspecialchars($groupTitle, ENT_QUOTES, 'UTF-8') ?></h2>
                    </div>
                    <p class="max-w-md text-sm leading-6 text-secondary-600">Explore our available oil products and enquire about the option that suits your requirements.</p>
                </div>

                <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3">
                    <?php foreach ($oilCards as $oil):
                        $oilImage = SITE_URL . '/assets/images/categories/' . rawurlencode($oil['slug']) . '.jpg';
                        $oilUrl = SITE_URL . '/' . rawurlencode($oil['slug']);
                        $oilId = $oilIdsBySlug[$oil['slug']] ?? '';
                    ?>
                        <article class="scroll-reveal overflow-hidden rounded-2xl border border-secondary-200 bg-white text-center shadow-md transition hover:-translate-y-1 hover:shadow-xl">
                            <a href="<?= htmlspecialchars($oilUrl, ENT_QUOTES, 'UTF-8') ?>" class="block">
                                <div class="overflow-hidden rounded-t-2xl bg-white">
                                    <img src="<?= htmlspecialchars($oilImage, ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($oil['title'], ENT_QUOTES, 'UTF-8') ?>" class="w-full h-auto object-cover aspect-square" loading="lazy">
                                </div>
                            </a>
                            <div class="p-5">
                                <h3 class="text-xl font-semibold text-secondary-900"><?= htmlspecialchars($oil['title'], ENT_QUOTES, 'UTF-8') ?></h3>
                                <p class="mt-3 text-sm font-semibold uppercase tracking-wide text-secondary-700">100% plant oil</p>
                                <p class="mt-2 min-h-12 text-sm leading-6 text-secondary-600">Quality plant oil, carefully sourced and prepared for your requirements.</p>
                                <div class="mt-5 grid gap-3 sm:grid-cols-2">
                                    <?php if ($oilId !== ''): ?>
                                        <button type="button" class="btn-cart inline-flex min-h-11 items-center justify-center rounded-md bg-primary-700 px-4 py-2 text-sm font-semibold text-white transition hover:bg-secondary-900" data-id="<?= htmlspecialchars($oilId, ENT_QUOTES, 'UTF-8') ?>">Add to Cart</button>
                                    <?php else: ?>
                                        <button type="button" class="inline-flex min-h-11 cursor-not-allowed items-center justify-center rounded-md bg-secondary-200 px-4 py-2 text-sm font-semibold text-secondary-500" disabled>Unavailable</button>
                                    <?php endif; ?>
                                    <button type="button" class="oil-enquire inline-flex min-h-11 items-center justify-center rounded-md border border-primary-700 px-4 py-2 text-sm font-semibold text-primary-800 transition hover:bg-primary-50" data-oil-name="<?= htmlspecialchars($oil['title'], ENT_QUOTES, 'UTF-8') ?>" data-oil-image="<?= htmlspecialchars($oilImage, ENT_QUOTES, 'UTF-8') ?>">Enquire now</button>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    <?php endforeach; ?>

    <section class="scroll-reveal mx-auto grid max-w-screen-xl gap-8 px-5 py-14 sm:px-8 md:grid-cols-3 md:py-20 lg:px-12" aria-label="Product support">
        <div class="border-t-2 border-primary-500 pt-5"><p class="text-xs font-semibold uppercase tracking-[.16em] text-primary-700">Product guidance</p><h2 class="mt-3 text-xl font-semibold text-secondary-950">Details for your application</h2><p class="mt-3 text-sm leading-7 text-secondary-600">Ask about specifications, packaging, and the information you need to assess a product.</p></div>
        <div class="border-t-2 border-primary-500 pt-5"><p class="text-xs font-semibold uppercase tracking-[.16em] text-primary-700">Supply planning</p><h2 class="mt-3 text-xl font-semibold text-secondary-950">Discuss your requirements</h2><p class="mt-3 text-sm leading-7 text-secondary-600">Share your expected volumes and destination so our team can discuss supply options.</p></div>
        <div class="border-t-2 border-primary-500 pt-5"><p class="text-xs font-semibold uppercase tracking-[.16em] text-primary-700">A direct conversation</p><h2 class="mt-3 text-xl font-semibold text-secondary-950">Enquire about a product</h2><p class="mt-3 text-sm leading-7 text-secondary-600">Use the enquiry form on any product card and we’ll have the product name ready for your message.</p></div>
    </section>
</main>

<div id="oil-enquiry-modal" data-captcha-url="<?= SITE_URL ?>/captcha.php" class="fixed inset-0 z-[100] hidden items-center justify-center overflow-y-auto bg-secondary-950/65 px-4 py-8 backdrop-blur-sm" role="dialog" aria-modal="true" aria-labelledby="oil-modal-title">
    <div class="relative my-auto w-full max-w-3xl rounded-2xl bg-[#f5f0e4] p-4 shadow-2xl sm:p-7">
        <button type="button" class="oil-modal-close absolute right-3 top-3 z-10 grid h-10 w-10 place-items-center rounded-full bg-white text-2xl text-secondary-800 shadow" aria-label="Close enquiry form">&times;</button>
        <div class="mb-5 flex items-center gap-4 pr-12"><img id="oil-modal-image" src="" alt="" class="h-16 w-16 rounded-xl border border-primary-200 bg-white object-cover shadow-sm"><div><p class="text-xs font-semibold uppercase tracking-[.16em] text-primary-700">Product enquiry</p><h2 id="oil-modal-title" class="mt-1 text-2xl font-semibold text-secondary-900"></h2></div></div>
        <?php $shopFormId = uniqid('shop_oil_', true); ?>
        <form action="<?= SITE_URL ?>/contact.php" method="POST" class="contact-form space-y-4" data-form-id="<?= htmlspecialchars($shopFormId, ENT_QUOTES, 'UTF-8') ?>">
            <div class="form-response-message hidden rounded border px-4 py-3 text-sm font-medium" role="alert" aria-live="polite"></div><input type="hidden" name="product_title" id="oil-modal-product-title" value="">
            <div class="grid gap-4 sm:grid-cols-2">
                <label class="flex flex-col text-xs font-semibold text-secondary-700">Your name<input type="text" name="name" autocomplete="name" placeholder="Full name" required maxlength="100" class="mt-2 rounded-md border border-secondary-200 bg-white px-3 py-3 text-sm font-normal"></label>
                <label class="flex flex-col text-xs font-semibold text-secondary-700">Email address<input type="email" name="email" autocomplete="email" placeholder="you@example.com" required maxlength="150" class="mt-2 rounded-md border border-secondary-200 bg-white px-3 py-3 text-sm font-normal"></label>
                <label class="flex flex-col text-xs font-semibold text-secondary-700">Phone number<div class="phone-field mt-2"><input type="tel" name="phone" class="phone w-full rounded-md border border-secondary-200 bg-white px-3 py-3 text-sm" autocomplete="tel" placeholder="Your phone number" required maxlength="20"><span class="error-msg text-xs text-red-700"></span><span class="valid-msg text-xs text-green-700"></span></div></label>
                <label class="flex flex-col text-xs font-semibold text-secondary-700">Country or region<input type="text" name="location" autocomplete="country-name" placeholder="Where are you contacting us from?" required maxlength="120" class="mt-2 rounded-md border border-secondary-200 bg-white px-3 py-3 text-sm font-normal"></label>
                <label class="flex flex-col text-xs font-semibold text-secondary-700 sm:col-span-2">Message<textarea name="message" rows="3" placeholder="How can we help?" required maxlength="1000" class="mt-2 resize-y rounded-md border border-secondary-200 bg-white px-3 py-3 text-sm font-normal"></textarea></label>
            </div>
            <div><label for="shop-oil-captcha" class="block text-xs font-semibold text-secondary-700">Verification code</label><div class="mt-2 flex flex-wrap items-center gap-2"><input id="shop-oil-captcha" type="text" name="captcha_code" class="captcha-input min-w-0 flex-1 rounded-md border border-secondary-200 bg-white px-3 py-3 text-sm" placeholder="Enter the code" required><img src="<?= SITE_URL ?>/captcha.php?generate=1&amp;form_id=<?= rawurlencode($shopFormId) ?>" alt="CAPTCHA" class="captcha-image h-12 rounded-md border border-secondary-200" data-form-id="<?= htmlspecialchars($shopFormId, ENT_QUOTES, 'UTF-8') ?>"><button class="captcha-refresh-btn grid h-11 w-11 place-items-center rounded-md border border-secondary-200 bg-white text-xl" type="button" title="Refresh verification code" aria-label="Refresh verification code">↻</button></div><input type="hidden" name="captcha_form_id" class="captcha-form-id" value="<?= htmlspecialchars($shopFormId, ENT_QUOTES, 'UTF-8') ?>"></div>
            <button type="submit" data-loading-text="Sending…" class="inline-flex min-h-12 w-full items-center justify-center gap-3 bg-primary-700 px-5 py-3 text-sm font-semibold text-white hover:bg-secondary-900">Send enquiry <span aria-hidden="true">↗</span></button>
        </form>
    </div>
</div>

<script src="<?= SITE_URL ?>/assets/js/category.js?v=<?= (int)filemtime(__DIR__ . '/assets/js/category.js') ?>"></script>
<script src="<?= SITE_URL ?>/assets/js/scroll-reveal.js?v=<?= (int)filemtime(__DIR__ . '/assets/js/scroll-reveal.js') ?>" defer></script>
<?php include __DIR__ . '/components/footer.php'; ?>
