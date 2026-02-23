<?php
$file = 'resources/views/auth/auth.blade.php';
$content = file_get_contents($file);
$content = str_replace(
    'href="#">رمز عبور را فراموش کردید؟',
    'href="{{ route(\'password.request\') }}">رمز عبور را فراموش کردید؟',
    $content
);
file_put_contents($file, $content);
echo "Fixed!\n";
