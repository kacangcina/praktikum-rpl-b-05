<?php if (isset($component)) { $__componentOriginal5863877a5171c196453bfa0bd807e410 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5863877a5171c196453bfa0bd807e410 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.app','data' => ['title' => 'Daftar - CuBu']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.app'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Daftar - CuBu']); ?>
    <section class="auth-shell">
        <div class="auth-story">
            <span class="eyebrow">Mulai dari dapurmu</span>
            <h1>Satu akun untuk semua inspirasi masak.</h1>
            <p>Buat akun gratis untuk membagikan resep dan menikmati seluruh pengalaman CuBu.</p>
            <div class="auth-story-mark">CuBu</div>
        </div>

        <div class="auth-panel">
            <span class="eyebrow">Bergabung dengan CuBu</span>
            <h2>Buat akun baru</h2>
            <p>Setelah mendaftar, kamu bisa langsung membagikan resep dalam bentuk foto.</p>

            <form method="POST" action="<?php echo e(route('register')); ?>" class="field-stack">
                <?php echo csrf_field(); ?>
                <label class="field">
                    <span>Username</span>
                    <input name="username" value="<?php echo e(old('username')); ?>" placeholder="Nama yang tampil di CuBu" required autofocus>
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
                    <span>Email</span>
                    <input type="email" name="email" value="<?php echo e(old('email')); ?>" placeholder="nama@email.com" required>
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

                <label class="field">
                    <span>Konfirmasi kata sandi</span>
                    <input type="password" name="password_confirmation" placeholder="Ulangi kata sandi" required>
                </label>

                <button class="button button-primary w-full">Daftar</button>
            </form>

            <p class="auth-switch">Sudah punya akun? <a href="<?php echo e(route('login')); ?>">Masuk</a></p>
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
<?php /**PATH C:\xampp\htdocs\praktikum-rpl-b-05\src\CuBu\resources\views\auth\register.blade.php ENDPATH**/ ?>