<?php
$viewPath = __DIR__ . '/../storage/framework/views';

if (is_dir($viewPath)) {
    $files = glob($viewPath . '/*.php');
    $count = 0;
    
    foreach ($files as $file) {
        if (is_file($file)) {
            unlink($file);
            $count++;
        }
    }
    
    echo "Deleted $count compiled view files.\n";
    echo "Please refresh your browser now.\n";
} else {
    echo "Views directory not found.\n";
}
