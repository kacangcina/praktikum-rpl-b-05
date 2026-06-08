<?php if (isset($component)) { $__componentOriginal5863877a5171c196453bfa0bd807e410 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5863877a5171c196453bfa0bd807e410 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.app','data' => ['title' => 'Pengajuan Creator - CuBu']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.app'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Pengajuan Creator - CuBu']); ?>
    <section class="form-page-heading">
        <span class="eyebrow">Program Creator CuBu</span>
        <h1>Ajukan verifikasi creator</h1>
        <p>Creator terverifikasi dapat mengunggah video kelas memasak. Kirim dokumen dan informasi yang membantu admin menilai pengalamanmu.</p>
    </section>

    <?php if($latestVerification?->status === 'pending'): ?>
        <div class="status-panel status-pending">
            <i data-lucide="clock"></i>
            <div>
                <h2>Pengajuan sedang ditinjau</h2>
                <p>Dikirim <?php echo e($latestVerification->submitted_at->diffForHumans()); ?>. Kamu akan menerima notifikasi setelah admin memberi keputusan.</p>
            </div>
        </div>
    <?php else: ?>
        <?php if($latestVerification?->status === 'rejected'): ?>
            <div class="status-panel status-rejected">
                <i data-lucide="circle-x"></i>
                <div>
                    <h2>Pengajuan sebelumnya ditolak</h2>
                    <p><?php echo e($latestVerification->rejection_reason); ?></p>
                </div>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?php echo e(route('creator.apply.store')); ?>" enctype="multipart/form-data" class="form-panel verification-form">
            <?php echo csrf_field(); ?>

            <label class="field">
                <span>Dokumen pendukung</span>
                <input type="file" name="document" accept=".pdf,.jpg,.jpeg,.png" required>
                <small>KTP, sertifikat, atau portofolio dalam PDF/JPG/PNG, maksimal 10 MB. Dokumen disimpan privat.</small>
                <?php $__errorArgs = ['document'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <small class="field-error"><?php echo e($message); ?></small> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </label>

            <label class="field">
                <span>Link portofolio <small>(opsional)</small></span>
                <input type="url" name="portfolio_url" value="<?php echo e(old('portfolio_url')); ?>" placeholder="https://instagram.com/...">
                <?php $__errorArgs = ['portfolio_url'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <small class="field-error"><?php echo e($message); ?></small> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </label>

            <label class="field">
                <span>Pengalaman dan alasan mendaftar</span>
                <textarea name="notes" rows="7" required placeholder="Ceritakan pengalaman memasak, jenis konten, dan rencana kelas video..."><?php echo e(old('notes')); ?></textarea>
                <?php $__errorArgs = ['notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <small class="field-error"><?php echo e($message); ?></small> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </label>

            <div class="form-actions">
                <a href="<?php echo e(route('profile.me')); ?>" class="button button-ghost">Batal</a>
                <button class="button button-primary"><i data-lucide="send"></i>Kirim pengajuan</button>
            </div>
        </form>
    <?php endif; ?>
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
<?php /**PATH C:\xampp\htdocs\praktikum-rpl-b-05\src\CuBu\resources\views\creator-verifications\create.blade.php ENDPATH**/ ?>