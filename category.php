<?php
require_once __DIR__ . '/include/header_config.php';
require_once __DIR__ . '/include/location-template.php';

// If location is set, validate it first
if (isset($location)) {
    // Checking if location is valid
    $stmt = $conn->prepare("SELECT title, data FROM tbl_location WHERE status = 1 AND url = ?");
    $stmt->bind_param("s", $location);
    $stmt->execute();

    // Bind the result to the $location_title variable
    $stmt->bind_result($location_title, $location_json);

    // Fetch the result
    $stmt->fetch();

    // Check if the result is valid
    if ($location_title === null) {
        $stmt->close();
        http_response_code(404);
        header("Location: " . SITE_URL);
        exit();
    }

    $stmt->close();
    $location_data = json_decode($location_json ?? '', true);
    $location_data = is_array($location_data) ? $location_data : [];
}

$stmt = $conn->prepare("SELECT id, parent_id, title, url, heading, service_heading, service_meta_title, service_meta_keyword, service_meta_desc, service_description, faqs, service_faqs, icon, image, banner, short_description, description, meta_title, meta_keyword, meta_desc, price, discounted_price, unit, status, created_at, updated_at FROM `tbl_product_category` WHERE status = 1 AND url = ?");
$stmt->bind_param("s", $slug);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows <= 0) {
    http_response_code(404);
    header('location: ' . SITE_URL);
    exit();
}

$stmt->bind_result(
    $id,
    $parent_id,
    $title,
    $url,
    $heading,
    $service_heading,
    $service_meta_title,
    $service_meta_keyword,
    $service_meta_desc,
    $service_description,
    $faqs_json,
    $service_faqs_json,
    $icon,
    $image,
    $banner,
    $short_description,
    $description,
    $meta_title,
    $meta_keyword,
    $meta_desc,
    $price,
    $discounted_price,
    $unit,
    $status,
    $created_at,
    $updated_at
);

$stmt->fetch();

$stmt->close();

$category_name = $title;
$page_heading = $heading ?: $category_name;
$categoryImageSlug = strtolower(trim((string)$url));
$categoryImageSlug = preg_replace('/[^a-z0-9-]+/', '-', $categoryImageSlug);
$categoryImageSlug = trim($categoryImageSlug, '-');
$categoryImageFile = __DIR__ . '/assets/images/categories/' . $categoryImageSlug . '.jpg';
$categoryOilImage = is_file($categoryImageFile)
    ? SITE_URL . '/assets/images/categories/' . rawurlencode($categoryImageSlug) . '.jpg'
    : (!empty($image) ? media_folder . $image : '');
$faq_json = $faqs_json ?? null;
if (isset($location)) {
    $replace = static fn($value, bool $escape = false) => replaceLocationTemplate((string) $value, $location_title, $location_data, $category_name, $escape);
    $page_heading = $replace($service_heading ?: $category_name);
    $meta_title = $replace($service_meta_title ?: $page_heading);
    $meta_keyword = $replace($service_meta_keyword);
    $meta_desc = $replace($service_meta_desc);
    $description = $replace($service_description ?: $description, true);
    $faq_json = $service_faqs_json;
}
$faqs = json_decode($faq_json ?: '[]', true);
$faqs = is_array($faqs) ? array_values(array_filter($faqs, static function ($item) {
    return is_array($item) && trim((string) ($item['question'] ?? '')) !== '' && trim((string) ($item['answer'] ?? '')) !== '';
})) : [];
if (isset($location)) {
    foreach ($faqs as &$faq) {
        $faq['question'] = $replace($faq['question']);
        $faq['answer'] = $replace($faq['answer']);
    }
    unset($faq);
}

$sub_cats = $getdata->fetch('tbl_product_category', "parent_id='$id' ORDER BY created_at");

