<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/include/header_config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json; charset=utf-8');

    $captchaValid = false;
    $formId = isset($_POST['captcha_form_id']) ? $_POST['captcha_form_id'] : 'default';
    $userInput = isset($_POST['captcha_code']) ? strtolower(trim($_POST['captcha_code'])) : '';
    $sessionKey = 'captcha_' . $formId;
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    
    if (!empty($userInput) && isset($_SESSION[$sessionKey])) {
        $captchaPhrase = strtolower($_SESSION[$sessionKey]);
        $captchaValid = ($userInput === $captchaPhrase);
        // Only clear session after successful validation
        if ($captchaValid) {
            unset($_SESSION[$sessionKey]);
        }
    }

    if (!$captchaValid) {
        echo json_encode(['success' => false, 'message' => 'Invalid verification code. Please try again.']);
        exit;
    }

    $fields = [
        'name' => ['max' => 100, 'type' => 'text'],
        'phone' => ['max' => 20, 'type' => 'phone'],
        'email' => ['max' => 150, 'type' => 'email'],
        'location' => ['max' => 120, 'type' => 'text'],
        'message' => ['max' => 1000, 'type' => 'text'],
    ];

    $optionalFields = [];

    $sanitizedData = [];
    $errMsg = '';

    foreach ($fields as $fieldName => $fieldConfig) {
        if (!isset($_POST[$fieldName]) || empty(trim($_POST[$fieldName]))) {
            $errMsg = ucfirst($fieldName) . ' is required.';
            break;
        }

        $rawValue = $_POST[$fieldName];
        $sanitizedValue = false;
        
        switch ($fieldConfig['type']) {
            case 'email':
                $sanitizedValue = $getdata->sanitizeEmail($rawValue);
                if ($sanitizedValue === false) {
                    $errMsg = 'Invalid email format.';
                    break 2;
                }
                break;
            case 'phone':
                $sanitizedValue = $getdata->sanitizePhoneNumber($rawValue);
                if ($sanitizedValue === false) {
                    $errMsg = 'Invalid phone number format.';
                    break 2;
                }
                break;
            case 'text':
            default:
                if ($fieldName === 'message') {
                    $sanitizedValue = $getdata->cleanTextArea($rawValue);
                } else {
                    $sanitizedValue = $getdata->cleanTextLine($rawValue);
                }
                break;
        }

        $sanitizedData[$fieldName] = $sanitizedValue;
    }

    foreach ($optionalFields as $fieldName => $fieldConfig) {
        if (isset($_POST[$fieldName]) && !empty(trim($_POST[$fieldName]))) {
            $rawValue = $_POST[$fieldName];
            $sanitizedValue = $getdata->sanitizeInput($rawValue);
            if (mb_strlen($sanitizedValue) <= $fieldConfig['max']) {
                $sanitizedData[$fieldName] = $sanitizedValue;
            }
        }
    }

    if (empty($errMsg)) {
        $linkPattern = '/(https?:\/\/|www\.)/i';

        foreach ($fields as $fieldName => $fieldConfig) {
            $value = $sanitizedData[$fieldName];

            if (preg_match($linkPattern, $value)) {
                $errMsg = ucfirst($fieldName) . ' cannot contain links.';
                break;
            }

            if (mb_strlen($value) > $fieldConfig['max']) {
                $errMsg = ucfirst($fieldName) . ' exceeds the allowed length of ' . $fieldConfig['max'] . ' characters.';
                break;
            }

            if ($getdata->isSpamMessage($value)) {
                $errMsg = ucfirst($fieldName) . ' contains spam content.';
                break;
            }
        }
    }

    if (!empty($errMsg)) {
        echo json_encode(['success' => false, 'message' => $errMsg]);
        exit;
    }

    $name = $sanitizedData['name'];
    $phone = $sanitizedData['phone'];
    $email = $sanitizedData['email'];
    $location = $sanitizedData['location'];
    $message = $sanitizedData['message'];
    $state = isset($sanitizedData['state']) ? $sanitizedData['state'] : '';
    $product_title = isset($sanitizedData['product_title']) ? $sanitizedData['product_title'] : '';
    $textable = isset($sanitizedData['textable']) ? $sanitizedData['textable'] : '';

    $stmt = $conn->prepare("INSERT INTO tbl_enquiry (name, phone, email, location, message) VALUES (?, ?, ?, ?, ?)");
    if (!$stmt) {
        error_log("Database Error: " . $conn->error);
        echo json_encode(['success' => false, 'message' => 'An error occurred. Please try again later.']);
        exit;
    }

    $stmt->bind_param("sssss", $name, $phone, $email, $location, $message);
    if (!$stmt->execute()) {
        error_log("Database Execution Error: " . $stmt->error);
        $stmt->close();
        echo json_encode(['success' => false, 'message' => 'An error occurred. Please try again later.']);
        exit;
    }
    $stmt->close();

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = MAIL_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = MAIL_USER;
        $mail->Password = MAIL_PASSWORD;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = MAIL_PORT;

        $mail->setFrom(MAIL_USER, $site_name);
        $mail->addReplyTo($email, $name);
        if (!empty($configData['email_1'])) {
            $mail->addAddress($configData['email_1']);
        }
        $mail->addBCC('behoof.commercial@gmail.com');

        $safeName = htmlspecialchars($name, ENT_QUOTES, 'UTF-8');
        $safeEmail = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');
        $safePhone = htmlspecialchars($phone, ENT_QUOTES, 'UTF-8');
        $safeLocation = htmlspecialchars($location, ENT_QUOTES, 'UTF-8');
        $safeTextable = htmlspecialchars((string) $textable, ENT_QUOTES, 'UTF-8');
        $safeState = htmlspecialchars((string) $state, ENT_QUOTES, 'UTF-8');
        $safeProductTitle = htmlspecialchars((string) $product_title, ENT_QUOTES, 'UTF-8');
        $safeMessage = nl2br(htmlspecialchars($message, ENT_QUOTES, 'UTF-8'), false);

        $mail->isHTML(true);
        $mail->Subject = "Enquiry for $site_name";
        $mail->Body = "
            <strong>Name:</strong> {$safeName} <br>
            <strong>Email:</strong> {$safeEmail} <br>
            <strong>Phone:</strong> {$safePhone} <br>" .
            (empty($textable) ? "" : "<strong>Is this number textable?:</strong> {$safeTextable} <br>") .
            "<strong>Country:</strong> {$safeLocation} <br>" .
            (empty($state) ? "" : "<strong>City/State:</strong> {$safeState} <br>") .
            (empty($product_title) ? "" : "<strong>Product Title:</strong> {$safeProductTitle} <br>") .
            "<strong>Message:</strong><br>{$safeMessage}<br>";

        if (!$mail->send()) {
            error_log("Mailer Error: " . $mail->ErrorInfo);
        }
    } catch (Exception $e) {
        error_log($e->getMessage());
    }

    echo json_encode(['success' => true, 'message' => 'Thank you for your enquiry. Our team will contact you soon.']);
    exit;
}

