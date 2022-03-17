<?php
  $primaryColor = $settings->get('theme_primary_color', '#000');
?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    
    <title><?php if($__env->hasSection('title')): ?> <?php echo $__env->yieldContent('title'); ?> - <?php endif; ?><?php echo e($settings->get('forum_title')); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1">

    <style>
      * {
       box-sizing: border-box;
      }
      body {
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol";
        font-size: 18px;
        text-align: center;
        line-height: 1.5;
        color: #333;
      }
      input,
      button,
      select,
      textarea {
        font-family: inherit;
        font-size: inherit;
        line-height: inherit;
      }
      a {
        cursor: pointer;
        color: <?php echo e($primaryColor); ?>;
        text-decoration: none;
        font-weight: bold;
      }
      a:hover {
        text-decoration: underline;
      }
      .container {
        margin: 100px auto;
        max-width: 450px;
        padding: 0 15px;
      }
      .button {
        display: inline-block;
        padding: 15px 25px;
        background: <?php echo e($primaryColor); ?>;
        color: #fff;
        text-decoration: none;
        text-align: center;
        vertical-align: middle;
        border-radius: 4px;
        cursor: pointer;
        white-space: nowrap;
        font-weight: bold;
        border: 0;
      }
      .button:hover {
        text-decoration: none;
      }
      .button:active,
      .button.active {
        box-shadow: inset 0 3px 5px rgba(0, 0, 0, .125);
      }
      .form {
        max-width: 300px;
        margin: 0 auto;
      }
      .form .button {
        display: block;
        width: 100%;
      }
      .form-control {
        display: block;
        width: 100%;
        text-align: center;
        padding: 15px 20px;
        background-color: #fff;
        border: 2px solid #eee;
        border-radius: 4px;
        transition: border-color .15s;
      }
      .form-control:focus,
      .form-control.focus {
         border-color: <?php echo e($primaryColor); ?>;
      }
      .errors {
        color: #d83e3e;
      }
      .errors ul {
        list-style-type: none;
        margin: 0;
        padding: 0;
      }
    </style>
  </head>

  <body>
    <div class="container">
      <?php echo $__env->yieldContent('content'); ?>
    </div>
  </body>
</html>
<?php /**PATH /var/www/flarum/vendor/flarum/core/src/Forum/../../views/layouts/basic.blade.php ENDPATH**/ ?>