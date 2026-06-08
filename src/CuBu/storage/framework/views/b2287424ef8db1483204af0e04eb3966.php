<?php if (isset($component)) { $__componentOriginal5863877a5171c196453bfa0bd807e410 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5863877a5171c196453bfa0bd807e410 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.app','data' => ['title' => 'Koleksi Saya - CuBu']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.app'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Koleksi Saya - CuBu']); ?>
    <section class="section-heading">
        <div>
            <span class="eyebrow">Resep tersimpan</span>
            <h1>Koleksi</h1>
            <p>Semua resep yang ingin kamu masak lagi tersimpan di satu tempat.</p>
        </div>
    </section>

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
                    <p class="recipe-author">Oleh <?php echo e($recipe->creator?->username); ?></p>
                    <h3><a href="<?php echo e(route('recipes.show', $recipe)); ?>"><?php echo e($recipe->title); ?></a></h3>
                    <p><?php echo e(\Illuminate\Support\Str::limit($recipe->description, 92)); ?></p>
                    <form method="POST" action="<?php echo e(route('collections.destroy', $recipe)); ?>">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('DELETE'); ?>
                        <button class="button button-ghost button-small"><i data-lucide="bookmark-x"></i>Hapus</button>
                    </form>
                </div>
            </article>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="empty-state full-grid">
                <i data-lucide="bookmark"></i>
                <h2>Koleksi masih kosong</h2>
                <p>Simpan resep dari halaman detail agar mudah ditemukan kembali.</p>
            </div>
        <?php endif; ?>
    </section>

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
<?php /**PATH C:\xampp\htdocs\praktikum-rpl-b-05\src\CuBu\resources\views\collections\index.blade.php ENDPATH**/ ?>