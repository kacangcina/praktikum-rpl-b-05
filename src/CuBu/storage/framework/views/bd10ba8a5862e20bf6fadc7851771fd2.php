<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>CuBu</title>
    <link rel="icon" href="<?php echo e(asset('images/cubu-logo.svg')); ?>" type="image/svg+xml">
    <?php echo app('Illuminate\Foundation\Vite')->reactRefresh(); ?>
    <?php echo app('Illuminate\Foundation\Vite')('resources/js/react/main.jsx'); ?>
</head>
<body>
    <div id="root"></div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\praktikum-rpl-b-05\src\CuBu\resources\views/react.blade.php ENDPATH**/ ?>