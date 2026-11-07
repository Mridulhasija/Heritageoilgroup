<?php

trait GetdataSanitizationTrait
{
    public function cleanTextLine($data): string
    {
        $text = (string) $data;
        $text = str_replace("\0", '', $text);
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = strip_tags($text);
        $text = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $text);
        $text = trim($text);
        $text = preg_replace('/\s+/u', ' ', $text);
        return $text;
    }

    public function cleanTextArea($data): string
    {
        $text = (string) $data;
        $text = str_replace("\0", '', $text);
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = strip_tags($text);
        $text = str_replace(["\r\n", "\r"], "\n", $text);
        $text = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $text);
        $text = trim($text);
        $text = preg_replace("/\n{3,}/", "\n\n", $text);
        return $text;
    }

    public function decodeStoredText($data, bool $singleLine = true): string
    {
        $text = $this->cleanTextArea($data);
        if ($singleLine) {
            $text = str_replace("\n", ' ', $text);
            $text = preg_replace('/\s+/u', ' ', $text);
            $text = trim($text);
        }
        return $text;
    }

    public function uuidv4()
    {
        $data = random_bytes(16);

        $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    public function sanitizeInput($data)
    {
        $strippedData = strip_tags($data);
        $trimmedData = trim($strippedData);
        $sanitizedData = filter_var($trimmedData, FILTER_SANITIZE_SPECIAL_CHARS);
        $safeData = htmlspecialchars($sanitizedData, ENT_QUOTES, 'UTF-8');
        return $safeData;
    }

    public function sanitizeEmail($data)
    {
        $validatedEmail = filter_var($data, FILTER_VALIDATE_EMAIL);
        if ($validatedEmail === false) {
            return false;
        }
        return $this->sanitizeInput($validatedEmail);
    }

    public function sanitizePhoneNumber($data)
    {
        // Remove non-numeric characters and leading/trailing whitespace
        $sanitizedData = trim(preg_replace('/[^0-9]+/', '', $data));

        // Check if the sanitized data has at least 10 digits
        if (strlen($sanitizedData) >= 10) {
            return $sanitizedData;
        }
        return false;
    }

    public function isSpamMessage(string $message): bool
    {
        $message = trim($message);

        // Normalize excessive whitespace
        $message = preg_replace('/\s+/', ' ', $message);

        // List of spam keyword groups
        $keywordPatterns = [
            // Finance, crypto, fake services
            '/\b(free money|loan approval|instant loan|make money|work from home|forex|binary options|crypto|bitcoin|usdt|investment plan)\b/i',

            // Adult & explicit spam
            '/\b(viagra|cialis|sex|escort|porn|xxx|adult|dating site)\b/i',

            // Marketing, SEO, promotions
            '/\b(seo|backlinks?|newsletter|traffic boost|digital marketing|promotion service)\b/i',

            // Fake urgency
            '/\b(click here|act now|limited offer|urgent reply|guaranteed)\b/i',

            // Common spam patterns & phishing
            '/\b(win|winner|prize|reward|lottery|jackpot)\b/i',
        ];

        // Suspicious URL patterns
        $urlPattern = '/https?:\/\/[^\s]+/i';

        // Repeated characters or gibberish
        $gibberishPatterns = [
            '/([A-Za-z0-9])\1{4,}/',     // "aaaaa" or "11111"
            '/[\!\?\$]{4,}/',            // "!!!!", "$$$$" etc
            '/[^\x00-\x7F]{10,}/',       // Too much non-ASCII (foreign spam)
        ];

        // Emoji spam (very common in bot messages)
        $emojiPattern = '/[\x{1F600}-\x{1FAFF}]{5,}/u';

        // Combine all patterns
        $patterns = array_merge($keywordPatterns, $gibberishPatterns, [
            $urlPattern,
            $emojiPattern
        ]);

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $message)) {
                return true;
            }
        }

        return false;
    }
}
