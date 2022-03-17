<div id="flarum-loading" style="display: none">
    <?php echo e($translator->trans('core.views.content.loading_text')); ?>

</div>

<noscript>
    <div class="Alert">
        <div class="container">
            <?php echo e($translator->trans('core.views.content.javascript_disabled_message')); ?>

        </div>
    </div>
</noscript>

<div id="flarum-loading-error" style="display: none">
    <div class="Alert">
        <div class="container">
            <?php echo e($translator->trans('core.views.content.load_error_message')); ?>

        </div>
    </div>
</div>

<noscript id="flarum-content">
    <?php echo $content; ?>

</noscript>
<?php /**PATH /var/www/flarum/vendor/flarum/core/src/Frontend/../../views/frontend/content.blade.php ENDPATH**/ ?>