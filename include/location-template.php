<?php
/** Build replacements for location and category content templates. */
function replaceLocationTemplate(string $content, string $locationTitle, ?array $locationData = null, ?string $categoryTitle = null, bool $escapeHtmlValues = false): string
{
    $locationData = $locationData ?? [];
    $values = [
        '{{location}}' => $locationTitle,
        '{{location.primary_commercial_hub}}' => is_scalar($locationData['primary_commercial_hub'] ?? null)
            ? trim((string) $locationData['primary_commercial_hub'])
            : '',
    ];

    foreach ([
        'major_sub_locations',
        'industrial_hubs',
        'trade_and_logistics_hubs',
    ] as $key) {
        $items = $locationData[$key] ?? [];
        if (!is_array($items)) {
            $items = [];
        }
        $items = array_values(array_filter(array_map(
            static fn($item): string => is_scalar($item) ? trim((string) $item) : '',
            $items
        ), static fn(string $item): bool => $item !== ''));
        $values['{{location.' . $key . '}}'] = implode(', ', $items);
    }

    if ($categoryTitle !== null) {
        $values['catname'] = $categoryTitle;
    }
    if ($escapeHtmlValues) {
        $values = array_map(static fn(string $value): string => htmlspecialchars($value, ENT_QUOTES, 'UTF-8'), $values);
    }
    return str_replace(array_keys($values), array_values($values), $content);
}
