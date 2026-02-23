<?php
// Test if form submission works
echo "<h1>Test Form Submission</h1>";
echo "<pre>";
echo "REQUEST_METHOD: " . $_SERVER['REQUEST_METHOD'] . "\n";
echo "POST Data:\n";
print_r($_POST);
echo "\nGET Data:\n";
print_r($_GET);
echo "</pre>";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "<p style='color: green; font-size: 20px;'>✓ Form submitted successfully!</p>";
    echo "<p>Email: " . htmlspecialchars($_POST['email'] ?? 'not set') . "</p>";
    echo "<p>Password: " . (isset($_POST['password']) ? '***' : 'not set') . "</p>";
} else {
    echo "<p style='color: orange;'>Waiting for form submission...</p>";
}
?>

<hr>

<h2>Test Form</h2>
<form method="POST" action="test-form-action.php">
    <div style="margin-bottom: 10px;">
        <label>Email:</label><br>
        <input type="email" name="email" value="test@test.com" required style="padding: 5px; width: 300px;">
    </div>
    <div style="margin-bottom: 10px;">
        <label>Password:</label><br>
        <input type="password" name="password" value="12345678" required style="padding: 5px; width: 300px;">
    </div>
    <button type="submit" style="padding: 10px 20px; background: blue; color: white; border: none; cursor: pointer;">
        Submit Test
    </button>
</form>

<script>
document.querySelector('form').addEventListener('submit', function(e) {
    console.log('✓ Form submit event fired');
    console.log('Action:', this.action);
    console.log('Method:', this.method);
});
</script>
