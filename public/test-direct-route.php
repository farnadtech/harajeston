<?php
// Direct route test
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Create a test request
$request = Illuminate\Http\Request::create(
    '/admin/listings/17/activate',
    'POST',
    [],
    [],
    [],
    ['HTTP_ACCEPT' => 'application/json']
);

try {
    $response = $kernel->handle($request);
    
    echo "<h1>Route Test Result</h1>";
    echo "<p><strong>Status Code:</strong> " . $response->getStatusCode() . "</p>";
    echo "<p><strong>Content:</strong></p>";
    echo "<pre>" . $response->getContent() . "</pre>";
    
} catch (Exception $e) {
    echo "<h1>Error</h1>";
    echo "<p>" . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>
