<?php $url = app('Flarum\Http\UrlGenerator'); ?>

<div class="container">
    <h2><?php echo e($translator->trans('flarum-tags.forum.index.tags_link')); ?></h2>

    <?php $__currentLoopData = [$primaryTags, $secondaryTags]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <ul>
            <?php $__currentLoopData = $category->pluck('attributes', 'id'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $tag): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li>
                    <a href="<?php echo e($url->to('forum')->route('tag', [
                                'slug' => $tag['slug']
                            ])); ?>">
                        <?php echo e($tag['name']); ?>

                    </a>

                    <?php if($children->has($id)): ?>
                        <ul>
                            <?php $__currentLoopData = $children->get($id); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $child): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li>
                                    <a href="<?php echo e($url->to('forum')->route('tag', [
                                                'slug' => $child['attributes']['slug']
                                            ])); ?>">
                                        <?php echo e($child['attributes']['name']); ?>

                                    </a>
                                </li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    <?php endif; ?>
                </li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>
<?php /**PATH /var/www/flarum/vendor/flarum/tags/views/frontend/content/tags.blade.php ENDPATH**/ ?>