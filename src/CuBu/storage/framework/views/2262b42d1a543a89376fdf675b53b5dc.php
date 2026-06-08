<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo e($title ?? 'CuBu'); ?></title>
    <link rel="icon" href="<?php echo e(asset('images/cubu-logo.svg')); ?>" type="image/svg+xml">
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
</head>
<body>
    <header class="site-header">
        <div class="site-nav">
            <a href="<?php echo e(route('recipes.index')); ?>" class="brand" aria-label="Beranda CuBu">
                <img src="<?php echo e(asset('images/cubu-logo.svg')); ?>" class="brand-mark" alt="">
                <span>CuBu</span>
            </a>

            <nav class="nav-links" aria-label="Navigasi utama">
                <a href="<?php echo e(route('recipes.index')); ?>" class="<?php echo e(request()->routeIs('recipes.*') && ! request()->routeIs('recipes.create') ? 'active' : ''); ?>">
                    <i data-lucide="house"></i>
                    Beranda
                </a>
                <?php if(auth()->guard()->check()): ?>
                    <a href="<?php echo e(route('collections.index')); ?>" class="<?php echo e(request()->routeIs('collections.*') ? 'active' : ''); ?>">
                        <i data-lucide="bookmark"></i>
                        Koleksi 
                    </a>
                    <?php if(auth()->user()->isAdmin()): ?>
                        <a href="<?php echo e(route('admin.creator-verifications.index')); ?>" class="<?php echo e(request()->routeIs('admin.*') ? 'active' : ''); ?>">
                            <i data-lucide="shield-check"></i>
                            Admin
                        </a>
                    <?php endif; ?>
                <?php endif; ?>
            </nav>

            <form action="<?php echo e(route('recipes.index')); ?>" method="GET" class="nav-search">
                <label class="sr-only" for="global-search">Cari resep</label>
                <input id="global-search" name="q" value="<?php echo e(request('q')); ?>" placeholder="Cari resep atau bahan...">
                <button aria-label="Cari"><i data-lucide="search"></i></button>
            </form>

            <div class="nav-actions">
                <?php if(auth()->guard()->check()): ?>
                    <a href="<?php echo e(route('profile.show', auth()->user())); ?>" class="nav-profile <?php echo e(request()->routeIs('profile.*') ? 'active' : ''); ?>" aria-label="Profil <?php echo e(auth()->user()->username); ?>">
                        <span class="profile-avatar profile-avatar-nav">
                            <?php if(auth()->user()->avatar_url): ?>
                                <img src="<?php echo e(auth()->user()->avatar_url); ?>" alt="">
                            <?php else: ?>
                                <span><?php echo e(auth()->user()->initials); ?></span>
                            <?php endif; ?>
                        </span>
                        <span class="user-chip"><?php echo e(auth()->user()->username); ?></span>
                    </a>
                    <form method="POST" action="<?php echo e(route('logout')); ?>">
                        <?php echo csrf_field(); ?>
                        <button class="button button-ghost button-small" title="Keluar">
                            <i data-lucide="log-out"></i>
                            <span class="desktop-label">Keluar</span>
                        </button>
                    </form>
                <?php else: ?>
                    <a href="<?php echo e(route('login')); ?>" class="button button-ghost button-small">Masuk</a>
                    <a href="<?php echo e(route('register')); ?>" class="button button-primary button-small">Daftar</a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <main class="page-shell">
        <?php if(session('status')): ?>
            <div class="flash-message" role="status">
                <?php echo e(session('status')); ?>

            </div>
        <?php endif; ?>

        <?php echo e($slot); ?>

    </main>

    <footer class="site-footer">
        <span class="brand brand-footer"><img src="<?php echo e(asset('images/cubu-logo.svg')); ?>" class="brand-mark" alt=""><span>CuBu</span></span>
        <p>Temukan, masak, dan bagikan resep favoritmu.</p>
    </footer>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\praktikum-rpl-b-05\src\CuBu\resources\views/components/layouts/app.blade.php ENDPATH**/ ?>