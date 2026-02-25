<!DOCTYPE html>
<html>
<head>
    <title>Test Livewire</title>
    <script src="http://localhost/haraj/public/livewire/livewire.js" data-turbo-eval="false" data-turbolinks-eval="false"></script>
</head>
<body>
    <h1>Test Livewire Component</h1>
    
    <div>
        <?php
        require_once __DIR__ . '/../vendor/autoload.php';
        $app = require_once __DIR__ . '/../bootstrap/app.php';
        $kernel = $app->make('Illuminate\Contracts\Http\Kernel');
        $request = Illuminate\Http\Request::capture();
        $response = $kernel->handle($request);
        
        // Get a listing
        $listing = \App\Models\Listing::where('status', 'active')->first();
        
        if ($listing) {
            echo \Livewire\Livewire::mount('auction-bidding', ['listing' => $listing])->html();
        }
        ?>
    </div>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof Livewire !== 'undefined') {
                console.log('Livewire loaded successfully!');
                Livewire.start();
            } else {
                console.error('Livewire NOT loaded!');
            }
        });
    </script>
</body>
</html>
