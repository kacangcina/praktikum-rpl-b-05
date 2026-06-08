<?php if (isset($component)) { $__componentOriginal5863877a5171c196453bfa0bd807e410 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5863877a5171c196453bfa0bd807e410 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.app','data' => ['title' => ''.e($recipe->title).' - CuBu']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.app'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => ''.e($recipe->title).' - CuBu']); ?>
    <a href="<?php echo e(route('recipes.index')); ?>" class="back-link">Kembali ke beranda</a>

    <article class="recipe-detail">
        <div class="detail-main">
            <div class="detail-media">
                <?php if($recipe->video && auth()->check()): ?>
                    <video controls preload="metadata" poster="<?php echo e($recipe->thumbnail_url); ?>">
                        <source src="<?php echo e($recipe->video->file_url); ?>" type="video/mp4">
                    </video>
                <?php elseif($recipe->video): ?>
                    <div class="video-locked">
                        <i data-lucide="lock"></i>
                        <strong>Video khusus pengguna CuBu</strong>
                        <p>Masuk untuk menonton panduan video resep ini.</p>
                        <a href="<?php echo e(route('login')); ?>" class="button button-primary">Masuk</a>
                    </div>
                <?php elseif($recipe->thumbnail_url): ?>
                    <img src="<?php echo e($recipe->thumbnail_url); ?>" alt="<?php echo e($recipe->title); ?>">
                <?php else: ?>
                    <span class="recipe-placeholder">CuBu</span>
                <?php endif; ?>
            </div>

            <div class="detail-title">
                <div>
                    <span class="eyebrow">Resep <?php echo e(ucfirst($recipe->difficulty)); ?></span>
                    <h1><?php echo e($recipe->title); ?></h1>
                    <p><?php echo e($recipe->description); ?></p>
                </div>
                <div class="detail-actions">
                    <?php if(auth()->guard()->check()): ?>
                        <?php if($isSaved): ?>
                            <form method="POST" action="<?php echo e(route('collections.destroy', $recipe)); ?>">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button class="button button-secondary detail-icon-action" title="Hapus dari koleksi" aria-label="Hapus <?php echo e($recipe->title); ?> dari koleksi">
                                    <i data-lucide="bookmark-check"></i>
                                </button>
                            </form>
                        <?php else: ?>
                            <form method="POST" action="<?php echo e(route('collections.store', $recipe)); ?>">
                                <?php echo csrf_field(); ?>
                                <button class="button button-secondary detail-icon-action" title="Simpan ke koleksi" aria-label="Simpan <?php echo e($recipe->title); ?> ke koleksi">
                                    <i data-lucide="bookmark"></i>
                                </button>
                            </form>
                        <?php endif; ?>

                        <?php if(auth()->user()->canUploadVideos() && auth()->id() === $recipe->user_id && ! $recipe->thumbnail_url): ?>
                            <a href="<?php echo e(route('recipes.video.create', $recipe)); ?>" class="button button-primary">
                                <i data-lucide="video"></i>
                                <?php echo e($recipe->video ? 'Ganti video' : 'Tambah video'); ?>

                            </a>
                        <?php endif; ?>

                        <?php if(auth()->id() === $recipe->user_id): ?>
                            <form method="POST" action="<?php echo e(route('recipes.destroy', $recipe)); ?>" onsubmit="return confirm('Hapus resep <?php echo e(addslashes($recipe->title)); ?>? Resep yang dihapus tidak dapat dikembalikan.')">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button class="button button-danger detail-icon-action" type="submit" title="Hapus resep" aria-label="Hapus resep <?php echo e($recipe->title); ?>">
                                    <i data-lucide="trash-2"></i>
                                </button>
                            </form>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>

            <section class="steps-section">
                <span class="eyebrow">Ikuti urutannya</span>
                <h2>Langkah memasak</h2>
                <ol class="steps-list">
                    <?php $__currentLoopData = $recipe->steps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $step): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li>
                            <span class="step-number"><?php echo e($step->step_number); ?></span>
                            <div class="step-copy">
                                <h3><?php echo e($step->title ?: 'Langkah '.$step->step_number); ?></h3>
                                <p><?php echo e($step->description); ?></p>
                            </div>
                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ol>
            </section>
        </div>

        <aside class="detail-sidebar">
            <section class="creator-panel">
                <div class="avatar"><?php echo e(strtoupper(substr($recipe->creator?->username ?? 'C', 0, 1))); ?></div>
                <div>
                    <span class="eyebrow">Dibuat oleh</span>
                    <h2><?php echo e($recipe->creator?->username ?? $recipe->creator?->name ?? 'Koki CuBu'); ?></h2>
                    <?php if($recipe->creator?->role === 'creator' && $recipe->creator?->is_verified): ?>
                        <span class="verified-badge">Creator terverifikasi</span>
                    <?php else: ?>
                        <span class="member-badge">Anggota CuBu</span>
                    <?php endif; ?>
                </div>
                <div class="stat-grid">
                    <div><strong><?php echo e($recipe->estimated_time); ?></strong><span>Menit</span></div>
                    <div><strong><?php echo e(ucfirst($recipe->difficulty)); ?></strong><span>Kesulitan</span></div>
                </div>
            </section>

            <section class="detail-panel">
                <h2>Alat masak</h2>
                <ul class="clean-list">
                    <?php $__currentLoopData = $recipe->tools; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tool): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($tool->tool_name); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </section>

            <section class="detail-panel">
                <h2>Bahan-bahan</h2>
                <ul class="ingredient-list">
                    <?php $__currentLoopData = $recipe->ingredients; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ingredient): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><span><?php echo e($ingredient->ingredient_name); ?></span><strong><?php echo e($ingredient->quantity); ?></strong></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </section>
        </aside>
    </article>
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
<?php /**PATH C:\xampp\htdocs\praktikum-rpl-b-05\src\CuBu\resources\views/recipes/show.blade.php ENDPATH**/ ?>