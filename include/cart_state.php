<?php

$isLoggedIn = isset($getdata) && $getdata->isLoggedIn();
$flatRate = 0.0;
$shippingMethod = (($_COOKIE['shipping_method'] ?? '') === 'pickup') ? 'pickup' : 'flat_rate';

function heritage_catalog_item(string $productID): ?array
{
    global $getdata;

    $productID = $getdata->sanitizeInput($productID);
    if (!is_string($productID) || $productID === '') {
        return null;
    }

    $product = $getdata->FetchProductData($productID);
    if (!empty($product)) {
        return $product;
    }

    $conn = db();
    $stmt = $conn->prepare('SELECT id, title, image, url, price, discounted_price, status FROM tbl_product_category WHERE id = ? LIMIT 1');
    if (!$stmt) {
        return null;
    }

    $stmt->bind_param('s', $productID);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result ? $result->fetch_assoc() : null;
    $stmt->close();

    return $row ?: null;
}

function heritage_catalog_image(array $productData): string
{
    if (!empty($productData['image'])) {
        return media_folder . $productData['image'];
    }

    $slug = strtolower(trim((string) ($productData['url'] ?? '')));
    $slug = trim((string) preg_replace('/[^a-z0-9-]+/', '-', $slug), '-');
    if ($slug === '') {
        return '';
    }

    $file = dirname(__DIR__) . '/assets/images/categories/' . $slug . '.jpg';
    if (!is_file($file)) {
        return '';
    }

    return SITE_URL . '/assets/images/categories/' . rawurlencode($slug) . '.jpg';
}

function heritage_cart_snapshot(): array
{
    global $getdata;

    $cartData = isset($_COOKIE['cart']) ? json_decode((string) $_COOKIE['cart'], true) : [];
    if (!is_array($cartData)) {
        $cartData = [];
    }

    $items = [];
    $removed = [];
    $kept = [];
    $total = 0.0;

    foreach ($cartData as $item) {
        if (!is_array($item)) {
            continue;
        }

        $productID = $getdata->sanitizeInput($item['productID'] ?? '');
        if (!is_string($productID) || $productID === '') {
            continue;
        }

        $quantity = filter_var(
            ($item['quantity'] ?? 1),
            FILTER_VALIDATE_INT,
            ['options' => ['min_range' => 1, 'max_range' => 999]]
        ) ?: 1;

        $productData = heritage_catalog_item($productID);
        if (empty($productData)) {
            continue;
        }

        if (isset($productData['status']) && (int) $productData['status'] === 0) {
            $removed[] = (string) ($productData['title'] ?? 'Item');
            continue;
        }

        $price = (!empty($productData['discounted_price']) && (float) $productData['discounted_price'] > 0)
            ? (float) $productData['discounted_price']
            : (float) ($productData['price'] ?? 0);
        $subtotal = $price * $quantity;
        $total += $subtotal;

        $items[] = [
            'id' => $productID,
            'name' => (string) ($productData['title'] ?? ''),
            'image' => heritage_catalog_image($productData),
            'price' => $price,
            'quantity' => $quantity,
            'subtotal' => $subtotal,
        ];
        $kept[] = ['productID' => $productID, 'quantity' => $quantity];
    }

    return [
        'items' => $items,
        'removed' => $removed,
        'kept' => $kept,
        'total' => $total,
    ];
}

function heritage_sync_cart_cookie(array $kept): void
{
    $payload = json_encode(array_values($kept), JSON_UNESCAPED_SLASHES);
    if ($payload === false) {
        return;
    }

    setcookie('cart', $payload, [
        'expires' => time() + (86400 * 30),
        'path' => '/',
        'samesite' => 'Lax',
    ]);
    $_COOKIE['cart'] = $payload;
}
