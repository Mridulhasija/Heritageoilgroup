<?php
$tailwind_theme = json_decode(
    file_get_contents(__DIR__ . '/../assets/tailwind.theme.json'),
    true,
    512,
    JSON_THROW_ON_ERROR
);
?>
<script>
    tailwind.config = <?= json_encode($tailwind_theme, JSON_THROW_ON_ERROR) ?>;
</script>
