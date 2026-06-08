<?php if (isset($component)) { $__componentOriginal5863877a5171c196453bfa0bd807e410 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5863877a5171c196453bfa0bd807e410 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.layouts.app','data' => ['title' => ''.e($user->username).' - CuBu']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('layouts.app'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => ''.e($user->username).' - CuBu']); ?>
    <section class="profile-header">
        <div class="profile-avatar-shell">
            <div class="profile-avatar profile-avatar-large">
                <?php if($user->avatar_url): ?>
                    <img src="<?php echo e($user->avatar_url); ?>" alt="Foto profil <?php echo e($user->username); ?>">
                <?php else: ?>
                    <span><?php echo e($user->initials); ?></span>
                <?php endif; ?>
            </div>

            <?php if($user->canUploadVideos()): ?>
                <span class="profile-role-badge creator" title="Creator terverifikasi" aria-label="Creator terverifikasi">
                    <i data-lucide="chef-hat"></i>
                </span>
            <?php elseif($user->isAdmin()): ?>
                <span class="profile-role-badge admin" title="Administrator" aria-label="Administrator">
                    <i data-lucide="shield-check"></i>
                </span>
            <?php else: ?>
                <?php if(auth()->guard()->check()): ?>
                    <?php if(auth()->id() === $user->id): ?>
                        <a href="<?php echo e(route('creator.apply')); ?>" class="profile-role-badge verification" title="Ajukan verifikasi creator" aria-label="Ajukan verifikasi creator">?</a>
                    <?php endif; ?>
                <?php endif; ?>
            <?php endif; ?>
        </div>

        <div class="profile-identity">
            <div class="profile-title-row">
                <h1><?php echo e($user->name); ?></h1>
                <span class="profile-name-divider" aria-hidden="true"></span>
                <p class="profile-username"><?php echo e('@'.$user->username); ?></p>
            </div>

            <div class="profile-stats">
                <?php if (! ($user->isAdmin())): ?>
                    <div><strong><?php echo e($recipes->total()); ?></strong><span>Resep</span></div>
                <?php endif; ?>
                <?php if($user->canUploadVideos()): ?>
                    <div><strong><?php echo e($user->videos()->count()); ?></strong><span>Video</span></div>
                <?php endif; ?>
                <div>
                    <strong><?php echo e($user->role_label); ?></strong>
                    <span>Status akun</span>
                </div>
            </div>

            <?php if(auth()->guard()->check()): ?>
                <?php if(auth()->id() === $user->id): ?>
                    <a href="<?php echo e(route('profile.edit')); ?>" class="button button-secondary profile-edit-button">
                        <i data-lucide="settings"></i>
                        Edit profil
                    </a>
                <?php endif; ?>
            <?php endif; ?>

            <p class="profile-bio">
                <?php echo e($user->bio ?: ($user->isAdmin() ? 'Akun administrator untuk mengelola verifikasi creator dan operasional CuBu.' : 'Belum ada bio. Ceritakan sedikit tentang gaya memasakmu.')); ?>

            </p>

            <?php if(auth()->guard()->check()): ?>
                <?php if(auth()->id() === $user->id): ?>
                    <?php if($user->isAdmin()): ?>
                        <div class="creator-status-actions">
                            <a href="<?php echo e(route('admin.creator-verifications.index')); ?>" class="button button-primary">
                                <i data-lucide="shield-check"></i>
                                Buka dashboard admin
                            </a>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </section>

    <?php if($notifications->isNotEmpty()): ?>
        <section class="notification-panel">
            <div class="panel-heading">
                <div><span class="eyebrow">Pembaruan akun</span><h2>Notifikasi</h2></div>
                <?php if($notifications->contains(fn ($notification) => is_null($notification->read_at))): ?>
                    <form method="POST" action="<?php echo e(route('notifications.read')); ?>">
                        <?php echo csrf_field(); ?>
                        <button class="button button-ghost button-small">Tandai dibaca</button>
                    </form>
                <?php endif; ?>
            </div>
            <div class="notification-list">
                <?php $__currentLoopData = $notifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <article class="<?php echo e($notification->read_at ? '' : 'unread'); ?>">
                        <i data-lucide="<?php echo e(($notification->data['status'] ?? '') === 'approved' ? 'badge-check' : 'circle-x'); ?>"></i>
                        <div>
                            <h3><?php echo e($notification->data['title'] ?? 'Notifikasi'); ?></h3>
                            <p><?php echo e($notification->data['message'] ?? ''); ?></p>
                        </div>
                    </article>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </section>
    <?php endif; ?>

    <?php if (! ($user->isAdmin())): ?>
        <section class="profile-recipes-heading">
            <div>
                <span class="eyebrow">Karya dapur</span>
                <h2>Resep <?php echo e($user->name); ?></h2>
            </div>

            <?php if(auth()->guard()->check()): ?>
                <?php if(auth()->id() === $user->id): ?>
                    <?php if(auth()->user()->canPublishRecipes()): ?>
                        <a href="<?php echo e(route('recipes.create')); ?>" class="button button-primary">
                            <i data-lucide="plus"></i>
                            Buat resep
                        </a>
                    <?php endif; ?>
                <?php endif; ?>
            <?php endif; ?>
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
                        <p class="recipe-author"><?php echo e($recipe->estimated_time); ?> menit</p>
                        <h3><a href="<?php echo e(route('recipes.show', $recipe)); ?>"><?php echo e($recipe->title); ?></a></h3>
                        <p><?php echo e(\Illuminate\Support\Str::limit($recipe->description, 92)); ?></p>
                        <div class="recipe-meta">
                            <span><?php echo e($recipe->ingredients->count()); ?> bahan</span>
                            <span><?php echo e(ucfirst($recipe->difficulty)); ?></span>
                        </div>
                        <?php if(auth()->guard()->check()): ?>
                            <?php if(auth()->id() === $user->id && $user->canUploadVideos() && ! $recipe->thumbnail_url): ?>
                                <a href="<?php echo e(route('recipes.video.create', $recipe)); ?>" class="button <?php echo e($recipe->video ? 'button-secondary' : 'button-primary'); ?> button-small profile-video-action">
                                    <i data-lucide="<?php echo e($recipe->video ? 'replace' : 'video'); ?>"></i>
                                    <?php echo e($recipe->video ? 'Ganti video' : 'Tambah video'); ?>

                                </a>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </article>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="empty-state full-grid">
                    <i data-lucide="book-open"></i>
                    <h2>Belum ada resep</h2>
                    <p><?php echo e($user->canUploadVideos() ? 'Buat resep terlebih dahulu sebelum menambahkan video.' : 'Resep yang dipublikasikan akan tampil di sini.'); ?></p>
                    <?php if(auth()->guard()->check()): ?>
                        <?php if(auth()->id() === $user->id && $user->canPublishRecipes()): ?>
                            <a href="<?php echo e(route('recipes.create')); ?>" class="button button-primary">Buat resep pertama</a>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </section>

        <div class="pagination-wrap"><?php echo e($recipes->links()); ?></div>
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
<?php /**PATH C:\xampp\htdocs\praktikum-rpl-b-05\src\CuBu\resources\views/profiles/show.blade.php ENDPATH**/ ?>