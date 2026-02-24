<?php
/**
 * تست تبدیل مبلغ تومان به ریال
 */

echo "<style>body{font-family:Tahoma;direction:rtl;padding:20px;} table{border-collapse:collapse;width:100%;} th,td{border:1px solid #ddd;padding:8px;text-align:right;} th{background:#4CAF50;color:white;}</style>";

echo "<h1>تست تبدیل مبلغ (تصحیح شده)</h1>";

$testAmounts = [10000, 50000, 100000, 500000, 1000000];

echo "<table>";
echo "<tr><th>مبلغ ورودی کاربر (تومان)</th><th>مالیات 9%</th><th>مبلغ نهایی (تومان)</th><th>مبلغ ارسالی به درگاه (ریال)</th><th>نمایش در درگاه (تومان)</th></tr>";

foreach ($testAmounts as $amount) {
    $tax = (int) ($amount * 0.09);
    $finalAmount = $amount + $tax;
    $amountInRial = $finalAmount * 10;
    $displayInGateway = $amountInRial / 10; // درگاه خودش به تومان نمایش میده
    
    echo "<tr>";
    echo "<td>" . number_format($amount) . " تومان</td>";
    echo "<td>" . number_format($tax) . " تومان</td>";
    echo "<td>" . number_format($finalAmount) . " تومان</td>";
    echo "<td>" . number_format($amountInRial) . " ریال</td>";
    echo "<td style='color:green;font-weight:bold;'>" . number_format($displayInGateway) . " تومان ✓</td>";
    echo "</tr>";
}

echo "</table>";

echo "<hr>";
echo "<h2>توضیحات</h2>";
echo "<p><strong>قبل از تصحیح:</strong> مبلغ بدون تبدیل ارسال می‌شد (مثلا 109,000 تومان = 109,000 ریال = 10,900 تومان در درگاه)</p>";
echo "<p><strong>بعد از تصحیح:</strong> مبلغ به ریال تبدیل می‌شود (مثلا 109,000 تومان × 10 = 1,090,000 ریال = 109,000 تومان در درگاه) ✓</p>";

echo "<hr>";
echo "<h2>مثال عملی</h2>";
echo "<p>کاربر میخواد <strong>100,000 تومان</strong> شارژ کنه:</p>";
echo "<ol>";
echo "<li>مبلغ ورودی: 100,000 تومان</li>";
echo "<li>مالیات 9%: 9,000 تومان</li>";
echo "<li>مبلغ نهایی: 109,000 تومان</li>";
echo "<li>تبدیل به ریال برای درگاه: 109,000 × 10 = <strong>1,090,000 ریال</strong></li>";
echo "<li>در درگاه پرداخت نمایش داده می‌شود: <strong>109,000 تومان</strong> ✓</li>";
echo "</ol>";

echo "<hr>";
echo "<h2>نکته مهم</h2>";
echo "<p style='background:#ffffcc;padding:10px;border-right:4px solid #ff9800;'>";
echo "درگاه‌های ایرانی (ZarinPal, Zibal, Vandar, PayPing) همگی به <strong>ریال</strong> کار می‌کنند، ";
echo "اما در صفحه پرداخت خودشان مبلغ را به <strong>تومان</strong> نمایش می‌دهند.";
echo "</p>";
