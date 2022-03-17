<?php $__env->startSection('content'); ?>
  <p>
    <?php echo e($message); ?>

  </p>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('flarum.forum::layouts.basic', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/flarum/vendor/flarum/core/src/Forum/../../views/error/default.blade.php ENDPATH**/ ?>