$pageCanonical = SITE_URL . '/' . (isset($location) ? $location . '/' : '') . $url . (isset($location) ? '/' : '');
$offerPrice = '100';
$currencyCode = explode('-', CURRENCY, 2)[0];
$unitCodes = ['KGM', 'GRM', 'TNE', 'LBR', 'LTR', 'MLT', 'GLL', 'C62', 'BG', 'BO', 'CT'];
$unit = $unit ?? 'C62';
$unitCode = in_array((string) $unit, $unitCodes, true) ? (string) $unit : 'C62';
$unitLabels = ['KGM' => 'kg', 'GRM' => 'g', 'TNE' => 'tonne', 'LBR' => 'lb', 'LTR' => 'L', 'MLT' => 'ml', 'GLL' => 'US gal', 'C62' => 'unit', 'BG' => 'bag', 'BO' => 'bottle', 'CT' => 'carton'];

// Offers apply to product pages only. Use the discounted price when available,
// otherwise fall back to the regular price.
if (count($sub_cats) === 0) {
    $candidatePrice = (is_numeric($discounted_price) && (float) $discounted_price > 0)
        ? $discounted_price
        : $price;

    if (is_numeric($candidatePrice) && (float) $candidatePrice > 0) {
        $offerPrice = number_format((float) $candidatePrice, 2, '.', '');
    }
}
$hasPublishedPrice = count($sub_cats) === 0 && is_numeric($candidatePrice ?? null) && (float) ($candidatePrice ?? 0) > 0;
$displayPrice = $hasPublishedPrice ? $getdata->formatPrice((float) $candidatePrice) : null;
$displayOriginalPrice = (is_numeric($price) && (float) $price > 0 && $displayPrice !== null && (float) $price !== (float) $candidatePrice)
    ? $getdata->formatPrice((float) $price)
    : null;

$schema = [
    '@context' => 'https://schema.org',
    '@graph' => [
        [
            '@type' => count($sub_cats) === 0 ? 'Product' : 'CollectionPage',
            '@id' => $pageCanonical . (count($sub_cats) === 0 ? '#product' : '#collection'),
            'name' => $category_name,
            'url' => $pageCanonical,
            'description' => strip_tags(!empty($short_description)
                ? $short_description
                : implode(' ', array_slice(
                    preg_split('/(?<=[.!?])\s+/', strip_tags($description), -1, PREG_SPLIT_NO_EMPTY),
                    0,
                    2
                ))),
            'category' => $category_name,
        ],
        [
            '@type' => 'BreadcrumbList',
            '@id' => $pageCanonical . '#breadcrumb',
            'itemListElement' => [
                [
                    '@type' => 'ListItem',
                    'position' => 1,
                    'name' => 'Home',
                    'item' => SITE_URL . '/',
                ],
                [
                    '@type' => 'ListItem',
                    'position' => 2,
                    'name' => $title,
                    'item' => $pageCanonical,
                ],
            ],
        ],
    ],
];

if (!empty($image)) {
    $schema['@graph'][0]['image'] = [media_folder . $image];
}

if (count($sub_cats) === 0) {
    // Publish these customer-facing policy pages at the matching URLs before
    // enabling product offers in production.
    $shippingPolicyUrl = rtrim(SITE_URL, '/') . '/shipping-policy';
    $returnPolicyUrl = rtrim(SITE_URL, '/') . '/return-policy';

    $schema['@graph'][0]['offers'] = [
        '@type' => 'Offer',
        'url' => $pageCanonical,
        'seller' => [
            '@type' => 'Organization',
            'name' => $site_name,
            'url' => rtrim(SITE_URL, '/') . '/',
        ],
        'shippingDetails' => [
            '@type' => 'OfferShippingDetails',
            'shippingSettingsLink' => $shippingPolicyUrl,
            'shippingRate' => [
                '@type' => 'MonetaryAmount',
                // No additional shipping charge is shown in the offer;
                // destination-specific freight is confirmed by quotation.
                'value' => '0.00',
                'currency' => $currencyCode,
            ],
            'deliveryTime' => [
                '@type' => 'ShippingDeliveryTime',
                'handlingTime' => [
                    '@type' => 'QuantitativeValue',
                    'minValue' => 1,
                    'maxValue' => 3,
                    'unitCode' => 'DAY',
                ],
                'transitTime' => [
                    '@type' => 'QuantitativeValue',
                    'minValue' => 7,
                    'maxValue' => 30,
                    'unitCode' => 'DAY',
                ],
            ],
        ],
        'hasMerchantReturnPolicy' => [
            '@type' => 'MerchantReturnPolicy',
            'merchantReturnLink' => $returnPolicyUrl,
            'returnPolicyCategory' => 'https://schema.org/MerchantReturnNotPermitted',
        ],
    ];

    $schema['@graph'][0]['offers']['price'] = $offerPrice;
    $schema['@graph'][0]['offers']['priceCurrency'] = $currencyCode;
    $schema['@graph'][0]['offers']['availability'] = 'https://schema.org/InStock';
    $schema['@graph'][0]['offers']['itemCondition'] = 'https://schema.org/NewCondition';
    $schema['@graph'][0]['offers']['priceSpecification'] = [
        '@type' => 'UnitPriceSpecification',
        'price' => $offerPrice,
        'priceCurrency' => $currencyCode,
        'referenceQuantity' => [
            '@type' => 'QuantitativeValue',
            'value' => 1,
            'unitCode' => $unitCode,
        ],
    ];
}

