<?php
include('include/header_config.php');

// Initialize cart data
$total = 0;
$cartItems = [];
$removedItems = []; // Track removed out-of-stock items

$cartData = isset($_COOKIE['cart']) ? json_decode($_COOKIE['cart'], true) ?? [] : [];

foreach ($cartData as $item) {
    $productID = $getdata->sanitizeInput($item['productID'] ?? null);
    if (!$productID) continue;

    $quantity = filter_var(
        ($item['quantity'] ?? 1),
        FILTER_VALIDATE_INT,
        ['options' => ['min_range' => 1]]
    ) ?: 1;

    $productData = $getdata->FetchProductData($productID);
    if (empty($productData)) continue;

    // Check if product is out of stock
    if (isset($productData['status']) && $productData['status'] == 0) {
        $removedItems[] = $productData['title'];

        // Remove from cart based on user type
        if ($isLoggedIn) {
            $stmt = $conn->prepare("DELETE FROM tbl_cart_items WHERE id = ?");
            $stmt->execute([$item['id']]);
        } else {
            // Don't add to updated cart data (effectively removing it)
        }
        continue; // Skip this item
    }

    $price = !empty($productData['discounted_price']) && $productData['discounted_price'] > 0
        ? $productData['discounted_price']
        : $productData['price'];

    $itemSubtotal = $price * $quantity;
    $total += $itemSubtotal;

    $cartItems[] = [
        'id' => $productID,
        'name' => $productData['title'],
        'image' => !empty($productData['image']) ? media_folder . $productData['image'] : '',
        'price' => $price,
        'quantity' => $quantity,
        'subtotal' => $itemSubtotal
    ];
}
$pageCanonical = SITE_URL . '/cart';

include('components/header.php');

$pageHeroTitle = trim((string) ($respage->title ?? 'Cart')) ?: 'Cart';
include __DIR__ . '/components/page-hero.php';
?>

