<?php if (isset($component)) { $__componentOriginal5863877a5171c196453bfa0bd807e410 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5863877a5171c196453bfa0bd807e410 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.app','data' => ['title' => 'Resep - CuBu']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.app'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Resep - CuBu']); ?>
    <?php if(! $search): ?>
        <section class="home-hero featured-hero">
            <?php if($featuredRecipe): ?>
                <div class="hero-content">
                    <span class="eyebrow">Highlighted recipe</span>
                    <p class="featured-author">
                        Oleh <?php echo e($featuredRecipe->creator?->username ?? $featuredRecipe->creator?->name ?? 'Koki CuBu'); ?>

                    </p>
                    <h1><?php echo e($featuredRecipe->title); ?></h1>
                    <p><?php echo e(\Illuminate\Support\Str::limit($featuredRecipe->description, 180)); ?></p>
                    <div class="featured-meta">
                        <span><?php echo e($featuredRecipe->estimated_time); ?> menit</span>
                        <span><?php echo e(ucfirst($featuredRecipe->difficulty)); ?></span>
                        <span><?php echo e($featuredRecipe->ingredients->count()); ?> bahan</span>
                        <?php if($featuredRecipe->video): ?>
                            <span><i data-lucide="play"></i> Video</span>
                        <?php endif; ?>
                    </div>
                    <a href="<?php echo e(route('recipes.show', $featuredRecipe)); ?>" class="button button-primary">
                        Lihat resep
                        <i data-lucide="arrow-right"></i>
                    </a>
                </div>
                <a href="<?php echo e(route('recipes.show', $featuredRecipe)); ?>" class="hero-featured-media">
                    <?php if($featuredRecipe->thumbnail_url): ?>
                        <img src="<?php echo e($featuredRecipe->thumbnail_url); ?>" alt="<?php echo e($featuredRecipe->title); ?>">
                    <?php else: ?>
                        <span class="recipe-placeholder">CuBu</span>
                    <?php endif; ?>
                    <?php if($featuredRecipe->video): ?>
                        <span class="featured-play"><i data-lucide="play"></i></span>
                    <?php endif; ?>
                </a>
            <?php else: ?>
                <div class="hero-content">
                    <span class="eyebrow">Highlighted recipe</span>
                    <h1>Resep pilihan akan tampil di sini.</h1>
                    <p>Publikasikan resep pertama untuk menjadikannya sorotan di beranda CuBu.</p>
                </div>
                <div class="hero-featured-media"><span class="recipe-placeholder">CuBu</span></div>
            <?php endif; ?>
        </section>
    <?php endif; ?>

    <section class="ingredient-strip">
        <div>
            <span class="eyebrow">Cari dari bahan</span>
            <h2>Punya bahan apa di dapur?</h2>
        </div>
        <div class="ingredient-chips">
            <?php $__currentLoopData = ['Ayam', 'Daging sapi', 'Telur', 'Tahu', 'Tempe', 'Cabai', 'Bawang merah']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ingredient): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e(route('recipes.index', ['q' => $ingredient])); ?>"><?php echo e($ingredient); ?></a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </section>

    <section class="section-heading">
        <div>
            <span class="eyebrow"><?php echo e($search ? 'Hasil pencarian' : 'Rekomendasi terkini'); ?></span>
            <h2><?php echo e($search ? 'Resep untuk "'.$search.'"' : 'Pilihan untuk dimasak hari ini'); ?></h2>
        </div>
        <?php if(auth()->guard()->check()): ?>
            <?php if(auth()->user()->canPublishRecipes()): ?>
                <a href="<?php echo e(route('recipes.create')); ?>" class="button button-secondary">Buat resep</a>
            <?php endif; ?>
        <?php endif; ?>
    </section>

    <?php if($search && $recipes->isEmpty()): ?>
        <div class="empty-state">
            <h2>Tidak ada resep yang ditemukan untuk "<?php echo e($search); ?>"</h2>
            <p>Coba kata kunci lain. Sementara itu, ini beberapa resep terbaru yang bisa kamu coba.</p>
        </div>
    <?php endif; ?>

    <section class="recipe-grid">
        <?php $__empty_1 = true; $__currentLoopData = $recipes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $recipe): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <article class="recipe-card">
                <a href="<?php echo e(route('recipes.show', $recipe)); ?>" class="recipe-image">
                    <?php if($recipe->thumbnail_url): ?>
                        <img src="<?php echo e($recipe->thumbnail_url); ?>" alt="<?php echo e($recipe->title); ?>">
                    <?php else: ?>
                        <span class="recipe-placeholder">CuBu</span>
                    <?php endif; ?>
                    <?php if($recipe->video): ?>
                        <span class="video-badge"><i data-lucide="play"></i>Video</span>
                    <?php else: ?>
                        <span class="photo-badge"><i data-lucide="image"></i>Foto</span>
                    <?php endif; ?>
                    <span class="difficulty difficulty-<?php echo e($recipe->difficulty); ?>"><?php echo e(ucfirst($recipe->difficulty)); ?></span>
                </a>
                <div class="recipe-card-body">
                    <p class="recipe-author">Oleh <?php echo e($recipe->creator?->username ?? $recipe->creator?->name ?? 'Koki CuBu'); ?></p>
                    <h3><a href="<?php echo e(route('recipes.show', $recipe)); ?>"><?php echo e($recipe->title); ?></a></h3>
                    <p><?php echo e(\Illuminate\Support\Str::limit($recipe->description, 92)); ?></p>
                    <div class="recipe-meta">
                        <span><?php echo e($recipe->estimated_time); ?> menit</span>
                        <span><?php echo e($recipe->ingredients->count()); ?> bahan</span>
                    </div>
                </div>
            </article>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <?php if (! ($search)): ?>
                <div class="empty-state full-grid">
                    <h2>Belum ada resep</h2>
                    <p>Jadilah yang pertama membagikan resep andalanmu di CuBu.</p>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </section>

    <?php if($suggestions->isNotEmpty()): ?>
        <section class="recipe-grid suggestions">
            <?php $__currentLoopData = $suggestions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $recipe): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <article class="recipe-card compact-card">
                    <a href="<?php echo e(route('recipes.show', $recipe)); ?>" class="recipe-image">
                        <?php if($recipe->thumbnail_url): ?>
                            <img src="<?php echo e($recipe->thumbnail_url); ?>" alt="<?php echo e($recipe->title); ?>">
                        <?php else: ?>
                            <span class="recipe-placeholder">CuBu</span>
                        <?php endif; ?>
                        <?php if($recipe->video): ?>
                            <span class="video-badge"><i data-lucide="play"></i>Video</span>
                        <?php else: ?>
                            <span class="photo-badge"><i data-lucide="image"></i>Foto</span>
                        <?php endif; ?>
                    </a>
                    <div class="recipe-card-body">
                        <p class="recipe-author">Saran resep</p>
                        <h3><a href="<?php echo e(route('recipes.show', $recipe)); ?>"><?php echo e($recipe->title); ?></a></h3>
                    </div>
                </article>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </section>
    <?php endif; ?>

    <div class="pagination-wrap"><?php echo e($recipes->links()); ?></div>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal5863877a5171c196453bfa0bd807e410)): ?>
<?php $attributes = $__attributesOriginal5863877a5171c196453bfa0bd807e410; ?>
<?php unset($__attributesOriginal5863877a5171c196453bfa0bd807e410); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal5863877a5171c196453bfa0bd807e410)): ?>
<?php $component = $__componentOriginal5863877a5171c196453bfa0bd807e410; ?>
<?php unset($__componentOriginal5863877a5171c196453bfa0bd807e410); ?>
<?php endif; ?>
<?php /**PATH C:\xampp\htdocs\praktikum-rpl-b-05\src\CuBu\resources\views/recipes/index.blade.php ENDPATH**/ ?>