if ($faqs) {
    $schema['@graph'][] = [
        '@type' => 'FAQPage',
        '@id' => $pageCanonical . '#faq',
        'mainEntity' => array_map(static fn($faq) => [
            '@type' => 'Question',
            'name' => strip_tags($faq['question']),
            'acceptedAnswer' => ['@type' => 'Answer', 'text' => $faq['answer']],
        ], $faqs),
    ];
}

include('components/header.php');
?>

<?php
$pageHeroTitle = $page_heading;
$pageHeroImage = SITE_URL . '/assets/images/contact-img';
include __DIR__ . '/components/page-hero.php';
?>


<?php if (count($sub_cats) > 0): // category page blocks ?>

    <section class="bg-secondary-50 px-6 py-12">
        <h2 class="mb-12 text-center text-4xl font-semibold text-secondary-900 lg:text-5xl">
            Related Products
        </h2>

        <div class="mx-auto grid max-w-6xl grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3">
            <?php
            foreach ($sub_cats as $sub_cat):
                $url = SITE_URL . '/' . (isset($location) ? $location . '/' : '') . $sub_cat['url'] . (isset($location) ? '/' : '');
                $subCategorySlug = strtolower(trim((string)($sub_cat['url'] ?? '')));
                $subCategorySlug = trim((string) preg_replace('/[^a-z0-9-]+/', '-', $subCategorySlug), '-');
                $subCategoryImageFile = __DIR__ . '/assets/images/categories/' . $subCategorySlug . '.jpg';
                $subCategoryImage = is_file($subCategoryImageFile)
                    ? SITE_URL . '/assets/images/categories/' . rawurlencode($subCategorySlug) . '.jpg'
                    : (!empty($sub_cat['image']) ? media_folder . $sub_cat['image'] : '');
            ?>
                <div class="text-center">
                    <a href="<?= $url ?>">
                        <div class="overflow-hidden border border-secondary-100 bg-white shadow-md transition hover:-translate-y-1 hover:shadow-xl">
                            <img
                                src="<?= htmlspecialchars($subCategoryImage, ENT_QUOTES, 'UTF-8') ?>"
                                alt="<?= htmlspecialchars((string)($sub_cat['title'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                                class="w-full h-auto object-cover aspect-square" />
                            <div class="bg-primary-700">
                                <h3 class="py-3 text-lg font-medium text-white"><?= $sub_cat['title'] ?></h3>
                            </div>
                            <?php
                            $subPrice = (is_numeric($sub_cat['discounted_price'] ?? null) && (float) $sub_cat['discounted_price'] > 0)
                                ? $sub_cat['discounted_price']
                                : ($sub_cat['price'] ?? '');
                            $subUnit = isset($unitLabels[$sub_cat['unit'] ?? '']) ? $sub_cat['unit'] : 'C62';
                            if (is_numeric($subPrice) && (float) $subPrice > 0):
                            ?>
                                <p class="bg-white px-3 py-3 text-sm font-semibold text-secondary-800">
                                    <?= htmlspecialchars($getdata->formatPrice((float) $subPrice)) ?>
                                    <span class="font-normal text-secondary-700">/ <?= htmlspecialchars($unitLabels[$subUnit]) ?></span>
                                </p>
                            <?php endif; ?>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>

        </div>
    </section>

