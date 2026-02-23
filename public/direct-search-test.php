<?php
// Load Laravel
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

header('Content-Type: application/json; charset=utf-8');

try {
    // Create a request
    $request = Illuminate\Http\Request::create(
        '/api/listings/search?q=تست',
        'GET',
        [],
        [],
        [],
        ['HTTP_ACCEPT' => 'application/json']
    );
    
    // Handle the request
    $response = $kernel->handle($request);
    
    // Output the response
    echo $response->getContent();
    
    $kernel->terminate($request, $response);
} catch (Exception $e) {
    echo json_encode([
        'error' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);
}
