<?php
require_once __DIR__ . '/include/header_config.php';
$testimonials = $getdata->fetch('tbl_testimonial', 'status = 1 ORDER BY created_at DESC') ?: [];
if (!$testimonials) {
    $testimonials = [[
        'name' => 'A valued customer',
        'designation' => '',
        'company' => '',
        'description' => 'Heritage Drop Oil has been a dependable partner. The quality is consistent, the team is thoughtful, and every order arrives with the care we have come to expect.',
    ]];
}
$pageCanonical = SITE_URL . '/about';
include('components/header.php');
$pageHeroTitle = trim((string) ($respage->title ?? 'About Us')) ?: 'About Us';
$pageHeroImage = SITE_URL . '/assets/images/contact-img';
include __DIR__ . '/components/page-hero.php';
?>
<main class="bg-white">
    <section class="px-5 py-12 sm:px-8 md:py-16 lg:px-12" aria-label="About Heritage Drop Oil">
        <article class="mx-auto max-w-6xl text-secondary-700 [&_h1]:mb-5 [&_h1]:text-4xl [&_h1]:font-medium [&_h1]:text-secondary-900 [&_h2]:mb-4 [&_h2]:mt-8 [&_h2]:text-3xl [&_h2]:font-semibold [&_h2]:text-secondary-900 [&_h3]:mb-3 [&_h3]:mt-6 [&_h3]:text-2xl [&_h3]:font-semibold [&_h3]:text-secondary-900 [&_p]:mb-5 [&_p]:text-lg [&_p]:leading-8 [&_ul]:mb-5 [&_ul]:list-disc [&_ul]:pl-6 [&_li]:mb-2">
            <img src="<?= SITE_URL ?>/assets/images/oil-img.jpg" alt="Golden edible oil" class="float-right mb-6 ml-8 h-64 w-full rounded-xl object-cover sm:mb-8 sm:h-80 sm:w-2/5 lg:h-96" loading="lazy">
            <?= $respage->description ?? '' ?>
            <div class="clear-both"></div>
        </article>
    </section>

    <section class="bg-white px-5 pb-16 pt-8 sm:px-8 md:pb-20 lg:px-12" aria-labelledby="why-choose-title">
        <div class="mx-auto max-w-6xl">
            <header class="mx-auto max-w-3xl text-center">
                <p class="text-xs font-semibold uppercase tracking-[.3em] text-primary-700">Why choose us</p>
                <h2 id="why-choose-title" class="mt-3 text-4xl font-semibold leading-tight text-secondary-900 sm:text-5xl" style="font-family: Georgia,serif">Why Global Producers Trust Us</h2>
                <p class="mx-auto mt-4 max-w-3xl text-base leading-7 text-secondary-600">We deliver premium oils backed by meticulous sourcing, seamless logistics, and personalised service for every shipment.</p>
            </header>
            <div class="mt-14 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                <article class="rounded-3xl border border-secondary-200 bg-secondary-50 p-8 shadow-sm">
                    <span class="grid h-12 w-12 place-items-center rounded-full bg-amber-100 text-2xl text-primary-700" aria-hidden="true">↓</span>
                    <h3 class="mt-6 text-lg font-semibold text-secondary-900">Efficient Logistics</h3>
                    <p class="mt-3 text-sm leading-5 text-secondary-600">Integrated shipping and customs management reduces lead times so you receive your consignments on schedule.</p>
                </article>
                <article class="rounded-3xl border border-secondary-200 bg-secondary-50 p-8 shadow-sm">
                    <span class="grid h-12 w-12 place-items-center rounded-full bg-amber-100 text-2xl text-primary-700" aria-hidden="true">▤</span>
                    <h3 class="mt-6 text-lg font-semibold text-secondary-900">Custom Blends</h3>
                    <p class="mt-3 text-sm leading-5 text-secondary-600">Collaborate with our food technologists to craft bespoke blends aligned with your brand and market profile.</p>
                </article>
                <article class="rounded-3xl border border-secondary-200 bg-secondary-50 p-8 shadow-sm">
                    <span class="grid h-12 w-12 place-items-center rounded-full bg-amber-100 text-2xl text-primary-700" aria-hidden="true">▧</span>
                    <h3 class="mt-6 text-lg font-semibold text-secondary-900">Temperature Control</h3>
                    <p class="mt-3 text-sm leading-5 text-secondary-600">Climate-managed storage maintains freshness, preventing oxidation and preserving nutritional integrity.</p>
                </article>
                <article class="rounded-3xl border border-secondary-200 bg-secondary-50 p-8 shadow-sm">
                    <span class="grid h-12 w-12 place-items-center rounded-full bg-amber-100 text-2xl text-primary-700" aria-hidden="true">☷</span>
                    <h3 class="mt-6 text-lg font-semibold text-secondary-900">Regulatory Expertise</h3>
                    <p class="mt-3 text-sm leading-5 text-secondary-600">Navigate export documentation effortlessly with our compliance team guiding you through each jurisdiction.</p>
                </article>
            </div>
        </div>
    </section>

    <section class="mx-auto grid w-full max-w-screen-xl gap-8 px-6 py-20 sm:px-10 md:grid-cols-[.85fr_1.15fr] md:gap-20 md:py-28 lg:px-16" aria-labelledby="testimonial-title">
        <div><span class="text-xs font-semibold uppercase tracking-[.18em] text-primary-700">From our community</span><h2 id="testimonial-title" class="mt-4 text-4xl font-normal leading-tight tracking-tight text-secondary-900 md:text-6xl">What our clients say</h2></div>
        <div data-testimonial-carousel>
            <div class="relative min-h-[390px] overflow-hidden sm:min-h-[330px] lg:min-h-[300px]" aria-live="polite">
                <?php foreach ($testimonials as $index => $testimonial): ?>
                    <?php
                    $testimonialName = trim((string)($testimonial['name'] ?? '')) ?: 'A valued customer';
                    $testimonialRole = trim(implode(' — ', array_filter([
                        (string)($testimonial['designation'] ?? ''),
                        (string)($testimonial['company'] ?? ''),
                    ])));
                    ?>
                    <figure data-testimonial-slide class="absolute inset-0 border-l-2 border-primary-500 pl-7 pr-8 transition-all duration-700 md:pl-10 <?= $index === 0 ? 'translate-y-0 opacity-100' : 'pointer-events-none translate-y-2 opacity-0' ?>" aria-hidden="<?= $index === 0 ? 'false' : 'true' ?>">
                        <blockquote class="font-serif text-2xl leading-relaxed text-secondary-800 md:text-4xl">“<?= nl2br(htmlspecialchars((string)($testimonial['description'] ?? ''), ENT_QUOTES, 'UTF-8')) ?>”</blockquote>
                        <figcaption class="mt-6 text-sm text-secondary-600"><span class="font-medium text-secondary-800"><?= htmlspecialchars($testimonialName, ENT_QUOTES, 'UTF-8') ?></span><?php if ($testimonialRole !== ''): ?><span> · <?= htmlspecialchars($testimonialRole, ENT_QUOTES, 'UTF-8') ?></span><?php endif; ?></figcaption>
                    </figure>
                <?php endforeach; ?>
            </div>
            <div class="mt-5 flex items-center gap-3" aria-label="Testimonial controls">
                <button type="button" data-testimonial-prev class="grid h-10 w-10 place-items-center rounded-full border border-secondary-200 text-secondary-800 transition-colors hover:border-primary-500 hover:bg-primary-100" aria-label="Previous testimonial">←</button>
                <?php foreach ($testimonials as $index => $_testimonial): ?><button type="button" data-testimonial-to="<?= $index ?>" aria-label="Show testimonial <?= $index + 1 ?>" aria-current="<?= $index === 0 ? 'true' : 'false' ?>" class="h-2 w-2 rounded-full bg-secondary-300 transition-all aria-[current=true]:w-6 aria-[current=true]:bg-primary-700"></button><?php endforeach; ?>
                <button type="button" data-testimonial-next class="grid h-10 w-10 place-items-center rounded-full border border-secondary-200 text-secondary-800 transition-colors hover:border-primary-500 hover:bg-primary-100" aria-label="Next testimonial">→</button>
            </div>
        </div>
    </section>
</main>
<script src="<?= SITE_URL ?>/assets/js/home-hero.js?v=<?= (int) filemtime(__DIR__ . '/assets/js/home-hero.js') ?>"></script>
<?php include('components/footer.php');?>
