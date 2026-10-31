<?php
    $seo = is_array($page['props']['seo'] ?? null) ? $page['props']['seo'] : null;
    $seoTitle = filled($seo['title'] ?? null) ? (string) $seo['title'] : config('app.name');
    $seoDescription = filled($seo['description'] ?? null) ? (string) $seo['description'] : null;
    $seoImage = filled($seo['image'] ?? null) ? (string) $seo['image'] : null;
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($seoTitle) ?></title>
<?php if ($seo !== null) { ?>
    <?php if ($seoDescription !== null) { ?>
    <meta name="description" content="<?= e($seoDescription) ?>" inertia="description">
    <meta property="og:description" content="<?= e($seoDescription) ?>" inertia="og:description">
    <?php } ?>
    <meta property="og:title" content="<?= e($seoTitle) ?>" inertia="og:title">
    <meta property="og:type" content="website" inertia="og:type">
    <meta property="og:url" content="<?= e(url()->current()) ?>" inertia="og:url">
    <?php if ($seoImage !== null) { ?>
    <meta property="og:image" content="<?= e($seoImage) ?>" inertia="og:image">
    <?php } ?>
    <link rel="icon" type="image/png" href="<?= e(asset('images/favicon.png')) ?>">
<?php } ?>
    <?= app(\Illuminate\Foundation\Vite::class)->reactRefresh() ?>
    <?= app(\Illuminate\Foundation\Vite::class)(['resources/react/main.tsx']) ?>
</head>
<body>
    <script data-page="root" type="application/json"><?= json_encode($page, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR) ?></script>
    <div id="root"></div>
</body>
</html>
