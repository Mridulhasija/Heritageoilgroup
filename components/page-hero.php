<?php
$heroTitle = trim((string) ($pageHeroTitle ?? ''));
$hasHeroImage = !empty($pageHeroImage);
?>
<section class="relative isolate grid min-h-56 place-items-center overflow-hidden bg-gradient-to-r from-primary-50 via-primary-100 to-primary-300 px-6 py-12">
    <?php if ($hasHeroImage): ?>
        <img src="<?= htmlspecialchars((string)$pageHeroImage, ENT_QUOTES, 'UTF-8') ?>" alt="" class="absolute inset-0 h-full w-full object-cover" onerror="this.hidden=true">
        <div class="absolute inset-0 bg-gradient-to-r from-secondary-950/80 via-secondary-950/50 to-secondary-950/20"></div>
    <?php else: ?>
        <div class="absolute -right-24 -top-24 h-80 w-80 rounded-full border border-white/60 shadow-[0_0_0_24px_rgba(255,255,255,.12),0_0_0_50px_rgba(255,255,255,.08)]"></div>
    <?php endif; ?>
    <h1 class="relative z-10 mx-auto w-full max-w-screen-xl text-4xl font-normal tracking-tight <?= $hasHeroImage ? 'text-white' : 'text-secondary-900' ?> sm:text-5xl md:text-6xl">
        <?= htmlspecialchars($heroTitle, ENT_QUOTES, 'UTF-8') ?>
    </h1>
</section>