$pageCanonical = SITE_URL . '/contact';
include('components/header.php');
$pageHeroTitle = trim((string) ($respage->title ?? 'Contact Us')) ?: 'Contact Us';
$pageHeroImage = SITE_URL . '/assets/images/contact-img';
include __DIR__ . '/components/page-hero.php';
?>
    <style>
        main > section[aria-labelledby="oil-contact-title"] {
            background-color: #f5f0e4;
            color: #29251e;
        }

        main > section[aria-labelledby="oil-contact-title"] .text-white {
            color: #29251e;
        }

        main > section[aria-labelledby="oil-contact-title"] .text-white\/70 {
            color: #5f584b;
        }

        main > section[aria-labelledby="oil-contact-title"] .text-white\/60 {
            color: #6e6659;
        }

        main > section[aria-labelledby="oil-contact-title"] .border-white\/15 {
            border-color: rgb(41 37 30 / 18%);
        }
    </style>
    <?php $contactHeading = 'We’d love to hear from you'; include __DIR__ . '/components/contact-form.php'; ?>

<main>
    <section class="bg-primary-50">
        <div class="mx-auto grid w-full max-w-screen-xl gap-12 px-6 py-20 sm:px-10 md:grid-cols-[.8fr_1.2fr] md:gap-20 md:py-28 lg:px-16">
            <div>
                <span class="text-xs font-semibold uppercase tracking-[.18em] text-primary-700">Goodness at every step</span>
                <h2 class="mt-4 text-4xl font-normal leading-tight tracking-tight text-secondary-900 md:text-6xl">Why choose us</h2>
                <p class="mt-5 max-w-sm leading-8 text-secondary-600">Quality is a practice, built into every decision from origin to delivery.</p>
            </div>
            <div class="border-t border-secondary-900/15">
                <article class="grid grid-cols-[3rem_1fr] gap-5 border-b border-secondary-900/15 py-6"><span class="grid h-11 w-11 place-items-center rounded-full border border-primary-600/50 bg-white/60 text-xl text-primary-700" aria-hidden="true">✳</span><div><h3 class="font-semibold text-secondary-900">Quality in every batch</h3><p class="mt-2 leading-7 text-secondary-600">Careful checks at each stage help keep the colour, flavour, and performance consistent from delivery to delivery.</p></div></article>
                <article class="grid grid-cols-[3rem_1fr] gap-5 border-b border-secondary-900/15 py-6"><span class="grid h-11 w-11 place-items-center rounded-full border border-primary-600/50 bg-white/60 text-xl text-primary-700" aria-hidden="true">⌁</span><div><h3 class="font-semibold text-secondary-900">Global sourcing insight</h3><p class="mt-2 leading-7 text-secondary-600">We follow harvests, origins, and market needs across regions to make informed choices about the oils we supply.</p></div></article>
                <article class="grid grid-cols-[3rem_1fr] gap-5 border-b border-secondary-900/15 py-6"><span class="grid h-11 w-11 place-items-center rounded-full border border-primary-600/50 bg-white/60 text-xl text-primary-700" aria-hidden="true">◎</span><div><h3 class="font-semibold text-secondary-900">Trusted producer relationships</h3><p class="mt-2 leading-7 text-secondary-600">Close partnerships support responsible purchasing, clear communication, and dependable product availability.</p></div></article>
                <article class="grid grid-cols-[3rem_1fr] gap-5 border-b border-secondary-900/15 py-6"><span class="grid h-11 w-11 place-items-center rounded-full border border-primary-600/50 bg-white/60 text-xl text-primary-700" aria-hidden="true">◌</span><div><h3 class="font-semibold text-secondary-900">Supply you can plan around</h3><p class="mt-2 leading-7 text-secondary-600">Responsive service and reliable delivery help kitchens, retailers, and food makers keep work moving.</p></div></article>
            </div>
        </div>
    </section>

    <section class="mx-auto grid w-full max-w-screen-xl gap-10 px-6 py-20 sm:px-10 md:grid-cols-[.8fr_1.2fr] md:gap-20 md:py-28 lg:px-16" aria-labelledby="order-support-title">
        <div>
            <span class="text-xs font-semibold uppercase tracking-[.18em] text-primary-700">Here when you need us</span>
            <h2 id="order-support-title" class="mt-4 text-4xl font-normal leading-tight tracking-tight text-secondary-900 md:text-5xl">A straightforward way to sort things out</h2>
            <p class="mt-5 max-w-md leading-8 text-secondary-600">Have a question about an order, or need to discuss a product replacement or exchange? Speak with our team and we’ll help you understand the available next steps.</p>
            <?php if (!empty($configData['phone_1'])): ?>
                <a href="tel:<?= htmlspecialchars(preg_replace('/[^\d+]/', '', (string)$configData['phone_1']), ENT_QUOTES, 'UTF-8') ?>" class="mt-7 inline-flex items-center gap-3 border-b border-primary-600 pb-2 font-semibold text-secondary-900 transition-colors hover:text-primary-700">
                    Call <?= htmlspecialchars((string)$configData['phone_1'], ENT_QUOTES, 'UTF-8') ?> <span aria-hidden="true">↗</span>
                </a>
            <?php endif; ?>
        </div>
        <div class="grid gap-0 border-t border-secondary-900/15">
            <article class="border-b border-secondary-900/15 py-6">
                <h3 class="font-semibold text-secondary-900">Get help choosing the right oil</h3>
                <p class="mt-2 leading-7 text-secondary-600">Tell us how you plan to use it and our team can help clarify product details and available options.</p>
            </article>
            <article class="border-b border-secondary-900/15 py-6">
                <h3 class="font-semibold text-secondary-900">Talk through an order concern</h3>
                <p class="mt-2 leading-7 text-secondary-600">Keep your order details handy so we can look into a delivery or product issue with you.</p>
            </article>
            <article class="border-b border-secondary-900/15 py-6">
                <h3 class="font-semibold text-secondary-900">Discuss a replacement or exchange</h3>
                <p class="mt-2 leading-7 text-secondary-600">If something isn’t right, contact us with the product and order details. We’ll review the situation and explain what can be arranged.</p>
            </article>
        </div>
    </section>

</main>
<?php include('components/footer.php'); ?>