<!-- Cart Page Main Container -->
<div class="bg-secondary-50 py-12 lg:py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Out of Stock Alert -->
        <?php if (!empty($removedItems)): ?>
            <div class="mb-6 bg-primary-50 border border-primary-200 rounded-2xl p-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-primary-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-primary-800">
                            <?= count($removedItems) == 1 ? 'Item Removed from Cart' : 'Items Removed from Cart' ?>
                        </h3>
                        <div class="mt-2 text-sm text-primary-700">
                            <p>The following <?= count($removedItems) == 1 ? 'product is' : 'products are' ?> currently out of stock and <?= count($removedItems) == 1 ? 'has' : 'have' ?> been removed from your cart:</p>
                            <ul class="mt-1 list-disc list-inside">
                                <?php foreach ($removedItems as $itemName): ?>
                                    <li><?= htmlspecialchars($itemName) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <div class="mt-3">
                            <a href="<?= SITE_URL ?>/our-products" class="text-sm font-medium text-primary-800 hover:text-primary-600 underline">
                                Browse other products →
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Cart Content Section -->
        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Cart Items (Left Column) -->
            <div class="w-full lg:w-2/3">
                <!-- Cart Container -->
                <div class="bg-white rounded-3xl shadow-sm ring-1 ring-black/5 overflow-hidden">
                    <!-- Cart Header -->
                    <div class="bg-secondary-50 p-4 border-b border-secondary-200">
                        <div class="grid grid-cols-10 gap-4 text-sm font-semibold text-secondary-600">
                            <div class="col-span-5 sm:col-span-6">Product</div>
                            <div class="col-span-3 sm:col-span-2 text-center">Quantity</div>
                            <div class="col-span-2 text-right">Subtotal</div>
                        </div>
                    </div>

                    <!-- Cart Items -->
                    <div id="cart-body" class="divide-y divide-secondary-100">
                        <?php if (empty($cartItems)): ?>
                            <div class="p-8 text-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                <h3 class="mt-4 text-lg font-medium text-gray-900">Your cart is empty</h3>
                                <p class="mt-2 text-gray-500">Looks like you haven't added any products to your cart yet.</p>
                                <a href="<?= SITE_URL ?>/our-products" class="mt-6 inline-block bg-primary-500 hover:bg-primary-600 text-white font-semibold py-3 px-6 rounded-full transition-colors">
                                    Start Shopping
                                </a>
                            </div>
                        <?php else: ?>
                            <?php foreach ($cartItems as $item): ?>
                                <div class="p-4 sm:p-6 hover:bg-secondary-50 transition-colors cart-item" id="<?= $item['id'] ?>">
                                    <div class="grid grid-cols-10 gap-4 items-center">
                                        <!-- Product Info -->
                                        <div class="col-span-5 sm:col-span-6">
                                            <div class="flex items-center">
                                                <div class="w-20 h-20 bg-secondary-50 rounded-2xl overflow-hidden flex-shrink-0">
                                                    <img src="<?= htmlspecialchars($item['image'], ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8') ?>" class="w-full h-full object-contain p-2">
                                                </div>
                                                <div class="ml-4">
                                                    <h3 class="text-sm font-medium text-gray-900"><?= htmlspecialchars($item['name']) ?></h3>
                                                    <p class="text-sm font-medium text-gray-600 item-price"><?= $getdata->formatPrice($item['price']) ?></p>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Quantity Controls -->
                                        <div class="col-span-3 sm:col-span-2">
                                            <div class="flex items-center justify-center border border-secondary-200 rounded-full overflow-hidden">
                                                <button type="button" class="qtybtn dec px-3 py-1.5 text-secondary-700 hover:bg-primary-50 transition-colors" aria-label="Decrease quantity">
                                                    -
                                                </button>
                                                <input type="text" value="<?= $item['quantity'] ?>" class="w-10 text-center border-0 focus:ring-0" readonly aria-label="Quantity">
                                                <button type="button" class="qtybtn inc px-3 py-1.5 text-secondary-700 hover:bg-primary-50 transition-colors" aria-label="Increase quantity">
                                                    +
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Subtotal -->
                                        <div class="col-span-2 text-right flex flex-col items-end">
                                            <p class="text-sm font-medium text-gray-900 final-item-price"><?= $getdata->formatPrice($item['subtotal']) ?></p>
                                            <button type="button" class="mt-2 text-sm text-secondary-500 hover:text-primary-600 transition-colors flex items-center" onclick="remove_item('<?= htmlspecialchars((string) $item['id'], ENT_QUOTES, 'UTF-8') ?>')">
                                                Remove
                                            </button>
                                        </div>
                                    </div>
                                </div>

                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <!-- Cart Actions -->
                    <div class="p-4 sm:px-6 bg-secondary-50 border-t border-secondary-200 flex flex-wrap gap-4 justify-between items-center">
                        <!-- Continue Shopping -->
                        <a href="<?= SITE_URL ?>/our-products" class="flex items-center text-secondary-700 hover:text-primary-600 transition-colors font-medium">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                            Continue Shopping
                        </a>
                    </div>
                </div>

            </div>

            <!-- Order Summary (Right Column) -->
            <div class="w-full lg:w-1/3">
                <div class="bg-white rounded-3xl shadow-sm ring-1 ring-black/5 overflow-hidden lg:sticky lg:top-28">
                    <!-- Summary Header -->
                    <div class="bg-secondary-50 p-5 border-b border-secondary-200">
                        <h2 class="bebas text-2xl text-secondary-950">Order Summary</h2>
                    </div>

                    <!-- Summary Content -->
                    <div class="p-6">
                        <!-- Summary Rows -->
                        <div class="space-y-4" id="cartSummary" data-flat-rate="<?= $flatRate ?>">
                            <div class="flex justify-between items-center text-secondary-700">
                                <span>Subtotal</span>
                                <span id="cartSubtotal"><?= $getdata->formatPrice($total) ?></span>
                            </div>
                            <fieldset class="flex justify-between gap-4 text-secondary-700">
                                <legend class="sr-only">Shipment</legend>
                                <span>Shipment</span>
                                <div class="space-y-2 text-right">
                                    <label class="flex items-center justify-end gap-2"><input type="radio" name="shipping_method" value="flat_rate" <?= $shippingMethod === 'flat_rate' ? 'checked' : '' ?>> <span>Flat rate: <?= $getdata->formatPrice($flatRate) ?></span></label>
                                    <label class="flex items-center justify-end gap-2"><input type="radio" name="shipping_method" value="pickup" <?= $shippingMethod === 'pickup' ? 'checked' : '' ?>> <span>Local pickup</span></label>
                                </div>
                            </fieldset>
                            <!-- Total -->
                            <div class="flex justify-between items-center">
                                <span class="text-lg font-semibold text-secondary-800">Total</span>
                                <span class="text-xl font-bold text-secondary-950" id="cartTotal"><?= $getdata->formatPrice($total > 0 ? $total + ($shippingMethod === 'flat_rate' ? $flatRate : 0) : 0) ?></span>
                            </div>
                        </div>

                        <!-- Checkout Button -->
                        <a href="<?= SITE_URL ?>/checkout" id="checkoutBtn"
                            class="<?= ($total <= 0) ? "hidden" : "" ?> w-full mt-6 bg-primary-500 hover:bg-primary-600 text-white font-semibold py-3 px-4 rounded-full flex items-center justify-center gap-2 transition-all hover:shadow-lg focus:outline-none focus:ring-4 focus:ring-primary-200" <?= empty($cartItems) ? 'aria-disabled="true"' : '' ?>>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                            </svg>
                            Proceed to Checkout
                        </a>

                        <!-- Secure Payment Message -->
                        <div class="flex items-center justify-center gap-2 mt-4 text-sm text-gray-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                            Secure Checkout
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
    <?php include __DIR__ . '/assets/js/cart.js'; ?>
</script>

<?php include("components/footer.php"); ?>