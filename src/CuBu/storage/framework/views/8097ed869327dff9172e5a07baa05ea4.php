<?php if (isset($component)) { $__componentOriginal5863877a5171c196453bfa0bd807e410 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5863877a5171c196453bfa0bd807e410 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.app','data' => ['title' => 'Masuk - CuBu']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.app'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Masuk - CuBu']); ?>
    <section class="auth-shell">
        <div class="auth-story">
            <span class="eyebrow">Selamat datang kembali</span>
            <h1>Masak lebih percaya diri bersama CuBu.</h1>
            <p>Simpan inspirasi, tulis resep andalan, dan temukan masakan baru dari bahan yang ada di rumah.</p>
            <div class="auth-story-mark">CuBu</div>
        </div>

        <div class="auth-panel">
            <span class="eyebrow">Masuk ke akun</span>
            <h2>Halo, koki!</h2>
            <p>Gunakan email dan kata sandi yang sudah didaftarkan.</p>

            <form method="POST" action="<?php echo e(route('login')); ?>" class="field-stack">
                <?php echo csrf_field(); ?>
                <label class="field">
                    <span>Email</span>
                    <input type="email" name="email" value="<?php echo e(old('email')); ?>" placeholder="nama@email.com" required autofocus>
                    <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <small class="field-error"><?php echo e($message); ?></small> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </label>

                <label class="field">
                    <span>Kata sandi</span>
                    <input type="password" name="password" placeholder="Minimal 8 karakter" required>
                    <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <small class="field-error"><?php echo e($message); ?></small> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </label>

                <label class="checkbox-field">
                    <input type="checkbox" name="remember" value="1">
                    <span>Ingat saya</span>
                </label>

                <button class="button button-primary w-full">Masuk ke CuBu</button>
            </form>

            <p class="auth-switch">Belum punya akun? <a href="<?php echo e(route('register')); ?>">Daftar sekarang</a></p>
        </div>
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
<?php /**PATH C:\xampp\htdocs\praktikum-rpl-b-05\src\CuBu\resources\views/auth/login.blade.php ENDPATH**/ ?>