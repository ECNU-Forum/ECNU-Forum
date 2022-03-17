<?php $url = app('Flarum\Http\UrlGenerator'); ?>

<div class="container">
    <h2><?php echo e($translator->trans('core.views.index.all_discussions_heading')); ?></h2>

    <ul>
        <?php $__currentLoopData = $apiDocument->data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $discussion): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li>
                <a href="<?php echo e($url->to('forum')->route('discussion', [
                    'id' => $discussion->attributes->slug
                ])); ?>">
                    <?php echo e($discussion->attributes->title); ?>

                </a>
            </li>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </ul>

    <?php if(isset($apiDocument->links->prev)): ?>
        <a href="<?php echo e($url->to('forum')->route('index')); ?>?page=<?php echo e($page - 1); ?>">&laquo; <?php echo e($translator->trans('core.views.index.previous_page_button')); ?></a>
    <?php endif; ?>

    <?php if(isset($apiDocument->links->next)): ?>
        <a href="<?php echo e($url->to('forum')->route('index')); ?>?page=<?php echo e($page + 1); ?>"><?php echo e($translator->trans('core.views.index.next_page_button')); ?> &raquo;</a>
    <?php endif; ?>
</div>
<?php /**PATH /var/www/flarum/vendor/flarum/core/src/Forum/../../views/frontend/content/index.blade.php ENDPATH**/ ?>