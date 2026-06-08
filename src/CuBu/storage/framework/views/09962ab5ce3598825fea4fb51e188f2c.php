<?php if (isset($component)) { $__componentOriginal5863877a5171c196453bfa0bd807e410 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5863877a5171c196453bfa0bd807e410 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.app','data' => ['title' => 'Tinjau '.e($verification->user->username).' - CuBu']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.app'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Tinjau '.e($verification->user->username).' - CuBu']); ?>
    <a href="<?php echo e(route('admin.creator-verifications.index')); ?>" class="back-link">Kembali ke antrean</a>

    <section class="admin-review-grid">
        <article class="form-panel">
            <span class="eyebrow">Pemohon creator</span>
            <div class="review-user">
                <span class="profile-avatar profile-avatar-large">
                    <?php if($verification->user->avatar_url): ?>
                        <img src="<?php echo e($verification->user->avatar_url); ?>" alt="">
                    <?php else: ?>
                        <span><?php echo e($verification->user->initials); ?></span>
                    <?php endif; ?>
                </span>
                <div>
                    <h1><?php echo e($verification->user->name); ?></h1>
                    <p><?php echo e('@'.$verification->user->username); ?></p>
                    <a href="<?php echo e(route('profile.show', $verification->user)); ?>">Lihat profil publik</a>
                </div>
            </div>

            <dl class="review-details">
                <div><dt>Dikirim</dt><dd><?php echo e($verification->submitted_at->format('d M Y, H:i')); ?></dd></div>
                <div><dt>Pengalaman</dt><dd><?php echo e($verification->notes); ?></dd></div>
                <?php if($verification->portfolio_url): ?>
                    <div><dt>Portofolio</dt><dd><a href="<?php echo e($verification->portfolio_url); ?>" target="_blank" rel="noopener"><?php echo e($verification->portfolio_url); ?></a></dd></div>
                <?php endif; ?>
                <div><dt>Dokumen</dt><dd><a href="<?php echo e(route('creator.verifications.document', $verification)); ?>" class="button button-secondary button-small"><i data-lucide="download"></i>Unduh dokumen</a></dd></div>
            </dl>
        </article>

        <aside class="form-panel review-action">
            <span class="eyebrow">Keputusan</span>
            <h2>Status: <?php echo e(ucfirst($verification->status)); ?></h2>

            <?php if($verification->status === 'pending'): ?>
                <form method="POST" action="<?php echo e(route('admin.creator-verifications.approve', $verification)); ?>">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PATCH'); ?>
                    <button class="button button-success w-full"><i data-lucide="badge-check"></i>Setujui creator</button>
                </form>

                <form method="POST" action="<?php echo e(route('admin.creator-verifications.reject', $verification)); ?>" class="field-stack">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PATCH'); ?>
                    <label class="field">
                        <span>Alasan penolakan</span>
                        <textarea name="rejection_reason" rows="5" required placeholder="Jelaskan dokumen yang kurang atau alasan lainnya..."><?php echo e(old('rejection_reason')); ?></textarea>
                        <?php $__errorArgs = ['rejection_reason'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <small class="field-error"><?php echo e($message); ?></small> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </label>
                    <button class="button button-danger w-full"><i data-lucide="circle-x"></i>Tolak pengajuan</button>
                </form>
            <?php else: ?>
                <p>Ditinjau oleh <?php echo e($verification->reviewer?->username ?? 'admin'); ?> pada <?php echo e($verification->reviewed_at?->format('d M Y, H:i')); ?>.</p>
                <?php if($verification->rejection_reason): ?>
                    <div class="status-panel status-rejected"><p><?php echo e($verification->rejection_reason); ?></p></div>
                <?php endif; ?>
            <?php endif; ?>
        </aside>
    </section>
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
<?php /**PATH C:\xampp\htdocs\praktikum-rpl-b-05\src\CuBu\resources\views\admin\creator-verifications\show.blade.php ENDPATH**/ ?>