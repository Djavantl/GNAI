<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e(config('app.name')) ?></title>
    <?= app(\Illuminate\Foundation\Vite::class)->reactRefresh() ?>
    <?= app(\Illuminate\Foundation\Vite::class)(['resources/react/main.tsx']) ?>
</head>
<body>
    <script data-page="root" type="application/json"><?= json_encode($page, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR) ?></script>
    <div id="root"></div>
</body>
</html>
