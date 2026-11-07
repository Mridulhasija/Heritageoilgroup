<?php
session_start();

require 'vendor/autoload.php';

use Gregwar\Captcha\CaptchaBuilder;

// Handle captcha generation
if (isset($_GET['generate'])) {
    // Generate a new captcha
    $builder = new CaptchaBuilder();

    // Generate a new captcha with improved readability
    $builder = new CaptchaBuilder();

    $builder->setDistortion(false);           // Disable distortion
    $builder->setMaxBehindLines(2);           // Minimal background lines
    $builder->setMaxFrontLines(0);            // No foreground lines
    $builder->setMaxAngle(3);                  // Very minimal text rotation
    $builder->setInterpolation(true);          // Smoother text rendering
    $builder->setBackgroundColor(255, 255, 255); // White background

    // Build with larger dimensions for better readability (width, height, font, fingerprint)
    $builder->build(150, 50);

    // Store the phrase in session with a unique key for each form
    $formId = isset($_GET['form_id']) ? $_GET['form_id'] : 'default';
    $_SESSION['captcha_' . $formId] = $builder->getPhrase();

    // Set headers for image output
    header('Content-Type: image/jpeg');
    header('Cache-Control: no-cache, no-store, must-revalidate');
    header('Pragma: no-cache');
    header('Expires: 0');

    // Output the image
    $builder->output();
    exit;
}

// Handle captcha validation
if (isset($_GET['validate'])) {
    header('Content-Type: application/json');

    $formId = isset($_GET['form_id']) ? $_GET['form_id'] : 'default';
    $userInput = isset($_GET['code']) ? strtolower(trim($_GET['code'])) : '';
    $sessionKey = 'captcha_' . $formId;

    $isValid = false;
    if (isset($_SESSION[$sessionKey]) && !empty($userInput)) {
        $captchaPhrase = strtolower($_SESSION[$sessionKey]);
        $isValid = ($userInput === $captchaPhrase);

        // Clear the captcha from session after validation attempt
        unset($_SESSION[$sessionKey]);
    }

    echo json_encode(['valid' => $isValid]);
    exit;
}

// Default: return 404
http_response_code(404);
exit;
