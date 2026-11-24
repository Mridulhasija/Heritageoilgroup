<?php
require_once __DIR__ . '/include/header_config.php';

$cartSnapshot = heritage_cart_snapshot();
$hasCart = !empty($cartSnapshot['items']);
$shippingCost = $shippingMethod === 'pickup' ? 0 : $flatRate;

$total = 0.0;
if ($hasCart) {
    $total = (float) $cartSnapshot['total'];
}

$formId = 'checkout_' . bin2hex(random_bytes(8));

$pageCanonical = SITE_URL . '/checkout';
include('components/header.php');
?>

<section class="bg-secondary-50 min-h-[calc(100vh-220px)]">
    <div class="max-w-screen-xl mx-auto px-4 py-10 lg:py-14">
        <div class="flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
            <div>
                <p class="text-xs font-semibold tracking-[0.22em] text-primary-600">ENQUIRY CHECKOUT</p>
                <h1 class="mt-3 bebas text-4xl text-secondary-950 sm:text-5xl">Checkout</h1>
                <p class="mt-2 text-secondary-700">Share your details so our team can confirm availability and the next steps.</p>
            </div>
            <a href="<?= SITE_URL ?>/cart"
                class="inline-flex items-center justify-center rounded-full bg-secondary-950 px-5 py-2.5 text-sm font-semibold text-white hover:bg-black focus:outline-none focus:ring-2 focus:ring-secondary-500 focus:ring-offset-2 focus:ring-offset-secondary-50">
                Back to cart
            </a>
        </div>

        <?php if (!$hasCart || $total <= 0): ?>
            <div class="mt-10 rounded-3xl bg-white p-10 ring-1 ring-black/5 text-center">
                <div class="mx-auto inline-flex h-14 w-14 items-center justify-center rounded-2xl bg-secondary-950/5 text-secondary-950 ring-1 ring-black/5">
                    <svg viewBox="0 0 24 24" class="h-7 w-7" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M6 6h15l-2 8H7L6 6z" stroke="currentColor" stroke-width="2" stroke-linejoin="round" />
                        <path d="M6 6L5 3H2" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                        <path d="M9 20a1 1 0 100-2 1 1 0 000 2z" fill="currentColor" />
                        <path d="M18 20a1 1 0 100-2 1 1 0 000 2z" fill="currentColor" />
                    </svg>
                </div>
                <h2 class="mt-5 bebas text-2xl text-secondary-950">Your cart is empty</h2>
                <p class="mt-2 text-secondary-700">Add products before proceeding to checkout.</p>
                <a href="<?= SITE_URL ?>/shop.php"
                    class="mt-6 inline-flex items-center justify-center rounded-full bg-primary-500 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-primary-500/20 transition hover:bg-primary-600 focus:outline-none focus:ring-4 focus:ring-primary-200">
                    Browse products
                </a>
            </div>
        <?php else: ?>
            <p class="mt-8 rounded-xl border border-green-200 bg-green-50 p-4 text-sm leading-relaxed text-green-900"><strong>Shipping:</strong> Flat rate of <?= htmlspecialchars($getdata->formatPrice($flatRate), ENT_QUOTES, 'UTF-8') ?> per order anywhere in the USA — charged once, no matter how many containers. Crane-truck delivery, 7–10 days. Local pickup is also available.</p>
            <div class="mt-10 grid gap-8 lg:grid-cols-12 lg:items-start">
                <div class="lg:col-span-8">
                    <form method="POST" action="<?= SITE_URL ?>/order" class="checkout-form rounded-3xl bg-white p-7 ring-1 ring-black/5"
                        data-form-id="<?= $formId ?>" data-captcha-url="<?= SITE_URL ?>/captcha.php">
                        <div class="flex items-start justify-between gap-6">
                            <div>
                                <h2 class="bebas text-2xl text-secondary-950">Your enquiry details</h2>
                                <p class="mt-2 text-sm text-secondary-700">No payment is collected online. We confirm the order request by email or phone.</p>
                            </div>
                            <div class="rounded-2xl bg-secondary-50 px-4 py-3 text-sm ring-1 ring-black/5">
                                <div class="text-xs font-semibold tracking-wide text-secondary-600">Order total</div>
                                <div class="mt-1 font-semibold text-secondary-950"><?= htmlspecialchars($getdata->formatPrice($total + $shippingCost), ENT_QUOTES, 'UTF-8') ?></div>
                            </div>
                        </div>

                        <div class="mt-7 grid gap-4 md:grid-cols-2">
                            <div class="md:col-span-2">
                                <label for="fullName" class="block text-sm font-semibold text-secondary-700 mb-2">Full name</label>
                                <input type="text" id="fullName" name="full_name" required
                                    class="w-full rounded-xl border border-black/10 bg-white p-3 text-secondary-800 focus:outline-none focus:ring-2 focus:ring-primary-300 focus:ring-offset-2 focus:ring-offset-white"
                                    placeholder="Your name" />
                            </div>

                            <div>
                                <label for="phone" class="block text-sm font-semibold text-secondary-700 mb-2">Phone</label>
                                <input type="tel" id="phone" name="phone" required autocomplete="tel"
                                    class="phone w-full rounded-xl border border-black/10 bg-white p-3 text-secondary-800 focus:outline-none focus:ring-2 focus:ring-primary-300 focus:ring-offset-2 focus:ring-offset-white"
                                    placeholder="+1 555 123 4567" />
                                <div class="mt-2 flex items-center gap-3 text-xs">
                                    <span class="error-msg hidden text-red-600" role="alert"></span>
                                    <span class="valid-msg hidden text-green-700"></span>
                                </div>
                            </div>

                            <div>
                                <label for="email" class="block text-sm font-semibold text-secondary-700 mb-2">Email</label>
                                <input type="email" id="email" name="email" required
                                    class="w-full rounded-xl border border-black/10 bg-white p-3 text-secondary-800 focus:outline-none focus:ring-2 focus:ring-primary-300 focus:ring-offset-2 focus:ring-offset-white"
                                    placeholder="you@example.com" />
                            </div>
                        </div>

                        <div class="mt-7">
                            <h3 class="text-sm font-semibold text-secondary-700">Location details</h3>
                            <div class="mt-3 grid gap-4 md:grid-cols-2">
                                <div>
                                    <label for="city" class="block text-sm font-semibold text-secondary-700 mb-2">City</label>
                                    <input type="text" id="city" name="city" required
                                        class="w-full rounded-xl border border-black/10 bg-white p-3 text-secondary-800 focus:outline-none focus:ring-2 focus:ring-primary-300 focus:ring-offset-2 focus:ring-offset-white"
                                        placeholder="City" />
                                </div>
                                <div>
                                    <label for="zip" class="block text-sm font-semibold text-secondary-700 mb-2">Zip code</label>
                                    <input type="text" id="zip" name="zip_code" required
                                        class="w-full rounded-xl border border-black/10 bg-white p-3 text-secondary-800 focus:outline-none focus:ring-2 focus:ring-primary-300 focus:ring-offset-2 focus:ring-offset-white"
                                        placeholder="12345" />
                                </div>
                                <div class="md:col-span-2">
                                    <label for="streetAddress" class="block text-sm font-semibold text-secondary-700 mb-2">Street address</label>
                                    <input type="text" id="streetAddress" name="street_address" required
                                        class="w-full rounded-xl border border-black/10 bg-white p-3 text-secondary-800 focus:outline-none focus:ring-2 focus:ring-primary-300 focus:ring-offset-2 focus:ring-offset-white"
                                        placeholder="1234 Main St" />
                                </div>
                            </div>
                        </div>

                        <div class="mt-7">
                            <h3 class="text-sm font-semibold text-secondary-700">Preferred follow-up</h3>
                            <div class="mt-3 grid gap-3 sm:grid-cols-2">
                                <?php
                                $modes = [
                                    'phone_call' => 'Phone call',
                                    'email' => 'Email',
                                    // 'sms_text' => 'SMS/text',
                                    // 'whatsapp' => 'WhatsApp',
                                ];
                                $first = true;
                                foreach ($modes as $value => $label):
                                ?>
                                    <label class="flex items-center gap-3 rounded-2xl border border-black/10 bg-secondary-50 p-4 transition hover:bg-secondary-100">
                                        <input type="radio" name="contact_preference" value="<?= htmlspecialchars($value, ENT_QUOTES, 'UTF-8') ?>" required <?= $first ? 'checked' : '' ?> class="text-primary-600">
                                        <span class="text-sm font-semibold text-secondary-950"><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></span>
                                    </label>
                                <?php
                                    $first = false;
                                endforeach;
                                ?>
                            </div>
                        </div>

                        <div class="mt-7">
                            <h3 class="text-sm font-semibold text-secondary-700">Payment options</h3>
                            <div class="mt-3 rounded-2xl border border-primary-300 bg-secondary-50 p-4">
                                <label class="flex items-center gap-3 text-sm font-semibold text-secondary-950">
                                    <input type="radio" checked class="text-primary-600">
                                    <span>Direct bank transfer</span>
                                </label>
                                <p class="mt-3 text-sm leading-relaxed text-secondary-700">Payment is accepted by bank wire transfer only. After you place your order, our sales team will email you an official invoice with our bank details. We’ll process and ship your order once our accounting department confirms receipt of payment. Shipping and tracking information will follow after confirmation.</p>
                            </div>
                        </div>

                        <div class="mt-7">
                            <label for="checkout-captcha-code" class="block text-sm font-semibold text-secondary-700 mb-2">Verification code</label>
                            <div class="flex items-center gap-2">
                                <input type="text" id="checkout-captcha-code" name="captcha_code" required placeholder="Captcha"
                                    class="flex-1 rounded-xl border border-black/10 bg-white p-3 text-secondary-800 focus:outline-none focus:ring-2 focus:ring-primary-300 focus:ring-offset-2 focus:ring-offset-white" />
                                <img src="<?= SITE_URL ?>/captcha.php?generate=1&form_id=<?= $formId ?>"
                                    alt="CAPTCHA"
                                    class="captcha-image h-[50px] w-auto cursor-pointer rounded-xl border border-black/10 bg-white"
                                    data-form-id="<?= $formId ?>" />
                                <button type="button"
                                    class="captcha-refresh-btn rounded-xl border border-black/10 bg-secondary-50 px-3 py-3 text-secondary-700 transition hover:bg-secondary-100"
                                    title="Refresh CAPTCHA" data-form-id="<?= $formId ?>">
                                    ↻
                                </button>
                            </div>
                            <p class="captcha-error mt-2 hidden text-sm text-red-600" role="alert"></p>
                            <input type="hidden" name="captcha_form_id" value="<?= $formId ?>">
                        </div>

                        <button type="submit"
                            class="mt-7 inline-flex w-full items-center justify-center rounded-full bg-primary-500 px-6 py-3.5 text-sm font-semibold text-white shadow-lg shadow-primary-500/20 transition hover:bg-primary-600 focus:outline-none focus:ring-4 focus:ring-primary-200">
                            Submit enquiry request
                        </button>
                    </form>
                </div>

                <div class="lg:col-span-4">
                    <div class="rounded-3xl bg-white p-6 ring-1 ring-black/5 lg:sticky lg:top-28">
                        <h2 class="bebas text-2xl text-secondary-950">Order summary</h2>
                        <div class="mt-4 space-y-3 text-sm text-secondary-700">
                            <div class="flex justify-between gap-4"><span>Subtotal</span><span><?= htmlspecialchars($getdata->formatPrice($total), ENT_QUOTES, 'UTF-8') ?></span></div>
                            <div class="flex justify-between gap-4"><span><?= $shippingMethod === 'pickup' ? 'Local pickup' : 'Flat rate' ?></span><span><?= htmlspecialchars($getdata->formatPrice($shippingCost), ENT_QUOTES, 'UTF-8') ?></span></div>
                            <div class="flex justify-between gap-4 border-t border-secondary-200 pt-3 font-semibold text-secondary-950"><span>Total</span><span><?= htmlspecialchars($getdata->formatPrice($total + $shippingCost), ENT_QUOTES, 'UTF-8') ?></span></div>
                        </div>
                        <div class="mt-6 text-xs font-semibold tracking-wide text-secondary-600">Note</div>
                        <p class="mt-2 text-sm leading-relaxed text-secondary-700">
                            This checkout creates an enquiry request only. Our team will confirm availability, lawful purchase requirements, and next steps before anything is finalized.
                        </p>
                        <div class="mt-5 flex items-center gap-2 text-xs font-semibold text-secondary-600">
                            <svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 21s7-4.5 7-11a7 7 0 10-14 0c0 6.5 7 11 7 11z" stroke="currentColor" stroke-width="2" stroke-linejoin="round" />
                                <path d="M10.5 11.5l1.2 1.2 2.8-3" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            We respond quickly
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<script defer src="<?= SITE_URL ?>/assets/js/checkout-form.js"></script>

<?php include('components/footer.php'); ?>
