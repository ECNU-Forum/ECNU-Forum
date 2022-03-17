<?php $url = app('Flarum\Http\UrlGenerator'); ?>

<div class="container">
    <h2><?php echo e($translator->trans('core.views.admin.title')); ?></h2>

    <table class="NoJs-InfoTable Table">
        <caption><h3><?php echo e($translator->trans('core.views.admin.info.caption')); ?></h3></caption>
        <tbody>
            <tr>
                <td>Flarum</td>
                <td><?php echo e($flarumVersion); ?></td>
            </tr>
        <tr>
            <td>PHP</td>
            <td><?php echo e($phpVersion); ?></td>
        </tr>
        <tr>
            <td>MySQL</td>
            <td><?php echo e($mysqlVersion); ?></td>
        </tr>
        </tbody>
    </table>

    <table class="NoJs-ExtensionsTable Table">
        <caption><h3><?php echo e($translator->trans('core.views.admin.extensions.caption')); ?></h3></caption>
        <thead>
            <tr>
                <th></th>
                <th><?php echo e($translator->trans('core.views.admin.extensions.name')); ?></th>
                <th><?php echo e($translator->trans('core.views.admin.extensions.package_name')); ?></th>
                <th><?php echo e($translator->trans('core.views.admin.extensions.version')); ?></th>
                <th></th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $extensions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $extension): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php $isEnabled = in_array($extension->getId(), $extensionsEnabled); ?>

                <tr>
                    <td class="NoJs-ExtensionsTable-icon">
                        <div class="ExtensionIcon" style="<?php echo e($extension->getIconStyles()); ?>">
                            <span class="icon <?php echo e($extension->getIcon()['name'] ?? ''); ?>"></span>
                        </div>
                    </td>
                    <td class="NoJs-ExtensionsTable-title"><?php echo e($extension->getTitle()); ?></td>
                    <td class="NoJs-ExtensionsTable-name"><?php echo e($extension->name); ?></td>
                    <td class="NoJs-ExtensionsTable-version"><?php echo e($extension->getVersion()); ?></td>
                    <td class="NoJs-ExtensionsTable-state">
                        <span class="ExtensionListItem-Dot <?php echo e($isEnabled ? 'enabled' : 'disabled'); ?>" aria-hidden="true"></span>
                    </td>
                    <td class="NoJs-ExtensionsTable-toggle Table-controls">
                        <form action="<?php echo e($url->to('admin')->route('extensions.update', ['name' => $extension->getId()])); ?>" method="POST">
                            <input type="hidden" name="csrfToken" value="<?php echo e($csrfToken); ?>">
                            <input type="hidden" name="enabled" value="<?php echo e($isEnabled ? 0 : 1); ?>">

                            <?php if($isEnabled): ?>
                                <button type="submit" class="Button Table-controls-item"><?php echo e($translator->trans('core.views.admin.extensions.disable')); ?></button>
                            <?php else: ?>
                                <button type="submit" class="Button Table-controls-item"><?php echo e($translator->trans('core.views.admin.extensions.enable')); ?></button>
                            <?php endif; ?>
                        </form>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="6" class="NoJs-ExtensionsTable-empty"><?php echo e($translator->trans('core.views.admin.extensions.empty')); ?></td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php /**PATH /var/www/flarum/vendor/flarum/core/src/Admin/../../views/frontend/content/admin.blade.php ENDPATH**/ ?>