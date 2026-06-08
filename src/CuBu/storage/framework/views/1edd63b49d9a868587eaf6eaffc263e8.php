<?php if (isset($component)) { $__componentOriginal5863877a5171c196453bfa0bd807e410 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5863877a5171c196453bfa0bd807e410 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.app','data' => ['title' => 'Video '.e($recipe->title).' - CuBu']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.app'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Video '.e($recipe->title).' - CuBu']); ?>
    <section class="form-page-heading">
        <span class="eyebrow">Video resep</span>
        <h1><?php echo e($recipe->video ? 'Ganti' : 'Tambahkan'); ?> video <?php echo e($recipe->title); ?></h1>
        <p>Video akan tampil langsung pada halaman detail resep. Format MP4, maksimal 500 MB.</p>
    </section>

    <form method="POST" action="<?php echo e(route('recipes.video.store', $recipe)); ?>" enctype="multipart/form-data" class="form-panel verification-form">
        <?php echo csrf_field(); ?>
        <label class="field">
            <span>Judul video</span>
            <input name="title" value="<?php echo e(old('title', $recipe->video?->title)); ?>" required>
            <?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <small class="field-error"><?php echo e($message); ?></small> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </label>
        <label class="field">
            <span>Deskripsi</span>
            <textarea name="description" rows="5"><?php echo e(old('description', $recipe->video?->description)); ?></textarea>
            <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <small class="field-error"><?php echo e($message); ?></small> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </label>
        <label class="field">
            <span>Tingkat kesulitan</span>
            <select name="difficulty" required>
                <option value="">Pilih kesulitan</option>
                <?php $__currentLoopData = ['mudah', 'sedang', 'sulit']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $difficulty): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($difficulty); ?>" <?php if(old('difficulty', $recipe->video?->difficulty) === $difficulty): echo 'selected'; endif; ?>><?php echo e(ucfirst($difficulty)); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <?php $__errorArgs = ['difficulty'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <small class="field-error"><?php echo e($message); ?></small> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </label>
        <label class="field">
            <span>File video MP4</span>
            <input type="file" name="video" accept="video/mp4" <?php if(! $recipe->video): echo 'required'; endif; ?>>
            <?php if($recipe->video): ?>
                <small>Kosongkan jika hanya ingin mengubah judul atau deskripsi video.</small>
            <?php endif; ?>
            <?php $__errorArgs = ['video'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <small class="field-error"><?php echo e($message); ?></small> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </label>
        <div class="form-actions">
            <a href="<?php echo e(route('recipes.show', $recipe)); ?>" class="button button-ghost">Batal</a>
            <button class="button button-primary"><i data-lucide="upload"></i>Simpan video</button>
        </div>
    </form>
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
<?php /**PATH C:\xampp\htdocs\praktikum-rpl-b-05\src\CuBu\resources\views\videos\create.blade.php ENDPATH**/ ?>