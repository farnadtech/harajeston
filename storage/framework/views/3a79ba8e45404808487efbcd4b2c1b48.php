<form action="<?php echo e($callbackUrl); ?>" method="post">
    <?php echo csrf_field(); ?>
    <input type="number" name="otp">
    <button type="submit">submit</button>
</form>
<?php /**PATH D:\xamp8.1\htdocs\haraj\vendor\farayaz\larapay\resources\views\otp.blade.php ENDPATH**/ ?>