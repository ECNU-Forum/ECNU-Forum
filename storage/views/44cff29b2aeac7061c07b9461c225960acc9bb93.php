<?php $__env->startSection('content'); ?>
  <p>
    <?php echo e($message); ?>

  </p>
  <p>
    <a href="javascript:history.back()">
      <?php echo e($translator->trans('core.views.error.csrf_token_mismatch_return_link')); ?>

    </a>
  </p>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('flarum.forum::layouts.basic', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/flarum/vendor/flarum/core/src/Forum/../../views/error/csrf_token_mismatch.blade.php ENDPATH**/ ?>