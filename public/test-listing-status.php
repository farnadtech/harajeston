<?php
// Test file to verify listing status system
require_once __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "<h1>Listing Status Test</h1>";
echo "<style>body { font-family: Arial; padding: 20px; } table { border-collapse: collapse; width: 100%; } th, td { border: 1px solid #ddd; padding: 8px; text-align: left; } th { background-color: #4CAF50; color: white; }</style>";

// Get all listings
$listings = \App\Models\Listing::with('seller')->get();

echo "<h2>All Listings (Total: " . $listings->count() . ")</h2>";
echo "<table>";
echo "<tr><th>ID</th><th>Title</th><th>Status</th><th>Seller</th><th>Type</th><th>Actions</th></tr>";

foreach ($listings as $listing) {
    $statusColor = match($listing->status) {
        'draft' => '#FFA500',
        'pending' => '#FFD700',
        'active' => '#4CAF50',
        'suspended' => '#FF6347',
        'rejected' => '#DC143C',
        default => '#808080'
    };
    
    echo "<tr>";
    echo "<td>{$listing->id}</td>";
    echo "<td>{$listing->title}</td>";
    echo "<td style='background-color: {$statusColor}; color: white; font-weight: bold;'>{$listing->status}</td>";
    echo "<td>{$listing->seller->name}</td>";
    echo "<td>{$listing->type}</td>";
    echo "<td>";
    
    // Show appropriate action buttons based on status
    if ($listing->status === 'draft') {
        echo "<a href='/admin/listings/{$listing->id}/approve' style='color: green;'>✓ Approve</a> | ";
        echo "<a href='/admin/listings/{$listing->id}/reject' style='color: red;'>✗ Reject</a>";
    } elseif ($listing->status === 'pending') {
        echo "<a href='/admin/listings/{$listing->id}/activate' style='color: green;'>▶ Activate</a>";
    } elseif ($listing->status === 'active') {
        echo "<a href='/admin/listings/{$listing->id}/suspend' style='color: orange;'>⏸ Suspend</a>";
    } elseif ($listing->status === 'suspended') {
        echo "<a href='/admin/listings/{$listing->id}/activate' style='color: green;'>▶ Activate</a>";
    }
    
    echo "</td>";
    echo "</tr>";
}

echo "</table>";

echo "<h2>Status Counts</h2>";
echo "<ul>";
echo "<li>Draft: " . \App\Models\Listing::where('status', 'draft')->count() . "</li>";
echo "<li>Pending: " . \App\Models\Listing::where('status', 'pending')->count() . "</li>";
echo "<li>Active: " . \App\Models\Listing::where('status', 'active')->count() . "</li>";
echo "<li>Suspended: " . \App\Models\Listing::where('status', 'suspended')->count() . "</li>";
echo "<li>Rejected: " . \App\Models\Listing::where('status', 'rejected')->count() . "</li>";
echo "</ul>";

echo "<h2>Test URLs</h2>";
echo "<p>Base URL: " . url('/') . "</p>";
echo "<p>Admin Listings: <a href='" . url('/admin/listings') . "'>" . url('/admin/listings') . "</a></p>";

// Test a specific listing
$testListing = $listings->first();
if ($testListing) {
    echo "<h3>Test Listing #{$testListing->id}</h3>";
    echo "<ul>";
    echo "<li>Approve URL: <a href='" . url("/admin/listings/{$testListing->id}/approve") . "'>" . url("/admin/listings/{$testListing->id}/approve") . "</a></li>";
    echo "<li>Reject URL: <a href='" . url("/admin/listings/{$testListing->id}/reject") . "'>" . url("/admin/listings/{$testListing->id}/reject") . "</a></li>";
    echo "<li>Activate URL: <a href='" . url("/admin/listings/{$testListing->id}/activate") . "'>" . url("/admin/listings/{$testListing->id}/activate") . "</a></li>";
    echo "<li>Suspend URL: <a href='" . url("/admin/listings/{$testListing->id}/suspend") . "'>" . url("/admin/listings/{$testListing->id}/suspend") . "</a></li>";
    echo "</ul>";
}
