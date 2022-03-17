<?php $url = app('Flarum\Http\UrlGenerator'); ?>

<?php $__env->startSection('title', $translator->trans('core.views.reset_password.title')); ?>

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

  <form class="form" method="POST" action="<?php echo e($url->to('forum')->route('savePassword')); ?>">
    <input type="hidden" name="csrfToken" value="<?php echo e($csrfToken); ?>">
    <input type="hidden" name="passwordToken" value="<?php echo e($passwordToken); ?>">

    <p class="form-group">
      <input type="password" class="form-control" name="password" autocomplete="new-password" placeholder="<?php echo e($translator->trans('core.views.reset_password.new_password_label')); ?>">
    </p>

    <p class="form-group">
      <input type="password" class="form-control" name="password_confirmation" autocomplete="new-password" placeholder="<?php echo e($translator->trans('core.views.reset_password.confirm_password_label')); ?>">
    </p>

    <p class="form-group">
      <button type="submit" class="button"><?php echo e($translator->trans('core.views.reset_password.submit_button')); ?></button>
    </p>
  </form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('flarum.forum::layouts.basic', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/flarum/vendor/flarum/core/src/Forum/../../views/reset-password.blade.php ENDPATH**/ ?>