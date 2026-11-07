<?php
require_once __DIR__ . '/include/header_config.php';
$serviceLocations = $getdata->fetch('tbl_location', 'status = 1 ORDER BY title') ?: [];
$productCategories = $getdata->fetch('tbl_product_category', 'status = 1 AND parent_id = 0 ORDER BY created_at') ?: [];
$testimonials = $getdata->fetch('tbl_testimonial', 'status = 1 ORDER BY created_at DESC') ?: [];
if (!$testimonials) {
    $testimonials = [[
        'name' => 'A valued customer',
        'designation' => '',
        'company' => '',
        'description' => 'Heritage Drop Oil has been a dependable partner. The quality is consistent, the team is thoughtful, and every order arrives with the care we have come to expect.',
    ]];
}
$pageCanonical = rtrim(SITE_URL, '/') . '/';
$schemaSiteUrl = rtrim(SITE_URL, '/');
$organizationId = $schemaSiteUrl . '/#organization';
$schema = ['@context' => 'https://schema.org', '@graph' => [
    ['@type' => 'Organization', '@id' => $organizationId, 'name' => $site_name, 'url' => $pageCanonical, 'description' => $meta_desc,
        'knowsAbout' => array_values(array_filter(array_map(static fn(array $category): string => trim((string)($category['title'] ?? '')), $productCategories)))],
    ['@type' => 'WebSite', '@id' => $schemaSiteUrl . '/#website', 'url' => $pageCanonical, 'name' => $site_name, 'publisher' => ['@id' => $organizationId], 'inLanguage' => 'en'],
    ['@type' => 'WebPage', '@id' => $pageCanonical . '#webpage', 'url' => $pageCanonical, 'name' => $meta_title, 'description' => $meta_desc, 'isPartOf' => ['@id' => $schemaSiteUrl . '/#website'], 'publisher' => ['@id' => $organizationId], 'inLanguage' => 'en'],
]];
include __DIR__ . '/components/header.php';
?>
<main class="font-sans text-secondary-900">
    <section class="relative isolate min-h-[620px] overflow-hidden bg-gradient-to-br from-primary-100 via-primary-300 to-primary-600 md:min-h-[680px]" aria-label="Heritage Drop Oil introduction">
        <div class="absolute inset-0" aria-hidden="true">
            <?php
            $slideBackdrops = [
                'bg-[radial-gradient(ellipse_at_78%_45%,#f5d475_0_18%,#bd8328_18.5%_32%,transparent_32.5%),linear-gradient(120deg,#e7dec8,#bb8935)]',
                'bg-[radial-gradient(ellipse_at_80%_55%,#efd48a_0_20%,#b78232_20.5%_37%,transparent_37.5%),linear-gradient(125deg,#e7dfce,#c8a25d)]',
                'bg-[radial-gradient(ellipse_at_73%_40%,#f5dc91_0_16%,#b9802c_16.5%_34%,transparent_34.5%),linear-gradient(120deg,#e9e3d5,#c79a47)]',
                'bg-[radial-gradient(ellipse_at_82%_64%,#f4d989_0_18%,#ac7728_18.5%_36%,transparent_36.5%),linear-gradient(130deg,#e8e1d3,#c3923d)]',
                'bg-[radial-gradient(ellipse_at_74%_50%,#f6df9a_0_21%,#b47b28_21.5%_37%,transparent_37.5%),linear-gradient(120deg,#e5dfd0,#cda45b)]',
            ];
            for ($slide = 1; $slide <= 5; $slide++): ?>
                <div class="oil-hero-slide absolute inset-0 <?= $slideBackdrops[$slide - 1] ?> <?= $slide === 1 ? 'opacity-100 scale-100' : 'opacity-0 scale-[1.03]' ?> bg-cover bg-center transition-[opacity,transform] duration-1000 motion-reduce:transition-none" data-slide="<?= $slide ?>">
                    <img src="<?= SITE_URL ?>/assets/images/hero/slide-0<?= $slide ?>.jpg" alt="" class="h-full w-full object-cover" onerror="this.hidden=true">
                    <span class="absolute inset-0 bg-gradient-to-r from-secondary-950/75 via-secondary-950/30 to-transparent"></span>
                </div>
            <?php endfor; ?>
        </div>
        <div class="relative mx-auto flex min-h-[620px] w-full max-w-screen-xl items-center px-6 py-24 text-white md:min-h-[680px] md:px-12 lg:px-16">
            <div class="max-w-3xl">
                <span class="mb-5 block text-xs font-semibold uppercase tracking-[.2em] text-primary-200">From the source, with care</span>
                <h1 data-hero-quote class="max-w-3xl text-5xl font-normal leading-[1.04] tracking-tight sm:text-6xl md:text-7xl lg:text-8xl">Pure oils. Thoughtfully brought home.</h1>
                <p class="mt-7 max-w-xl text-base leading-8 text-white/85 md:text-lg">Discover carefully sourced oils, crafted for the table and the traditions around it.</p>
                <a href="<?= SITE_URL ?>/about" class="mt-8 inline-flex items-center gap-4 bg-primary-300 px-6 py-4 text-sm font-semibold text-secondary-950 transition-all duration-300 hover:bg-primary-200 hover:shadow-[0_0_26px_rgba(234,211,154,.65)] focus:outline-none focus:ring-4 focus:ring-primary-200">Know more <span aria-hidden="true">↗</span></a>
            </div>
        </div>
        <button class="oil-hero-previous absolute left-5 top-1/2 z-10 grid h-12 w-12 -translate-y-1/2 place-items-center rounded-full border border-white/60 bg-secondary-950/25 text-2xl text-white backdrop-blur hover:bg-secondary-900" type="button" aria-label="Previous slide">←</button>
        <button class="oil-hero-next absolute right-5 top-1/2 z-10 grid h-12 w-12 -translate-y-1/2 place-items-center rounded-full border border-white/60 bg-secondary-950/25 text-2xl text-white backdrop-blur hover:bg-secondary-900" type="button" aria-label="Next slide">→</button>
        <div class="absolute bottom-7 left-1/2 z-10 flex -translate-x-1/2 gap-3" aria-label="Choose a slide">
            <?php for ($slide = 1; $slide <= 5; $slide++): ?><button type="button" aria-label="Show slide <?= $slide ?>" aria-current="<?= $slide === 1 ? 'true' : 'false' ?>" data-go-to="<?= $slide - 1 ?>" class="h-2.5 w-2.5 rounded-full border border-white aria-[current=true]:bg-primary-300 aria-[current=true]:ring-4 aria-[current=true]:ring-primary-200/30"></button><?php endfor; ?>
        </div>
        <span class="absolute bottom-7 right-8 z-10 text-xs tracking-widest text-white/80" data-slide-count>01 / 05</span>
    </section>

    <section class="mx-auto grid w-full max-w-screen-xl items-center gap-12 px-6 py-20 sm:px-10 md:grid-cols-2 md:gap-20 md:py-28 lg:px-16">
        <div>
            <span class="text-xs font-semibold uppercase tracking-[.18em] text-primary-700">A little about us</span>
            <h2 class="mt-4 text-4xl font-normal leading-tight tracking-tight text-secondary-900 md:text-6xl">Get to know us</h2>
            <p class="mt-6 max-w-xl leading-8 text-secondary-600">Heritage Drop Oil brings carefully selected sunflower and palm oils from trusted producers to kitchens and food businesses. We focus on dependable sourcing, careful handling, and consistent quality in every batch.</p>
            <p class="mt-4 max-w-xl leading-8 text-secondary-600">From everyday cooking to large-scale food production, our oils are chosen for their reliable performance, clean flavour, and the care taken from origin to delivery.</p>
            <a href="<?= SITE_URL ?>/about" class="mt-6 inline-flex items-center gap-4 border-b border-primary-600 pb-2 font-semibold text-secondary-900 hover:text-primary-700">Our story <span aria-hidden="true">↗</span></a>
        </div>
        <div class="relative mx-auto grid aspect-square w-full max-w-[480px] place-items-center overflow-hidden rounded-[46%_54%_42%_58%/54%_43%_57%_46%] bg-gradient-to-br from-primary-100 via-primary-300 to-primary-600" role="img" aria-label="Heritage Drop Oil team member at work">
            <img src="<?= SITE_URL ?>/assets/images/hero/get-to-know-us.jpg" alt="Heritage Drop Oil team member at work" class="absolute inset-0 z-10 h-full w-full object-cover" hidden onerror="this.hidden=true" onload="this.hidden=false;this.nextElementSibling.hidden=true">
            <div class="absolute inset-0 grid place-items-center" aria-hidden="true">
                <div class="absolute inset-[10%] rounded-[54%_46%_60%_40%/42%_55%_45%_58%] border border-white/70"></div>
                <span class="h-40 w-28 rotate-45 rounded-[70%_8%_65%_55%] bg-gradient-to-br from-yellow-100 via-primary-500 to-yellow-700 shadow-2xl shadow-secondary-900/20 md:h-52 md:w-36"></span>
            </div>
            <span class="absolute bottom-[8%] right-[2%] bg-white/85 px-4 py-3 text-xs text-secondary-800 shadow-sm backdrop-blur">Made with care, from seed to table</span>
        </div>
    </section>

    <section class="bg-primary-50">
        <div class="mx-auto grid w-full max-w-screen-xl gap-12 px-6 py-20 sm:px-10 md:grid-cols-[.8fr_1.2fr] md:gap-20 md:py-28 lg:px-16">
            <div><span class="text-xs font-semibold uppercase tracking-[.18em] text-primary-700">Goodness at every step</span><h2 class="mt-4 text-4xl font-normal leading-tight tracking-tight text-secondary-900 md:text-6xl">Why choose us</h2><p class="mt-5 max-w-sm leading-8 text-secondary-600">Quality is a practice, built into every decision from origin to delivery.</p></div>
            <div class="border-t border-secondary-900/15">
                <article class="grid grid-cols-[3rem_1fr] gap-5 border-b border-secondary-900/15 py-6"><span class="grid h-11 w-11 place-items-center rounded-full border border-primary-600/50 bg-white/60 text-xl text-primary-700">✳</span><div><h3 class="font-semibold text-secondary-900">Quality in every batch</h3><p class="mt-2 leading-7 text-secondary-600">Careful checks at each stage help keep the colour, flavour, and performance consistent from delivery to delivery.</p></div></article>
                <article class="grid grid-cols-[3rem_1fr] gap-5 border-b border-secondary-900/15 py-6"><span class="grid h-11 w-11 place-items-center rounded-full border border-primary-600/50 bg-white/60 text-xl text-primary-700">⌁</span><div><h3 class="font-semibold text-secondary-900">Global sourcing insight</h3><p class="mt-2 leading-7 text-secondary-600">We follow harvests, origins, and market needs across regions to make informed choices about the oils we supply.</p></div></article>
                <article class="grid grid-cols-[3rem_1fr] gap-5 border-b border-secondary-900/15 py-6"><span class="grid h-11 w-11 place-items-center rounded-full border border-primary-600/50 bg-white/60 text-xl text-primary-700">◎</span><div><h3 class="font-semibold text-secondary-900">Trusted producer relationships</h3><p class="mt-2 leading-7 text-secondary-600">Close partnerships support responsible purchasing, clear communication, and dependable product availability.</p></div></article>
                <article class="grid grid-cols-[3rem_1fr] gap-5 border-b border-secondary-900/15 py-6"><span class="grid h-11 w-11 place-items-center rounded-full border border-primary-600/50 bg-white/60 text-xl text-primary-700">◌</span><div><h3 class="font-semibold text-secondary-900">Supply you can plan around</h3><p class="mt-2 leading-7 text-secondary-600">Responsive service and reliable delivery help kitchens, retailers, and food makers keep work moving.</p></div></article>
            </div>
        </div>
    </section>

      <!-- testimonial section -->
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

    <?php $contactHeading = 'Let’s start a conversation'; $contactInnerClass = 'px-2 sm:px-4 lg:px-10'; $contactSectionSpacing = 'py-20 md:py-28'; include __DIR__ . '/components/contact-form.php'; ?>
</main>
<script src="<?= SITE_URL ?>/assets/js/home-hero.js?v=<?= (int) filemtime(__DIR__ . '/assets/js/home-hero.js') ?>"></script>
<?php include __DIR__ . '/components/footer.php'; ?>