<?php endif; ?>

<!-- content -->
<section class="mx-auto grid w-full max-w-screen-xl gap-8 px-4 py-10 md:grid-cols-[minmax(0,2fr)_minmax(260px,1fr)] md:px-8">

    <div class="min-w-0">

        <div class="space-y-4 text-base leading-8 text-secondary-600 [&_h1]:mb-5 [&_h1]:text-4xl [&_h1]:font-medium [&_h1]:text-secondary-900 [&_h2]:mb-4 [&_h2]:mt-8 [&_h2]:text-2xl [&_h2]:font-semibold [&_h2]:text-secondary-900 [&_h3]:mb-3 [&_h3]:mt-6 [&_h3]:text-xl [&_h3]:font-semibold [&_p]:mb-5 [&_ul]:mb-5 [&_ul]:list-disc [&_ul]:pl-6 [&_ol]:mb-5 [&_ol]:list-decimal [&_ol]:pl-6 [&_li]:mb-2 [&_a]:font-medium [&_a]:text-primary-700 [&_a]:underline [&_blockquote]:border-l-4 [&_blockquote]:border-primary-500 [&_blockquote]:pl-5 [&_blockquote]:italic [&_img]:my-6 [&_img]:h-auto [&_img]:max-w-full">
            <?= $description ?>
        </div>

        <?php if ($faqs): ?>
            <section class="mt-8" aria-labelledby="faq-heading">
                <h2 id="faq-heading" class="text-2xl font-semibold">Frequently Asked Questions</h2>
                <?php foreach ($faqs as $faq): ?>
                    <details class="mt-4 border border-secondary-200 bg-white p-4">
                        <summary class="cursor-pointer font-semibold"><?= htmlspecialchars($faq['question'], ENT_QUOTES, 'UTF-8') ?></summary>
                        <div class="mt-3"><?= nl2br(htmlspecialchars($faq['answer'], ENT_QUOTES, 'UTF-8')) ?></div>
                    </details>
                <?php endforeach; ?>
            </section>
        <?php endif; ?>

    </div>


    <!-- Category enquiry form -->
    <aside class="self-start">

        <figure class="relative mb-6 aspect-square overflow-hidden rounded-xl border border-primary-200 bg-gradient-to-br from-primary-50 via-primary-100 to-primary-300 shadow-lg shadow-secondary-900/10">
            <img class="h-full w-full object-cover transition-transform duration-500 hover:scale-105" src="<?= htmlspecialchars($categoryOilImage ?: media_folder . $image, ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars((string)$getdata->fetchAltText($image), ENT_QUOTES, 'UTF-8') ?>" onerror="this.hidden=true" />
            <figcaption class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-secondary-950/80 to-transparent px-5 pb-5 pt-12 text-lg font-medium text-white"><?= htmlspecialchars((string)$category_name, ENT_QUOTES, 'UTF-8') ?></figcaption>
        </figure>
        <?php if ($displayPrice !== null): ?>
            <div class="mb-6 bg-primary-50 px-5 py-4">
                <div class="text-xs font-semibold uppercase tracking-wide text-secondary-700">From</div>
                <div class="mt-1 text-2xl font-semibold text-secondary-900">
                    <?= htmlspecialchars($displayPrice) ?>
                    <span class="text-sm font-normal text-secondary-700">/ <?= htmlspecialchars($unitLabels[$unitCode]) ?></span>
                    <?php if ($displayOriginalPrice !== null): ?><del class="ml-2 text-sm font-normal text-gray-500"><?= htmlspecialchars($displayOriginalPrice) ?></del><?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </aside>

</section>

<?php $contactHeading = 'Ask about this product'; $contactContext = 'I would like to know more about ' . $category_name . '.'; $contactInnerClass = 'md:px-2'; $contactSectionSpacing = 'py-10'; include __DIR__ . '/components/contact-form.php'; ?>

<?php
include('components/footer.php');
?>
