<?php
session_start();

echo "<h1>بررسی وضعیت لاگین</h1>";
echo "<div style='background: #f8f9fa; padding: 20px; border-radius: 5px; font-family: Tahoma;'>";

echo "<h2>Session Data:</h2>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

echo "<h2>Cookies:</h2>";
echo "<pre>";
print_r($_COOKIE);
echo "</pre>";

if (isset($_COOKIE['laravel_session'])) {
    echo "<div style='background: #d4edda; padding: 15px; margin: 10px 0; border-radius: 5px;'>";
    echo "<strong>✓ Laravel session cookie موجود است</strong><br>";
    echo "این یعنی احتمالاً لاگین هستید.";
    echo "</div>";
} else {
    echo "<div style='background: #f8d7da; padding: 15px; margin: 10px 0; border-radius: 5px;'>";
    echo "<strong>✗ Laravel session cookie موجود نیست</strong><br>";
    echo "شما لاگین نیستید.";
    echo "</div>";
}

echo "<h2>لینک‌های مفید:</h2>";
echo "<ul style='font-size: 16px;'>";
echo "<li><a href='/haraj/public/login'>صفحه لاگین</a></li>";
echo "<li><a href='/haraj/public/dashboard'>داشبورد</a></li>";
echo "<li><a href='/haraj/public/listings/create'>ایجاد حراجی</a></li>";
echo "</ul>";

echo "</div>";
