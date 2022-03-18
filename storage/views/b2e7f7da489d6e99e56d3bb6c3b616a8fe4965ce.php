<?php $__env->startSection('title', $translator->trans('core.views.confirm_email.title')); ?>

<?php $__env->startSection('content'); ?>
    <?php if($errors->any()): ?>
        <div class="errors">
            <ul>
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    <form class="form" method="POST" action="">
        <input type="hidden" name="csrfToken" value="<?php echo e($csrfToken); ?>" />

        <p><?php echo e($translator->trans('core.views.confirm_email.text')); ?></p>

        <p class="form-group">
            <button type="submit" class="button"><?php echo e($translator->trans('core.views.confirm_email.submit_button')); ?></button>
        </p>
    </form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('flarum.forum::layouts.basic', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/flarum/vendor/flarum/core/src/Forum/../../views/confirm-email.blade.php ENDPATH**/ ?>