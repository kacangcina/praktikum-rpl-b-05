<?php if (isset($component)) { $__componentOriginal5863877a5171c196453bfa0bd807e410 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5863877a5171c196453bfa0bd807e410 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.app','data' => ['title' => 'Buat Resep - CuBu']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.app'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Buat Resep - CuBu']); ?>
    <div class="form-page-heading">
        <span class="eyebrow">Bagikan masakanmu</span>
        <h1>Buat resep baru</h1>
        <p>Lengkapi setiap bagian agar resep mudah dipahami dan berhasil dicoba oleh orang lain.</p>
    </div>

    <form method="POST" action="<?php echo e(route('recipes.store')); ?>" enctype="multipart/form-data" class="recipe-form" data-recipe-form>
        <?php echo csrf_field(); ?>

        <section class="form-panel form-intro-grid">
            <div>
                <h2>Informasi utama</h2>
                <p class="form-help">Pilih salah satu media utama: foto masakan atau video memasak.</p>

                <div class="media-upload-grid">
                    <label class="upload-dropzone" data-image-dropzone>
                        <input type="file" name="thumbnail" accept="image/jpeg,image/png,image/webp" data-image-input>
                        <span class="upload-icon">+</span>
                        <strong>Unggah foto masakan</strong>
                        <small>JPG, PNG, atau WebP, maksimal 5 MB</small>
                        <img data-image-preview alt="Pratinjau foto masakan">
                    </label>

                    <?php if(auth()->user()->canUploadVideos()): ?>
                        <label class="upload-dropzone video-upload-dropzone" data-video-dropzone>
                            <input type="file" name="video" accept="video/mp4" data-video-input>
                            <span class="upload-icon"><i data-lucide="video"></i></span>
                            <strong>Unggah video memasak</strong>
                            <small data-video-file-name>MP4, maksimal 500 MB</small>
                        </label>
                    <?php endif; ?>
                </div>
                <?php $__errorArgs = ['thumbnail'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="field-error"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                <?php $__errorArgs = ['video'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="field-error"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <div class="field-stack">
                <label class="field">
                    <span>Judul resep</span>
                    <input name="title" value="<?php echo e(old('title')); ?>" placeholder="Contoh: Soto Ayam Lamongan" required>
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
                    <span>Deskripsi singkat</span>
                    <textarea name="description" rows="4" placeholder="Ceritakan rasa dan ciri khas masakan ini..." required><?php echo e(old('description')); ?></textarea>
                    <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <small class="field-error"><?php echo e($message); ?></small> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </label>

                <div class="two-fields">
                    <label class="field">
                        <span>Tingkat kesulitan</span>
                        <select name="difficulty" required>
                            <option value="">Pilih kesulitan</option>
                            <option value="mudah" <?php if(old('difficulty') === 'mudah'): echo 'selected'; endif; ?>>Mudah</option>
                            <option value="sedang" <?php if(old('difficulty') === 'sedang'): echo 'selected'; endif; ?>>Sedang</option>
                            <option value="sulit" <?php if(old('difficulty') === 'sulit'): echo 'selected'; endif; ?>>Sulit</option>
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
                        <span>Estimasi waktu</span>
                        <div class="input-suffix">
                            <input type="number" name="estimated_time" min="1" max="1440" value="<?php echo e(old('estimated_time', 30)); ?>" required>
                            <span>menit</span>
                        </div>
                        <?php $__errorArgs = ['estimated_time'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <small class="field-error"><?php echo e($message); ?></small> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </label>
                </div>
            </div>
        </section>

        <div class="form-columns">
            <section class="form-panel">
                <div class="panel-heading">
                    <div>
                        <span class="eyebrow">Persiapan</span>
                        <h2>Alat masak</h2>
                    </div>
                    <button type="button" class="icon-button" data-add-row="tools-list" title="Tambah alat" aria-label="Tambah alat">+</button>
                </div>
                <div id="tools-list" class="dynamic-list" data-row-list>
                    <?php $__currentLoopData = old('tools', ['']); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tool): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="dynamic-row">
                            <input name="tools[]" value="<?php echo e($tool); ?>" placeholder="Nama alat masak" required>
                            <button type="button" class="remove-button" data-remove-row aria-label="Hapus alat">&times;</button>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <?php $__errorArgs = ['tools'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="field-error"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                <?php $__errorArgs = ['tools.*'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="field-error"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </section>

            <section class="form-panel">
                <div class="panel-heading">
                    <div>
                        <span class="eyebrow">Takaran lengkap</span>
                        <h2>Bahan</h2>
                    </div>
                    <button type="button" class="icon-button" data-add-row="ingredients-list" title="Tambah bahan" aria-label="Tambah bahan">+</button>
                </div>
                <div id="ingredients-list" class="dynamic-list" data-row-list>
                    <?php
                        $oldNames = old('ingredient_names', ['']);
                        $oldQuantities = old('ingredient_quantities', ['']);
                    ?>
                    <?php $__currentLoopData = $oldNames; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="dynamic-row ingredient-row">
                            <input name="ingredient_names[]" value="<?php echo e($name); ?>" placeholder="Nama bahan" required>
                            <input name="ingredient_quantities[]" value="<?php echo e($oldQuantities[$index] ?? ''); ?>" placeholder="Takaran" required>
                            <button type="button" class="remove-button" data-remove-row aria-label="Hapus bahan">&times;</button>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
                <?php $__errorArgs = ['ingredient_names'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="field-error"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                <?php $__errorArgs = ['ingredient_names.*'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="field-error"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                <?php $__errorArgs = ['ingredient_quantities.*'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="field-error"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </section>
        </div>

        <section class="form-panel">
            <div class="panel-heading">
                <div>
                    <span class="eyebrow">Urut dan jelas</span>
                    <h2>Langkah memasak</h2>
                </div>
                <button type="button" class="button button-secondary button-small" data-add-row="steps-list">+ Tambah langkah</button>
            </div>
            <div id="steps-list" class="steps-editor" data-row-list data-numbered>
                <?php
                    $oldStepTitles = old('step_titles', ['']);
                    $oldSteps = old('steps', ['']);
                ?>
                <?php $__currentLoopData = $oldSteps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $step): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="step-editor-row">
                        <span class="step-editor-number"></span>
                        <div class="step-editor-fields">
                            <input name="step_titles[]" value="<?php echo e($oldStepTitles[$index] ?? ''); ?>" placeholder="Contoh: Rebus ayam" required>
                            <textarea name="steps[]" rows="3" placeholder="Jelaskan cara, waktu, dan tanda kematangannya..." required><?php echo e($step); ?></textarea>
                        </div>
                        <button type="button" class="remove-button" data-remove-row aria-label="Hapus langkah">&times;</button>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <?php $__errorArgs = ['step_titles'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="field-error"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            <?php $__errorArgs = ['step_titles.*'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="field-error"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            <?php $__errorArgs = ['steps'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="field-error"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            <?php $__errorArgs = ['steps.*'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="field-error"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </section>

        <div class="form-actions">
            <a href="<?php echo e(route('recipes.index')); ?>" class="button button-ghost">Batal</a>
            <button class="button button-primary">Publikasikan resep</button>
        </div>
    </form>

    <template id="tools-list-template">
        <div class="dynamic-row">
            <input name="tools[]" placeholder="Nama alat masak" required>
            <button type="button" class="remove-button" data-remove-row aria-label="Hapus alat">&times;</button>
        </div>
    </template>
    <template id="ingredients-list-template">
        <div class="dynamic-row ingredient-row">
            <input name="ingredient_names[]" placeholder="Nama bahan" required>
            <input name="ingredient_quantities[]" placeholder="Takaran" required>
            <button type="button" class="remove-button" data-remove-row aria-label="Hapus bahan">&times;</button>
        </div>
    </template>
    <template id="steps-list-template">
        <div class="step-editor-row">
            <span class="step-editor-number"></span>
            <div class="step-editor-fields">
                <input name="step_titles[]" placeholder="Contoh: Rebus ayam" required>
                <textarea name="steps[]" rows="3" placeholder="Jelaskan cara, waktu, dan tanda kematangannya..." required></textarea>
            </div>
            <button type="button" class="remove-button" data-remove-row aria-label="Hapus langkah">&times;</button>
        </div>
    </template>
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
<?php /**PATH C:\xampp\htdocs\praktikum-rpl-b-05\src\CuBu\resources\views/recipes/create.blade.php ENDPATH**/ ?>