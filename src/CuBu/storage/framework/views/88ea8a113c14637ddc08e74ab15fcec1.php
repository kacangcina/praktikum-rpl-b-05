<?php if (isset($component)) { $__componentOriginal5863877a5171c196453bfa0bd807e410 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5863877a5171c196453bfa0bd807e410 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.app','data' => ['title' => 'Edit Profil - CuBu']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.app'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Edit Profil - CuBu']); ?>
    <section class="profile-edit-shell">
        <div class="form-page-heading profile-edit-heading">
            <span class="eyebrow">Pengaturan akun</span>
            <h1>Edit profil</h1>
            <p>Perbarui identitas yang akan dilihat pengguna lain saat menemukan resepmu.</p>
        </div>

        <form method="POST" action="<?php echo e(route('profile.update')); ?>" enctype="multipart/form-data" class="form-panel profile-edit-form">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PUT'); ?>

            <div class="avatar-editor">
                <label class="avatar-upload-control" data-avatar-dropzone>
                    <input type="file" name="avatar" accept="image/jpeg,image/png,image/webp" data-avatar-input>
                    <span class="profile-avatar profile-avatar-editor">
                        <?php if($user->avatar_url): ?>
                            <img src="<?php echo e($user->avatar_url); ?>" alt="Foto profil saat ini" data-avatar-preview>
                        <?php else: ?>
                            <span data-avatar-initials><?php echo e($user->initials); ?></span>
                            <img alt="Pratinjau foto profil" data-avatar-preview>
                        <?php endif; ?>
                    </span>
                    <span class="avatar-edit-icon"><i data-lucide="pencil"></i></span>
                </label>
                <div>
                    <h2>Foto profil</h2>
                    <p>Gunakan JPG, PNG, atau WebP maksimal 2 MB.</p>
                    <?php $__errorArgs = ['avatar'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="field-error"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>
            </div>

            <div class="profile-fields">
                <label class="field">
                    <span>Nama</span>
                    <input name="name" value="<?php echo e(old('name', $user->name)); ?>" required>
                    <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <small class="field-error"><?php echo e($message); ?></small> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </label>

                <label class="field">
                    <span>Username</span>
                    <input name="username" value="<?php echo e(old('username', $user->username)); ?>" required>
                    <?php $__errorArgs = ['username'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <small class="field-error"><?php echo e($message); ?></small> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </label>

                <label class="field">
                    <span>Bio</span>
                    <textarea name="bio" rows="6" maxlength="500" placeholder="Ceritakan minat dan gaya memasakmu..."><?php echo e(old('bio', $user->bio)); ?></textarea>
                    <?php $__errorArgs = ['bio'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <small class="field-error"><?php echo e($message); ?></small> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </label>
            </div>

            <div class="form-actions">
                <a href="<?php echo e(route('profile.show', $user)); ?>" class="button button-ghost">Batal</a>
                <button class="button button-primary">
                    <i data-lucide="save"></i>
                    Simpan profil
                </button>
            </div>
        </form>
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
<?php /**PATH C:\xampp\htdocs\praktikum-rpl-b-05\src\CuBu\resources\views\profiles\edit.blade.php ENDPATH**/ ?>