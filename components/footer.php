<?php
$footerCategories = $getdata->fetch('tbl_product_category', 'status = 1 AND parent_id = 0 ORDER BY created_at') ?: [];
$footerUrl = static fn(string $path): string => rtrim(SITE_URL, '/') . '/' . ltrim($path, '/');
$footerSubcategories = [];
foreach ($footerCategories as $footerCategory) {
    $footerSubcategories[(string)$footerCategory['id']] = $getdata->fetch('tbl_product_category', "status = 1 AND parent_id = '" . (int)$footerCategory['id'] . "' ORDER BY created_at") ?: [];
}
?>
<footer class="bg-secondary-950 px-6 pt-16 text-white sm:px-10 lg:px-16">
    <div class="mx-auto grid w-full max-w-screen-xl gap-10 pb-12 sm:grid-cols-2 md:grid-cols-[1.2fr_.7fr_1.2fr_1fr] md:gap-12">
        <div>
            <a href="<?= SITE_URL ?>/" class="inline-flex min-h-12 items-center gap-3 text-lg font-semibold tracking-tight text-white no-underline">
                <img src="<?= SITE_URL ?>/assets/brand/heritage-drop-mark.svg" alt="" class="h-11 w-11 shrink-0">
                <span>Heritage Drop Oil</span>
            </a>
            <p class="mt-4 max-w-xs text-base leading-7 text-white/70">Thoughtfully sourced oils, bringing the goodness of the harvest to homes and businesses.</p>
        </div>
        <nav class="flex flex-col items-start gap-3 text-base" aria-label="Quick links"><h2 class="mb-2 text-base font-semibold text-primary-300">Quick links</h2><a class="text-white/75 no-underline hover:text-primary-200" href="<?= SITE_URL ?>/">Home</a><a class="text-white/75 no-underline hover:text-primary-200" href="<?= SITE_URL ?>/about">About us</a><a class="text-white/75 no-underline hover:text-primary-200" href="<?= SITE_URL ?>/contact">Contact us</a><a class="text-white/75 no-underline hover:text-primary-200" href="<?= SITE_URL ?>/service-areas">Service areas</a></nav>
        <nav class="flex flex-col items-start gap-3 text-base" aria-label="Our products"><h2 class="mb-2 text-base font-semibold text-primary-300">Our products</h2>
            <?php foreach ($footerCategories as $category): ?>
                <a class="text-white/85 no-underline hover:text-primary-200" href="<?= $footerUrl($category['url'] ?? '') ?>"><?= htmlspecialchars((string)$category['title'], ENT_QUOTES, 'UTF-8') ?></a>
                <?php foreach ($footerSubcategories[(string)$category['id']] ?? [] as $subcategory): ?><a class="-mt-2 pl-3 text-sm text-white/65 no-underline hover:text-primary-200" href="<?= $footerUrl($subcategory['url'] ?? '') ?>"><?= htmlspecialchars((string)$subcategory['title'], ENT_QUOTES, 'UTF-8') ?></a><?php endforeach; ?>
            <?php endforeach; ?>
        </nav>
        <div class="flex flex-col items-start gap-3 text-base"><h2 class="mb-2 text-base font-semibold text-primary-300">Contact us</h2>
            <?php if (!empty($configData['address'])): ?><p class="m-0 max-w-xs whitespace-pre-line leading-7 text-white/65"><?= htmlspecialchars((string)$configData['address'], ENT_QUOTES, 'UTF-8') ?></p><?php endif; ?>
            <?php if (!empty($configData['phone_1'])): ?><a class="text-white/75 no-underline hover:text-primary-200" href="tel:<?= htmlspecialchars(preg_replace('/[^\d+]/', '', (string)$configData['phone_1']), ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars((string)$configData['phone_1'], ENT_QUOTES, 'UTF-8') ?></a><?php endif; ?>
            <?php if (!empty($configData['email_1'])): ?><a class="text-white/75 no-underline hover:text-primary-200" href="mailto:<?= htmlspecialchars((string)$configData['email_1'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars((string)$configData['email_1'], ENT_QUOTES, 'UTF-8') ?></a><?php endif; ?>
        </div>
    </div>
    <div class="-mx-6 border-t border-white/15 sm:-mx-10 lg:-mx-16"><div class="mx-auto flex w-full max-w-screen-xl flex-col justify-between gap-3 px-6 py-5 text-sm text-white/65 sm:flex-row sm:px-10 lg:px-8"><span>© 2026 Heritagedropoil. All rights reserved.</span><a class="text-white/75 no-underline hover:text-primary-200" href="<?= SITE_URL ?>/contact">Talk to our team <span aria-hidden="true">↗</span></a></div></div>
</footer>
</body>
</html>
