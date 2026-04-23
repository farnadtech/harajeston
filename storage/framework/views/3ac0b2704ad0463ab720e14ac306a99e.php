<html>

<body>
    <script>
        var form = document.createElement("form");
        form.setAttribute("method", "POST");
        form.setAttribute("action", "<?php echo e($action); ?>");
        form.setAttribute("target", "_self");
        var fields = <?php echo json_encode($fields, 15, 512) ?>;
        for (var key in fields) {
            var hiddenField = document.createElement("input");
            hiddenField.setAttribute("name", key);
            hiddenField.setAttribute("value", fields[key]);
            form.appendChild(hiddenField);
        }

        form.appendChild(hiddenField);

        document.body.appendChild(form);
        form.submit();
        document.body.removeChild(form);
    </script>
    redirecting
</body>

</html><?php /**PATH D:\xamp8.1\htdocs\haraj\vendor\farayaz\larapay\resources\views\redirector.blade.php ENDPATH**/ ?>