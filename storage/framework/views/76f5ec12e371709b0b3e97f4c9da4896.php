<form action="<?php echo e($callbackUrl); ?>" method="post">
    <?php echo csrf_field(); ?>
    <input type="number" name="otp">
    <button type="submit">submit</button>
</form>
<?php /**PATH D:\xamp8.1\htdocs\haraj\resources\views\vendor\larapay\otp.blade.php ENDPATH**/ ?>