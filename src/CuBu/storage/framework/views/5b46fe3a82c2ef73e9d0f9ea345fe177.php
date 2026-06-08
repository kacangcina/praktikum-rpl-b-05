<?php if (isset($component)) { $__componentOriginal5863877a5171c196453bfa0bd807e410 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5863877a5171c196453bfa0bd807e410 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.app','data' => ['title' => 'Verifikasi Creator - Admin CuBu']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.app'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Verifikasi Creator - Admin CuBu']); ?>
    <section class="section-heading admin-heading">
        <div>
            <span class="eyebrow">Dashboard admin</span>
            <h1>Verifikasi creator</h1>
            <p>Tinjau dokumen dan pengalaman pengguna sebelum memberikan hak upload video.</p>
        </div>
    </section>

    <nav class="status-tabs" aria-label="Filter status">
        <?php $__currentLoopData = ['pending' => 'Menunggu', 'approved' => 'Disetujui', 'rejected' => 'Ditolak']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <a href="<?php echo e(route('admin.creator-verifications.index', ['status' => $value])); ?>" class="<?php echo e($status === $value ? 'active' : ''); ?>">
                <?php echo e($label); ?> <span><?php echo e($counts[$value] ?? 0); ?></span>
            </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </nav>

    <div class="admin-list">
        <?php $__empty_1 = true; $__currentLoopData = $verifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $verification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <article class="admin-list-row">
                <span class="profile-avatar profile-avatar-nav">
                    <?php if($verification->user->avatar_url): ?>
                        <img src="<?php echo e($verification->user->avatar_url); ?>" alt="">
                    <?php else: ?>
                        <span><?php echo e($verification->user->initials); ?></span>
                    <?php endif; ?>
                </span>
                <div>
                    <h2><?php echo e($verification->user->name); ?></h2>
                    <p><?php echo e('@'.$verification->user->username); ?> · <?php echo e($verification->submitted_at->format('d M Y, H:i')); ?></p>
                </div>
                <span class="status-badge status-<?php echo e($verification->status); ?>"><?php echo e(ucfirst($verification->status)); ?></span>
                <a href="<?php echo e(route('admin.creator-verifications.show', $verification)); ?>" class="button button-secondary button-small">Tinjau</a>
            </article>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <div class="empty-state">
                <h2>Tidak ada pengajuan</h2>
                <p>Belum ada data dengan status ini.</p>
            </div>
        <?php endif; ?>
    </div>

    <div class="pagination-wrap"><?php echo e($verifications->links()); ?></div>
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
<?php /**PATH C:\xampp\htdocs\praktikum-rpl-b-05\src\CuBu\resources\views/admin/creator-verifications/index.blade.php ENDPATH**/ ?>